<?php

/* @var $view \Nethgui\Renderer\Xhtml */

$view->rejectFlag($view::INSET_FORM);

$actionUrl = $view->getModuleUrl();
echo "<form action=\"{$actionUrl}\" method=\"post\" enctype=\"multipart/form-data\">";

include 'WizHeader.php';

echo $view->textLabel('InternetWarning')
    ->setAttribute('tag', 'div')
    ->setAttribute('escapeHtml', FALSE)
    ->setAttribute('class', 'internet')
;


$idArc = $view->getUniqueId('arc');

echo "<div class=\"labeled-control label-above\"><label for=\"{$idArc}\">" . \htmlspecialchars($T('UploadArc_label')) . "</label><input type=\"file\" name=\"arc\" id=\"{$idArc}\" /></div>";

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