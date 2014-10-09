<?php

echo $view->header()->setAttribute('template', $T('backup_config_Header'));

echo "<div>".$T('current_backup_label').":</div>";
echo "<dl class='rlc_module'>";
echo "<dt>".$T('date_label')."</dt><dd>".$view->textLabel('date')."</dd>";
echo "<dt>".$T('size_label')."</dt><dd>".$view->textLabel('size')."</dd>";
echo "</dl>";

echo "<div id='bc_module_warning' class='ui-state-highlight'><span class='ui-icon ui-icon-info'></span>".$T('backup_config_label')."</div>";

echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
;


$view->includeCSS("
  #bc_module_warning {
     margin-bottom: 8px;
     padding: 8px;
  }

  #bc_module_warning .ui-icon {
     float: left;
     margin-right: 3px;
  }
");

