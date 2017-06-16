<?php

/* @var $view \Nethgui\Renderer\Xhtml */
$view->rejectFlag($view::INSET_FORM);
$idArc = $view->getUniqueId('arc');
$uploadWidget = "<div class=\"labeled-control label-above\"><label for=\"{$idArc}\">" . \htmlspecialchars($T('UploadArc_label')) . "</label><input type=\"file\" name=\"arc\" id=\"{$idArc}\" /></div>";
$actionUrl = $view->getModuleUrl();

echo "<form action=\"{$actionUrl}\" method=\"post\" enctype=\"multipart/form-data\">";
include 'WizHeader.php';

echo $view->fieldsetSwitch('RestoreConfigStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->textLabel('InternetWarning')
        ->setAttribute('tag', 'div')
        ->setAttribute('escapeHtml', FALSE)
        ->setAttribute('class', 'internet'))
    ->insert($view->literal($uploadWidget))
;

include 'WizFooter.php';
echo "</form>";

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