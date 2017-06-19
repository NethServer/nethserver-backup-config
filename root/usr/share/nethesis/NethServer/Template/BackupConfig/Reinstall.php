<?php

$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('Reinstall_Header'));

echo '<p>' . $T('Reinstall_Description') . '</p>';

echo $view->buttonList()
    ->insert($view->button('Reinstall', $view::BUTTON_SUBMIT))
    ->insert($view->button('Back', $view::BUTTON_CANCEL))
;
