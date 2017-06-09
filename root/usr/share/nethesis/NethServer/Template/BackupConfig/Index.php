<?php

/* @var $view \Nethgui\Renderer\Xhtml */
echo $view->header()->setAttribute('template', $T('Index_Header'));

require __DIR__ . '/../../../Nethgui/Template/Table/Read.php';