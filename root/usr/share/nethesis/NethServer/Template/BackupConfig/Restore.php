<?php

$view->includeCSS("
  dl.rlc_module {
     margin-bottom: 20px;
  }

  dl.rlc_module {
    padding: 0.5em;
  }
  .rlc_module dt {
    float: left;
    clear: left;
    font-weight: bold;
    margin-right: 5px;
  }
  .rlc_module dt:after {
    content: \":\";
  }
  .rlc_module dd {
  }
");

echo $view->header('RLC')->setAttribute('template', $T('RLC_header'));

echo "<div>".$T('current_backup_label').":</div>";
echo "<dl class='rlc_module'>";
echo "<dt>".$T('date_label')."</dt><dd>".$view->textLabel('date')."</dd>"; 
echo "<dt>".$T('size_label')."</dt><dd>".$view->textLabel('size')."</dd>"; 
echo "</dl>";

echo $view->panel()
    ->insert($view->textLabel('SameHardware')->setAttribute('template', $T('SameHardware_label')))
    ->insert($view->radioButton('SameHardware', '1'))
    ->insert($view->radioButton('SameHardware', '0'));

echo $view->buttonList()
    ->insert($view->button('Restore', $view::BUTTON_SUBMIT))
    ->insert($view->button('Help', $view::BUTTON_HELP))
;



