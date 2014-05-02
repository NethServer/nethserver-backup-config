<?php

$view->requireFlag($view::INSET_DIALOG);

echo $view->header()->setAttribute('template', $T('confirm_backup_Header'));

echo "<div>".$T('confirm_backup_label')."</div>";

echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
    ->insert($view->button('Cancel', $view::BUTTON_CANCEL))
;

