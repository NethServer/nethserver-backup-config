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

namespace NethServer\Module;

class BackupConfig extends \Nethgui\Controller\TableController implements \Nethgui\Component\DependencyConsumer
{
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $base)
    {
        return new \NethServer\Tool\CustomModuleAttributesProvider($base, array(
            'category' => 'Configuration')
        );
    }

    public function initialize()
    {
        $columns = array(
             'id',
             'type',
             'description',
             'timestamp',
             'Actions',
         );

         $platform = $this->getPlatform();
         $this->setTableAdapter(new BackupConfig\BackupHistoryAdapter($this->getPlatform()))
            ->setReadAction(new BackupConfig\Index())
            ->setColumns($columns)
            ->addTableAction(new BackupConfig\Backup())
            ->addTableAction(new BackupConfig\Upload())
            ->addTableAction(new BackupConfig\Configure())
            ->addTableAction(new \Nethgui\Controller\Table\Help())
            ->addRowAction(new BackupConfig\Restore())
            ->addRowAction(new BackupConfig\Download())
            ->addRowAction(new BackupConfig\Delete('delete'))
            ->addChild(new BackupConfig\Reinstall())
        ;

        parent::initialize();
    }

    public function setUserNotifications(\Nethgui\Model\UserNotifications $n)
    {
        $this->notifications = $n;
        return $this;
    }

    public function setSystemTasks(\Nethgui\Model\SystemTasks $t)
    {
        $this->systemTasks = $t;
        return $this;
    }

    public function getDependencySetters()
    {
        return array(
            'UserNotifications' => array($this, 'setUserNotifications'),
            'SystemTasks' => array($this, 'setSystemTasks'),
        );
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $this->notifications->defineTemplate('adminTodo', \NethServer\Module\AdminTodo::TEMPLATE, 'bg-yellow');
        if($this->getRequest()->hasParameter('restoreSuccess')) {
            $this->notifications->message($view->translate('restoreSuccess_notification'));
            $view->getCommandList()->show();
            $view->getCommandList()->sendQuery($view->getModuleUrl('/AdminTodo?notifications'));
        } elseif ($this->getRequest()->hasParameter('restoreFailure')) {
            $taskStatus = $this->systemTasks->getTaskStatus($this->getRequest()->getParameter('taskId'));
            $data = \Nethgui\Module\Tracker::findFailures($taskStatus);
            $this->notifications->trackerError($data);
        } elseif($this->getRequest()->isValidated()) {
            $view->getCommandList()->show();
        }
    }
}