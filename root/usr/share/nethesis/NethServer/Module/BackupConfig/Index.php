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

class Index extends \Nethgui\Controller\Table\Read
{
    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);
        $view->setTemplate('NethServer\Template\BackupConfig\Index');
    }

    public function prepareViewForColumnType(\Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $icon = '';
        if($values['type'] === 'upload') {
            $icon = 'fa-upload';
            $title = $view->translate('Type_upload_label');
        } elseif ($values['type'] === 'snapshot') {
            $icon = 'fa-camera';
            $title = $view->translate('Type_snapshot_label');
        } elseif ($values['type'] === 'cron') {
            $icon = 'fa-clock-o';
            $title = $view->translate('Type_scheduled_label');
        } elseif ($values['type'] === 'cloud') {
            $icon = 'fa-cloud';
            $title = $view->translate('Type_cloud_label');
        }
        if($icon) {
            return sprintf('<i class="fa %s" aria-disabled="true" title="%s"></i>', $icon, htmlspecialchars($title));
        }
        return '';
    }

    public function prepareViewForColumnTimestamp(\Nethgui\View\ViewInterface $view, $key, $values, &$rowMetadata)
    {
        $dateFormat = 'Y-m-d H:i T';
        return date($dateFormat, $values['original_ts']);
    }

}