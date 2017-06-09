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

class Download extends \Nethgui\Controller\Table\RowAbstractAction
{
    public function initialize()
    {
        $parameterSchema = array(
            array('id', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::KEY),
        );
        $this->setSchema($parameterSchema);
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
        if( ! $this->getRequest()->isValidated()) {
            parent::prepareView($view);
            return;
        }

        if(! $this->getRequest()->hasParameter('download')) {
            $view->getCommandList('/Main')->sendQuery($view->getModuleUrl(sprintf('/BackupConfig/Download/%s.tar.xz?BackupConfig[Download][download]=1', $this->parameters['id'])));
            return;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        $file = tempnam('/tmp', 'backup-config-history-');
        $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history pull -i ${1} -f ${2}', array($this->parameters['id'], $file));
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="backup-config-' . $this->parameters['id'] . '.tar.xz"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            unlink($file);
            exit;
        }
    }

    public function nextPath()
    {
        return 'Index';
    }
}
