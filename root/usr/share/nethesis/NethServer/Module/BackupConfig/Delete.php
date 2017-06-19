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

class Delete extends \Nethgui\Controller\Table\Modify
{
    public function initialize()
    {
        $parameterSchema = array(
            array('id', Validate::ANYTHING, \Nethgui\Controller\Table\Modify::KEY),
        );
        $this->setSchema($parameterSchema);
        $this->setViewTemplate('Nethgui\Template\Table\Delete');
        parent::initialize();
    }

    public function process()
    {
        if ($this->getRequest()->isMutation()) {
            $this->getPlatform()->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history drop -i ${@}', array($this->parameters['id']));
            $this->getParent()->getAdapter()->flush();
        }
    }
}
