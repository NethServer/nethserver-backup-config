<?php

echo $view->header('name')->setAttribute('template', $T('Restore_Header'));

$readOnly = $view::STATE_DISABLED | $view::STATE_READONLY;

echo $view->textInput('description', $readOnly);
echo $view->textInput('size', $readOnly);
echo $view->textInput('type', $readOnly);
echo $view->textInput('original_ts', $readOnly);
echo $view->textInput('ProductName', $readOnly);
echo $view->textInput('Version', $readOnly);
echo $view->textInput('Release', $readOnly);


echo $view->buttonList()
    ->insert($view->button('Restore', $view::BUTTON_SUBMIT))
    ->insert($view->button('Back', $view::BUTTON_CANCEL))
    ->insert($view->button('Help', $view::BUTTON_HELP))
;



