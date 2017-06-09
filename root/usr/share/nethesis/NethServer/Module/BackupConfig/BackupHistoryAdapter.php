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

class BackupHistoryAdapter extends \Nethgui\Adapter\LazyLoaderAdapter
{
    private $platform;

    public function __construct(\Nethgui\System\PlatformInterface $platform)
    {
        $this->platform = $platform;
        parent::__construct(array($this, 'historyLoader'));
    }
    
    public function historyLoader()
    {
        $loader = new \ArrayObject();
        $items = json_decode($this->platform->exec('/usr/bin/sudo /usr/libexec/nethserver/backup-config-history list')->getOutput(), TRUE);
        foreach ($items as $row) {
            $loader[$row['id']] = $row;
        }
        return $loader;
    }
    
    public function flush()
    {
        $this->data = NULL;
        return $this;
    }
}