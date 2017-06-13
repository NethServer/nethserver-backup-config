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

class Restore extends \Nethgui\Controller\AbstractController
{
    public $wizardPosition = 30;
    protected function initializeAttributes(\Nethgui\Module\ModuleAttributesInterface $attributes) {
        return new \NethServer\Tool\CustomModuleAttributesProvider($attributes, array('languageCatalog' => 'NethServer_Module_BackupConfig'));
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        if( ! $this->getRequest()->isMutation() || ! $_FILES['arc']['tmp_name']) {
            return;
        }

        $arcValidator = $this->createValidator()->platform('config-backup-upload');

        if( ! $arcValidator->evaluate($_FILES['arc']['tmp_name'])) {
            $report->addValidationError($this, 'UploadArc', $arcValidator);
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

    public function nextPath() {
        if($_FILES['arc']['tmp_name']) {
            return 'Review';
        } elseif ($this->getRequest()->hasParameter('skip') || $this->getRequest()->isMutation()) {
            $successor = $this->getParent()->getSuccessor($this);
            return $successor ? $successor->getIdentifier() : 'Review';
        }
        return parent::nextPath();
    }

}
