<?php

/* @var $view \Nethgui\Renderer\Xhtml */
$view->requireFlag($view::FORM_ENC_MULTIPART);

include 'WizHeader.php';

echo $view->fieldsetSwitch('RestoreConfigStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->textLabel('InternetWarning')
        ->setAttribute('tag', 'div')
        ->setAttribute('escapeHtml', FALSE)
        ->setAttribute('class', 'internet'))
    ->insert($view->fileUpload('UploadArc')->setAttribute('htmlName', 'arc'))
    ->insert($view->checkBox('InstallPackages', 'yes')->setAttribute('uncheckedValue', 'no'))
;

include 'WizFooter.php';

$view->includeCss("

.internet {
    background-color: #FFB600;
    padding: .8em;
    display: flex;
    margin-bottom: 1em;
}

.internet .fa {
     align-self: center;
}

.internet .text {
    flex-grow: 1; margin-left: 0.5em;
}

.internet:empty {
    display: none;
}
");