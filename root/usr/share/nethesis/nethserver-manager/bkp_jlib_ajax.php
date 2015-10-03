<?php

// Library contains ajax calls for actions
// usage:
// lib_ajax.php?act=x&p1=y&p2=z...&p9=
// where x = action and p1,p2.. are values
// example: ?act=add_user&p1=Wolf



// FUNCTIONS //
//-----------------------------------------------------------------------------------------------------
function clean_enter($string) {
	$string=strtr($string, array("\n" => '<br />', "\r\n" =>'<br />'));	
	return $string;
};
//-----------------------------------------------------------------------------------------------------
function cleanInput($input) {
	$search = array(
	    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
	    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
	    '@<style[^>]*?>.*?</style>@siU'    // Strip style tags properly
    	//,'@<![\s\S]*?--[ \t\n\r]*>@'     // --- ENABLED --- Strip multi-line comments
	);
    $output = preg_replace($search, '', $input);
    return clean_enter($output);
};
//-------------------------------------------------------------------------------------------------------------------------------------------//
function sanitize($input) {
	// global $connection;
	if (is_array($input)) {
		foreach($input as $var=>$val) {
			$output[$var] = sanitize($val);
		}
    } else {
		if (get_magic_quotes_gpc()) {
		    $input = stripslashes($input);
		}
		$input  = cleanInput($input);
		// $output = mysqli_real_escape_string($connection, $input);
    }
    return $input;
};
//-----------------------------------------------------------------------------------------------------

function format_bytes($size) {
	$units = array(' B', ' KiB', ' MiB', ' GiB', ' TiB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++)
		$size /= 1024;
	return round($size, 2) . $units[$i];
}

//-------------------------------------------------------------------------------------------------------------------------------------------//


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

//-------------------------------------------------------------------------------------------------------------------------------------------//

function get_file_extension($file_name) {
	return substr(strrchr($file_name,'.'),1);
}

//-------------------------------------------------------------------------------------------------------------------------------------------//
function delete_backup($file_name) {
	
	
	if (isset($file_name) and !empty($file_name) and (get_file_extension($file_name) == "xz")) 
			{
				// $this->getPlatform()->exec('/usr/bin/sudo /bin/rm -rf "/var/lib/nethserver/backup/'.$file_name.'"'); 
				$result=passthru('/usr/bin/sudo rm  /var/lib/nethserver/backup/'.$file_name.'', $out);
				if ($result) {
								
								return ("Deleted backup ". $file_name);
													} else {
															return "Error on delete: ". $file_name." Output: ".var_dump($out)." Error: ".var_dump($err);
															};
				} else {
						return "Error, file is not valid for removal";
						};
		
		/*
		if (unlink('backup/' . $file_name)) {
			return ("Deleted backup ". $file_name);
		} else {
			return "error on delete";
			};
		*/
				
		 
		
	
};
//-------------------------------------------------------------------------------------------------------------------------------------------//
function get_backup($backup_file) {
	
	if (!$backup_file) {
		 echo "File error!";
	} else {
		
		$path = "backup/";
		// change the path to fit your websites document structure
		$fullPath = $path . $backup_file;
		if ($fd = fopen($fullPath, "rb")) {
			header("Pragma: public");
			header("Expires: -1");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=" . basename($fullPath));
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($fullPath));
			ob_clean();
			flush();
			echo readfile("$fullPath");
		};
		fclose($fd);
		exit;
	};
};
//-------------------------------------------------------------------------------------------------------------------------------------------//
function restore_backup($backup_file) {
	
	if ((isset($backup_file)) && (!empty($backup_file))) {
		
		exec('/usr/bin/sudo /sbin/e-smith/restore-config '.$backup_file, array(), TRUE);
		
	};
};
//-------------------------------------------------------------------------------------------------------------------------------------------//

//// SRIPT LOGIC /////



$actiune="";
$p=array();
$json=array();
function get_parameters() {
	global $p,$json;
	for ($i=1; $i < 10 ; $i++) {
		if (isset($_GET['p'.$i])) {
			$p[$i]=sanitize($_GET['p'.$i]);
		}
		elseif (isset($_POST['p'.$i])) {
			$p[$i]=sanitize($_POST['p'.$i]); 
		}
	}
	if (isset($_GET['json'])) {
		$json=json_decode($_GET["json"], true);
	}
	elseif (isset($_POST['json'])) {
		$json=json_decode($_POST["json"], true);
	}
}

if ((isset($_GET['act']))&&(!empty($_GET['act']))) {
	$act=sanitize($_GET['act']);
	get_parameters();
} elseif ((isset($_POST['act']))&&(!empty($_POST['act']))) {
	$act=sanitize($_POST['act']);
	get_parameters();
} else {
	die();
		};





///------------------------------------------------------------------///
if ($act !="") {
	switch ($act) {
		
		case "menu_backup":
			echo menu_backup();
			break;
		
		
		case "backup_table":
			echo backup_table($p[1]);
			break; 
		
		case "delete_backup":
			echo delete_backup($p[1]);
			break;
		case "get_backup":
			echo get_backup($p[1]);
			break;
		case "restore_backup":
			echo restore_backup($p[1]);
			break;
		
		case "upd_table":
			echo upd_template($json);
			break;
	}; // end switch
} else {
	die();
};

