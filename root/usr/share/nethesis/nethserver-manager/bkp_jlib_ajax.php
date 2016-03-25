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
    	//,'@<![\s\S]*?--[ \t\n\r]*>@'     // --- DISABLED --- Strip multi-line comments
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


function get_filename_md5($filename){

if (file_exists($filename.".md5")) {

	$file_handle = fopen($filename.".md5", "r");
	$txt = fgets($file_handle);
	return substr( $txt, 0, strpos( $txt, ' ' ) );
	fclose($file_handle);
	} else {
			return " <strong> .MD5 file Not Available! </strong> ";
			};
}

//-------------------------------------------------------------------------------------------------------------------------------------------//

function calculate_md5($filename){

if (file_exists($filename)) {

	return md5_file( $filename );
	
	} else {
			return "N/A";
			};
}

//-------------------------------------------------------------------------------------------------------------------------------------------//

function table_backup() {
    
    $html = '        
        <div id="ui-table-contain" class="ui-widget">
            <table id="bkptable" class="ui-widget ui-widget-content">
            <thead>
                <tr class="ui-widget-header">
                    <th style="width: 35px;"> - </th>
                    <th >File Name</th>
                    <th style="width: 250px;" > MD5 from file </th>
                    <th style="width: 50px;" > MD5 check </th>
                    <th style="width: 66px;" > Size </th>
                    <th style="width: 73px;" > Delete </th>
                    <th style="width: 92px;" > Download </th>
                    <th style="width: 81px;" > Restore </th>
                </tr></thead><tbody>';

	$files=glob('backup/*.{xz}', GLOB_BRACE);
    arsort($files);     													//sort the array to display the latest backup first
    $i=0;
    foreach ($files as $value) {
        if (($value != ".") && ($value != "..") && ($value != ".htaccess") && (!is_dir('backup/'.$value))) {
			$i++;
			unset($md5_check, $md5_value);
			$md5_value=get_filename_md5($value);
			$md5_check=calculate_md5($value);
			
			if ($md5_value != $md5_check ) {$md5_check = " <i class=\"fa fa-exclamation-triangle md5_error\" style=\"color: #e60000; text-shadow: 1px 1px 0 #444; \"  title=\"Missing <strong>.MD5</strong> file for backup archive: </br>".str_replace("backup/","",$value).". </br>Or, content of.MD5 file <strong>does not match</strong> the MD5 checksum\"></i> "; } 
				else {$md5_check = " <i class=\"fa fa-check md5_ok\" style=\"color: #00cc33; text-shadow: 1px 1px 0 #444;\" title=\"MD5 checksum <strong>OK</strong>\"  ></i> "; }  
			
			$value= str_replace("backup/","",$value);
            $html .= '
                <tr id="' . $value . '">
                    <td style="text-align:center"> '. $i .'</td>
                    <td> ' . $value . '</td>
                    <td> ' . $md5_value . '</td>
                    <td style="text-align:center" > ' . $md5_check . '</td>
                    <td style="text-align:center" > ' . format_bytes(filesize('backup/' . $value)) . '</td>
                    <td style="text-align:center" ><span class="bkp_delete" > <i class="fa fa-trash-o"></i> Delete </span></td>
                    <td style="text-align:center" ><span class="bkp_download" > <i class="fa fa-download"></i> Download </span></td>
                    <td style="text-align:center" ><span class="bkp_restore" > <i class="fa fa-refresh"></i> Restore </span></td>
                </tr>';
        }
    }
    $html .= '
    </tbody></table></div>
    '; 
    return $html;
   
};

//-------------------------------------------------------------------------------------------------------------------------------------------//

function get_file_extension($file_name) {
	return substr(strrchr($file_name,'.'),1);
}

//-------------------------------------------------------------------------------------------------------------------------------------------//

function get_keep_value() {
		$out=array();
		$err=array();
		
		$command = "/usr/bin/sudo /sbin/e-smith/db configuration getprop backup-config keep_backups";
		$result=exec($command, $out, $err);
		if ($err == 0) {
						return $result;
								} else { return $err; };
		
}

//-------------------------------------------------------------------------------------------------------------------------------------------//

function set_keep_value($bkp_value) {
		$out=array();
		$err=array();
		
		$command = "/usr/bin/sudo /sbin/e-smith/db configuration setprop backup-config keep_backups ".$bkp_value;
		$result=exec($command, $out, $err);
		if ($err == 0) {
						return $result;
								} else { return $err; };
		
}
//-------------------------------------------------------------------------------------------------------------------------------------------//
function delete_backup($file_name) {
		$out=array();
		$err=array();
	
	if (isset($file_name) && !empty($file_name) && (get_file_extension($file_name) == "xz")) 
			{
				$command = '/usr/bin/sudo /sbin/e-smith/delete-config '.$file_name;
				$result=exec($command, $out, $err);
				
				if ($err == 0) {
								return ("Deleted file: ". $file_name);
								} else {
										
										
										return "</br> Error removing: ". $file_name." </br> Output: <pre>".print_r ($out)."</pre> </br> Error: <pre>".$err."</pre>";
										};
				} else {
						return "Error, file is not valid for removal";
						};
		
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
		$out=array();
		$err=array();
		unset($out, $err);
		
	if ((isset($backup_file)) && (!empty($backup_file)) && (get_file_extension($backup_file) == "xz")) {
		
		$command='/usr/bin/sudo /sbin/e-smith/restore-config '.$backup_file;
		$rezult = exec($command, $out, $err);
		
	};
if ($err == 0) {return "</br>Restoration of: ".$backup_file." Done!" ; } 
	else {
			return "</br>Restore: ".$backup_file."</br> Out: <pre>".print_r($out)."</pre></br> Error: <pre>".print_r($err)."</pre></br>";
			};
	
	
};
//-------------------------------------------------------------------------------------------------------------------------------------------//


function upload_backup(){
if(isset($_POST['submit_file']))
	{
	$out=array();
	$err=array();
	
	$check=get_file_extension($_FILES['upload_file']['name']);
	if ( $check == "xz") { 
							$uploadfile=$_FILES["upload_file"]["tmp_name"];
													
							$target=sanitize($_FILES['upload_file']['name']);
							
							
							if (file_exists('/../../../..'.$uploadfile)){
								$command='/usr/bin/sudo /sbin/e-smith/upload-config '.$uploadfile.' '.$target;
								$rezult = exec($command, $out, $err);
								$msj = 'Completed: Backup '.$_FILES["upload_file"]["name"].' uploaded <br/> '.print_r($out).' <br/>'.print_r($err);
								
								} else 
										{ 
										 $msj = "Error: TEMP File not found !". '/../../../..'.$uploadfile; 
											};
							
							} 
							else { 
									$msj = "Error: Filetype not allowed (".$check.")";
									};
	
	
  }else {
			$msj = "Error: Not posible to upload file";
		}; 
  
  return $msj;
};
	

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
		
		case "bkp_table":
			echo table_backup();
			break;
		
		case "keep_value":
			echo get_keep_value();
			break; 
		
		case "set_keep_value":
			echo set_keep_value($p[1]);
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
		
		case "load":
			echo upload_backup();
			break;	
			
	}; // end switch
} else {
	die();
};

