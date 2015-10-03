<?php
$view->useFile('js/js_fct.js');


function format_bytes($size) {
	$units = array(' B', ' KiB', ' MiB', ' GiB', ' TiB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++)
		$size /= 1024;
	return round($size, 2) . $units[$i];
}


function menu_backup() {
    
    $html = '        
        <div id="ui-table-contain" class="ui-widget">
            <table id="dbtable" class="ui-widget ui-widget-content">
            <thead>
                <tr class="ui-widget-header">
                    <td>File Name</td>
                    <td style="width: 66px;">Size</td>
                    <td style="width: 73px;">Delete</td>
                    <td style="width: 92px;">Download</td>
                    <td style="width: 81px;">Restore</td>
                </tr></thead><tbody>';

   // $files = scandir('backup', 1);
    $files = glob('backup/*.{xz}', GLOB_BRACE);
    foreach ($files as $value) {
        if (($value != ".") && ($value != "..") && ($value != ".htaccess") && (!is_dir('backup/'.$value))) {
			$value= str_replace("backup/","",$value);
            $html .= '
                <tr id="' . $value . '">
                    <td>' . $value . '</td>
                    <td>' . format_bytes(filesize('backup/' . $value)) . '</td>
                    <td ><span class="link_img delete" ><img src="/images/trash.png" align="absmiddle" border="0"/> Delete</span></td>
                    <td ><span class="link_img download"><img src="/images/download.png" align="absmiddle" border="0"/> Download</span></td>
                    <td ><span class="link_img restore"><img src="/images/restore.png" align="absmiddle" border="0"/> Restore</span></td>
                </tr>';
        }
    }
    $html .= '
    </tbody></table></div>
    '; /*removed snipped*/ 
    return $html;
    
// removed '<script type="text/javascript" src="js/admin.js"></script>'    
    
};


echo $view->header()->setAttribute('template', $T('backup_config_Header'));
echo "<div>".$T('current_backup_label').":</div>";

echo "<div id=\"table_holder\">".menu_backup()."</div>";
echo "<dl class='rlc_module'>";
echo "</dl>";

echo "<div id='bc_module_warning' class='ui-state-highlight'><span class='ui-icon ui-icon-info'></span>".$T('backup_config_label')."</div>";
echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
    ->insert($view->button('Help', $view::BUTTON_HELP))
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
  
  
.link_img{
	opacity:0.6;
}

.link_img:hover{
	opacity:1;
	cursor: pointer;
}
  

/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
  
  
");

// DEBUG // 
$file_1name='backup-config_2015-09-20_13-46-13.tar.xz';
$result=exec('sudo rm -f /var/lib/nethserver/backup/'.$file_1name.'');
echo $result;

function get_file_extension($file_name) {
	return substr(strrchr($file_name,'.'),1);
}


echo "</br>-----------------------------------------------</br>";
echo $moduleUrl = $view->getModuleUrl();
echo "</br>";
echo $moduleUrl1 = json_encode($view->getModuleUrl());
echo "</br>";
echo basename(__DIR__) ;
echo "</br>";

echo "</br>-----------------------------------------------</br>";


 $filess = scandir("backup/", 1);
    foreach ($filess as $values) {
				
		if (get_file_extension($values) == 'xz' ) {
		echo $values."</br>";
		};
		
	};

echo "</br>-----------------------------------------------</br>";

// END DEBUG //

$view->includeJavascript("
(function ( $ ) {
  
  
  
  $(document).ready(function() {
    $( \".delete\" ).on({ click: function() {
		var fname = $(this).parent().parent().attr('id');
		alert (fname);
		$.post( \"/bkp_jlib_ajax.php\", {act:\"delete_backup\", p1:fname}, function( data ) {
				alert(data);
			});
		
		jqui_confirm(\"Delete back-up ?\", \"Selected back-up will be deleted, all containing data will be lost!<br>\" + \"Are you sure you want to delete: <br>\"+ fname +\" ?\", \"Delete\" );
	}});

	$( \".download\" ).on({ click: function() {
		window.location='/bkp_jlib_ajax.php?act=get_backup&p1='+$(this).parent().parent().attr('id');
	}});

	$( \".restore\" ).on({ click: function() {
		var fname = $(this).parent().parent().attr('id');
		fct = function() {
			$.post( \"/bkp_jlib_ajax.php\", {act:\"restore_backup\", p1:fname}, function( data ) {
				validate_response(data, \"menu_backup\");
			});
		};
		jqui_confirm(\"Restore back-up ?\", \"Current data will be overwritten, all current data will be lost!<br>\" + \"Are you sure you want to restore: <br>\"+ fname +\" ?\", \"Restore\", fct );
	}});
	
	  
  });
})( jQuery);
");






