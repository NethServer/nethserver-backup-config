<?php

$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('confirm_restore_Header'));

echo "<div>".$T('confirm_restore_label')."</div>";

echo $view->buttonList()
    ->insert($view->button('Restore', $view::BUTTON_SUBMIT))
    ->insert($view->button('Cancel', $view::BUTTON_CANCEL))
;

