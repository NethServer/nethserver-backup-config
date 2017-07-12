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


class Backup extends \Nethgui\Controller\Table\AbstractAction implements \Nethgui\Component\DependencyConsumer
{
    private $error;

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('Description', $this->createValidator()->maxLength(32));
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $process = $this->getPlatform()->exec('/usr/bin/sudo /sbin/e-smith/backup-config -f');
            if($process->getExitCode() === 0) {
                $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history push -t snapshot -d ${1}', array($this->parameters['Description']));
            } else {
                $this->error = 'Command_backupconfig_failed';
            }
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        if($this->error) {
            $this->notifications->error($view->translate($this->error));
        }
    }

    public function setUserNotifications(\Nethgui\Model\UserNotifications $n)
    {
        $this->notifications = $n;
        return $this;
    }

    public function getDependencySetters()
    {
        return array('UserNotifications' => array($this, 'setUserNotifications'));
    }

}