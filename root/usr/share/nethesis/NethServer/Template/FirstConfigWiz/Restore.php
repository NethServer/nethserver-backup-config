<?php

/* @var $view \Nethgui\Renderer\Xhtml */

$view->rejectFlag($view::INSET_FORM);

$actionUrl = $view->getModuleUrl();
echo "<form action=\"{$actionUrl}\" method=\"post\" enctype=\"multipart/form-data\">";

include 'WizHeader.php';

$idArc = $view->getUniqueId('arc');

echo "<div class=\"labeled-control label-above\"><label for=\"{$idArc}\">" . \htmlspecialchars($T('UploadArc_label')) . "</label><input type=\"file\" name=\"arc\" id=\"{$idArc}\" /></div>";

include 'WizFooter.php';

echo "</form>";