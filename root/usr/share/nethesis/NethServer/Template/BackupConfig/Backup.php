<?php

echo $view->header()->setAttribute('template', $T('backup_config_Header'));
$view->useFile('js/bootstrap.min.js');
$view->useFile('js/bootstrap-table-all.min.js');



echo "<div>".$T('current_backup_label').":</div>";
echo "<dl class='rlc_module'>";
echo "<dt>".$T('date_label')."</dt><dd>".$view->textLabel('date')."</dd>";
echo "<dt>".$T('size_label')."</dt><dd>".$view->textLabel('size')."</dd>";
echo "</dl>";

echo "<div><table id=\"table_backup\"></table></div>";

echo "<div id='bc_module_warning' class='ui-state-highlight'><span class='ui-icon ui-icon-info'></span>".$T('backup_config_label')."</div>";

echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
    ->insert($view->button('Help', $view::BUTTON_HELP))
;


$view->includeJavascript("
(function ( $ ) {
  function loadPage() {
		 
	/*
	* Load a custom CSS file
	*/	 
	 loadCSS = function(href) {
								var cssLink = $(\"<link rel='stylesheet' type='text/css' href='\"+href+\"'>\");
								$(\"head\").append(cssLink); 
								};
		
		
		
		
	
 
		// load the css file 
		loadCSS('css/bootstrap.min.css');
		loadCSS('css/bootstrap-table.min.css');
 
 
		//add data to the bkp table
		
		/*
		Prepare the Json for the table
		*/
	
		$('#table_backup').bootstrapTable({
		".$view->textLabel('backup_table')."
		});
 
		
	} // end function loadPage 
  
  $(document).ready(function() {    
   
   // call the functions inside loadPage at doc.ready
   
   loadPage(); 
   
   });
  
	})( jQuery);
");




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

