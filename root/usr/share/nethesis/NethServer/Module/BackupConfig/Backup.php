<?php
namespace NethServer\Module\BackupConfig;
/*
 * Copyright (C) 2012 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Force configuration backup
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class Backup extends \Nethgui\Controller\AbstractController 
{

    private $backup;

    public function process()
    {
	parent::process();
        if ( ! $this->getRequest()->isMutation()) {
            return;
        } else {
            $this->getPlatform()->exec('/usr/bin/sudo /sbin/e-smith/backup-config');
        }
    }
    public function nextPath()
    {
        return $this->getRequest()->isMutation() ? 'Backup' : parent::nextPath();
    }

    private function getBackupInfo()
    {
        return json_decode($this->getPlatform()->exec('/usr/libexec/nethserver/backup-config-info')->getOutput(), TRUE);
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
	parent::prepareView($view);
        if (!$this->backup) {
            $this->backup = $this->getBackupInfo();
        }
        if (isset($this->backup['size'])) {
            $view['size'] = round($this->backup['size'] / 1024,2).' KB';
            $view['date'] = date("o-m-d G:i", $this->backup['date']);
        } else {
            $view['size'] = '-';
            $view['date'] = '-';
        }
    }

}
