<?php

/*
 * Copyright (C) 2017 Nethesis S.r.l.
 * http://www.nethesis.it - nethserver@nethesis.it
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License,
 * or any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see COPYING.
 */

namespace NethServer\Module\BackupConfig;
use Nethgui\System\PlatformInterface as Validate;

class Restore extends \Nethgui\Controller\Table\RowAbstractAction
{
    public function initialize()
    {
        $parameterSchema = array(
            array('id', FALSE, \Nethgui\Controller\Table\Modify::KEY),
            array('description', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('type', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('size', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('original_ts', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('disk_ts', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('push_ts', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('ProductName', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('Version', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
            array('Release', FALSE, \Nethgui\Controller\Table\Modify::FIELD),
        );
        $this->setSchema($parameterSchema);
        $this->declareParameter('InstallPackages', Validate::YES_NO);
        parent::initialize();
    }

    public function bind(\Nethgui\Controller\RequestInterface $request)
    {
        $keyValue = implode('/', $request->getPath());
        $this->getAdapter()->setKeyValue(basename($keyValue, '.tar.xz'));
        parent::bind($request);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if( ! $this->getRequest()->isValidated()) {
            return;
        }
        $view['InstallPackages'] = 'yes';
        $dateFormat = 'Y-m-d H:i:s T';
        $view['disk_ts'] = date($dateFormat, $view['disk_ts']);
        $view['original_ts'] = date($dateFormat, $view['original_ts']);
        $view['push_ts'] = date($dateFormat, $view['push_ts']);
        $view['name'] = $view['id'];
        $view['type'] = ucfirst($view['type']);
        if($view['description']) {
            $view['name'] = sprintf('%s - %s', $view['id'], $view['description']);
        }
        $view['size'] = $this->getHumanFilesize($view['size']);
        if($this->getRequest()->isMutation()) {
            $this->getPlatform()->setDetachedProcessCondition('success', array(
                'location' => array(
                    'url' => $view->getModuleUrl('/BackupConfig?restoreSuccess'),
                    'freeze' => TRUE,
            )));
            $this->getPlatform()->setDetachedProcessCondition('failure', array(
                'location' => array(
                    'url' => $view->getModuleUrl('/BackupConfig?restoreFailure&taskId={taskId}'),
                    'freeze' => TRUE,
            )));
        }
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $process = $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history pull -i ${1}', array($this->parameters['id']));
            if($process->getExitCode() === 0) {
                $args = array('--mask-unit', 'httpd-admin');
                if($this->parameters['InstallPackages'] === 'no') {
                    $args[] = '--no-reinstall';
                }
                $this->getPlatform()->exec('/usr/bin/sudo /sbin/e-smith/restore-config ${@}', $args, TRUE);
            }
        }
    }

    function getHumanFilesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f %sB", $bytes / pow(1024, $factor), @$sz[$factor]);
    }
}
