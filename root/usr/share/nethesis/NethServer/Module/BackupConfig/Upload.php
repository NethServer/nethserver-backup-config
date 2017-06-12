<?php

namespace NethServer\Module\BackupConfig;

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

 use Nethgui\System\PlatformInterface as Validate;

class Upload extends \Nethgui\Controller\Table\AbstractAction
{
    public function initialize()
    {
        parent::initialize();
        $this->declareParameter('id', FALSE);
        $this->declareParameter('Description', $this->createValidator()->maxLength(32));
    }
    
    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        parent::validate($report);

        if( ! $this->getRequest()->isMutation()) {
            return;
        }

        $arcValidator = $this->createValidator()->platform('config-backup-upload');

        if( ! $arcValidator->evaluate($_FILES['arc']['tmp_name'])) {
            $report->addValidationError($this, 'UploadArc', $arcValidator);
        }

    }

    public function process()
    {
        if ( ! $this->getRequest()->isMutation()) {
            return;
        }
        $process = $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history push -t upload -f ${1} -d ${2}', array($_FILES['arc']['tmp_name'], $this->parameters['Description']));
        $this->getParent()->getAdapter()->flush();
    }

    public function nextPath()
    {
        return $this->getRequest()->isMutation() ? 'read' : $this->getIdentifier();
    }
}