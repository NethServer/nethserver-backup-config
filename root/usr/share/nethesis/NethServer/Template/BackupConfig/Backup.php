<?php

$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('Backup_Header'));

echo $view->textInput('Description');

echo $view->buttonList()
    ->insert($view->button('Backup', $view::BUTTON_SUBMIT))
    ->insert($view->button('Back', $view::BUTTON_CANCEL))
;
