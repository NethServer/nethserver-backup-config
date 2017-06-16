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

namespace NethServer\Module\FirstConfigWiz;
use Nethgui\System\PlatformInterface as Validate;

class Restore extends \Nethgui\Controller\AbstractController implements \Nethgui\Component\DependencyConsumer
{
    public $wizardPosition = 30;
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes) {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array('languageCatalog' => 'NethServer_Module_BackupConfig'));
    }

    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('RestoreConfigStatus', Validate::SERVICESTATUS);
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        if( ! $this->getRequest()->isMutation() || $this->parameters['RestoreConfigStatus'] === 'disabled') {
            return;
        }

        $arcValidator = $this->createValidator()->platform('config-backup-upload');
        if( ! $arcValidator->evaluate($_FILES['arc']['tmp_name'])) {
            $report->addValidationError($this, 'UploadValidatorWidget', $arcValidator);
        }

    }

    public function process()
    {
        parent::process();
        if($this->getRequest()->isMutation() && $_FILES['arc']['tmp_name']) {
            $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history push -t upload -f ${1} -d ${2}', array($_FILES['arc']['tmp_name'], 'First configuration wizard'));
            $this->getParent()->storeAction(array(
                'message' => array(
                    'module' => $this->getIdentifier(),
                    'id' => 'Restore_Action',
                    'args' => array()
                ),
                'events' => array('nethserver-backup-config-restorewizard')
            ));
        }
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view['InternetWarning'] = '';
        if ($view->getTargetFormat() === $view::TARGET_JSON) {
            $exitCode = $this->getPlatform()->exec('curl --connect-timeout 4  http://mirrorlist.centos.org')->getExitCode();
            if($exitCode === 0) {
                // pass
            } else {
                $icon = '<i class="fa fa-2x fa-exclamation-triangle" aria-hidden="true"></i>';
                $view['InternetWarning'] = $icon . '<span class="text">' . htmlspecialchars($view->translate("InternetConnectionNotAvailable")) . '</span>';
            }
        }

    }

    public function nextPath() {
        if($_FILES['arc']['tmp_name']) {
            return 'Review';
        } elseif ($this->getRequest()->hasParameter('skip') || $this->getRequest()->isMutation()) {
            $successor = $this->getParent()->getSuccessor($this);
            return $successor ? $successor->getIdentifier() : 'Review';
        }
        return parent::nextPath();
    }

    public function setUserNotifications(\Nethgui\Model\UserNotifications $n)
    {
        $this->notifications = $n;
        return $this;
    }

    public function getDependencySetters()
    {
        return array(
            'UserNotifications' => array($this, 'setUserNotifications'),
        );
    }
}
