<?php
$view->includeCSS("
  #bc_module_warning {
     margin-bottom: 8px;
     padding: 8px;
  }
  #bc_module_warning .ui-icon {
     float: left;
     margin-right: 3px;
  }
  
   
.link_img, .bkp_delete, .bkp_download, .bkp_restore {
	opacity: 0.6;
    padding: 6px;
    cursor: pointer;
    
}

.link_img:hover, .bkp_delete:hover, .bkp_download:hover, .bkp_restore:hover  {
	opacity:1;
	cursor: pointer;
		
}

/*  table rows highlight */

tbody tr {
    border: 1px dotted #222529;
    vertical-align: middle;
    padding: 2px;
    height: 22px;
}

tr:hover td {
  background-color: #daecf5; 
  color: #000;
}


/**
 * Twitter Bootstrap style.
 *
 * Tested with IE 8, IE 9, Chrome 18, Firefox 9, Opera 11.
 * Does not work with IE 7.
 */

.ui-tooltip {

	font-size: 12px;
	line-height: 20px;
	color: #333333;
	width: 350px;
	

	background-color: #FBFBFB !important;

	
    min-width: 350px;

	padding: 1px;
	background-color: #ffffff  !important;
	border: 1px solid #ccc;
	border: 1px solid rgba(0, 0, 0, 0.2);
	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;
	-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
	-webkit-background-clip: padding-box;
	-moz-background-clip: padding;
	background-clip: padding-box;
}

.ui-tooltip-titlebar {

	border-color: #FBFBFB !important;
	background-color: #FBFBFB !important;

		/* padding: 8px 14px; */
		margin: 0 !important;
		font-size: 14px !important;
		color: #ffffff !important;
        font-weight: bold !important;
		line-height: 18px !important;
		
		background: #a90329;
		background: -moz-linear-gradient(top, #a90329 0%, #8f0222 44%, #6d0019 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#a90329), color-stop(44%,#8f0222), color-stop(100%,#6d0019));
		background: -webkit-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%);
		background: -o-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%);
		background: -ms-linear-gradient(top, #a90329 0%,#8f0222 44%,#6d0019 100%);
		background: linear-gradient(to bottom, #a90329 0%,#8f0222 44%,#6d0019 100%);
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a90329', endColorstr='#6d0019',GradientType=0 );
		
		
		border-bottom: 1px solid #ebebeb !important;
		-webkit-border-radius: 5px 5px 0 0 !important;
		-moz-border-radius: 5px 5px 0 0 !important;
		border-radius: 5px 5px 0 0 !important;
	}

.ui-tooltip-close{
			border: 1px solid #e3a1a1;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
			right: 11px;
			top: 45%;
			border-style: none;
		}

.ui-tooltip-content{
		font-size: 12px;
		text-align:center;
        display: block;
		padding: 15px 2px;;
        margin:  auto auto;
        float:none;
        font-size: 12px;
        border:none;
        line-height: 150%;
        border-color: #FBFBFB;
		background-color: #FBFBFB;
        background-color: #ffffff  !important;
	}


.ybtn, .nbtn {
    min-width: 70px;
    margin: 10px 5px;
    padding: 3px 10px;
    display: inline-block;
    font-size: 12px;
    text-align:center;
    text-color: #555;
}

.ybtn:hover, .nbtn:hover{
font-weight: bold
color #f6f6f6;
background: #e2e2e2;
background: -moz-linear-gradient(top, #e2e2e2 0%, #dbdbdb 50%, #d1d1d1 51%, #fefefe 100%);
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#e2e2e2), color-stop(50%,#dbdbdb), color-stop(51%,#d1d1d1), color-stop(100%,#fefefe));
background: -webkit-linear-gradient(top, #e2e2e2 0%,#dbdbdb 50%,#d1d1d1 51%,#fefefe 100%);
background: -o-linear-gradient(top, #e2e2e2 0%,#dbdbdb 50%,#d1d1d1 51%,#fefefe 100%);
background: -ms-linear-gradient(top, #e2e2e2 0%,#dbdbdb 50%,#d1d1d1 51%,#fefefe 100%);
background: linear-gradient(to bottom, #e2e2e2 0%,#dbdbdb 50%,#d1d1d1 51%,#fefefe 100%);

}


.ui-icon {
		
			width: auto;
			height: auto;
			float: right;
			font-size: 20px;
			font-weight: bold;
			line-height: 18px;
			color: #000;
			text-shadow: 0 1px 0 #555;
			opacity: 0.6;
			filter: alpha(opacity=60);
		}

.ui-icon:hover{
			color: #FFFFFF;
			background-color:#B20000;
			text-decoration: none;
			cursor: pointer;
			opacity: 1;
			filter: alpha(opacity=100);
		}



#progressbar {
font-size:10px;
    width: 300px;
    height: 12px;
    border: 1px solid #111;
    background-color: #292929;
    margin-top:25px;
    margin:auto;
    display:bloc;
    
}
#progressbar div {
    height: 100%;
    color: #fff;
    text-align: right;
    line-height: 12px; /* same as #progressBar height if we want text middle aligned */
    width: 0;
    background-color: #0099ff;
}

.round-blue {
	border-radius: 12px;
	background: #0E2FDA;	
	box-shadow: 0 -1px 1px #c0bfbc inset;
}

.round-blue div {
	border-radius: 7px;
	box-shadow: 0 2px 2px #333;	
	background-color: ##0E2FDA;
	background: -webkit-linear-gradient(top, #3D82F3, #0352A3);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#00ADFF), to(#0352A3)); 
	background: -moz-linear-gradient(top, #3D82F3, #0352A3); 
	background: -ms-linear-gradient(top, #3D82F3, #0352A3); 
	background: -o-linear-gradient(top, #3D82F3, #0352A3);	
}

.not_shown{
margin:5px auto;
padding:5px;

}

#debug_div{
width:99%;
height:150px;
float:left;
padding:0 5px 0 5px;
position:relative;
float:left;
overflow-y:auto;

}

/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */  
");


function format_bytes($size){
	$units = array(' B', ' KiB', ' MiB', ' GiB', ' TiB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++)
		$size /= 1024;
	return round($size, 2) . $units[$i];
}

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

function calculate_md5($filename){

if (file_exists($filename)) {
	return md5_file( $filename );
	} else {
			return "N/A";
			};
}

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
	arsort($files);		//sort the array to display the latest backup first
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

echo $view->header()->setAttribute('template', $T('backup_config_Header'));

echo "<p>&nbsp;</p>";

echo '
	<div id="uploadForm" >
			<input type="file" id="upload_file" name="upload_file" />
			<button type="submit" name=\'submit_file\' id=\'upload_backup_file\' ><i class="fa fa-upload"></i> Upload Backup</button>
	</div>';

echo "<div id=\"keep_holder\" style=\"padding:5px\" >How many backups to keep: <input type=\"text\" id=\"keep_value\" type=\"number\" size=\"5\" style='width:5em; text-align:center' ></div>";

echo "<div id=\"table_holder\" >".table_backup()."</div>";
echo "<dl class='rlc_module'>";
echo "</dl>";

echo "<div id='bc_module_warning' class='ui-state-highlight'> <span class='ui-icon ui-icon-info'></span>".$T('backup_config_label')."</div>";
echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
    ->insert($view->button('Help', $view::BUTTON_HELP));

// DEBUG // 

echo '<div id="extra_module_warning" class="ui-state-highlight" style="height:162px;">
<span class="fa fa-info-circle"> </span>
<span id="debug_div"></span>
</div>';
// END DEBUG //

$view->includeJavascript("

(function($){


function update_info(msj){
	$('#debug_div').append('</br>'); 
	$('#debug_div').append(msj+'</br>'); 
	$('#debug_div').animate({scrollTop: $('#debug_div').prop()}, 400);

}


// CONFIRM PROMPT ---------------------------------------------------------//
function confirm_prompt(question, btn1, btn2, callback) {
    var message = $('<div />', { 
        html: question
    }),
    spacer = $('<span />', { 
        html: '   '
    }),    
    ok = $('<button /> ', { 
        text: '',
        click: function() { callback(true); }
    }),
    cancel = $('<button />', { 
        text: '',
        click: function() { callback(false); }
    });
    
    ok.html(btn1).addClass('ybtn');
    cancel.html(btn2).addClass('nbtn');
    
    dialogue(message.append(ok).append(spacer).append(cancel), '<i class=\"fa fa-exclamation-triangle\"></i> Attention!');
}


// qTip2 DIALOG -----------------------------------------------------------//
function dialogue(content, title) {
    $('<div />').qtip({
		overwrite: false,
		id: 'qtip_dialog',
        content: {
            text: content,
            title: { button: 'Close', text:title }
        },
        position: {
            my: 'center', 
            at: 'center',
            target: $(window)
        },
        show: {
            ready: true,
            solo: true,
            modal: {
                on: true,
                blur: false
            }
        },
        hide: false,
        style:{ 
                def: false,		        
                classes: 'ui-tooltip',
                widget: false,
                width: 350
              },
       
        events: {
			     render: function(event, api) {
				    $('button', api.elements.content).click(function() { 	api.hide();  });
            }
            //hide:  function(event, api) { $(this).qtip('destroy'); $('#qtip_dialog').qtip('destroy'); api.destroy(); }
        }
    });
} // end Qtip function



//-----------------------------------------------------------------------------------// 
function update_table(){ 
						$.post( \"/bkp_jlib_ajax.php\", {act:'bkp_table'}, 
						function(data) { 
										$(\"#table_holder\").empty().html(data); 
										update_info(\" Table updated\");
										}); 
						};  

//-----------------------------------------------------------------------------------// 
function keep_value(){ 
						$.post( '/bkp_jlib_ajax.php', {act:'keep_value'}, 
						function(keep_data) { 
										$('#keep_value').val(keep_data);
										}); 
						}  

//-----------------------------------------------------------------------------------// 
function set_keep_value(set_value){ 
						$.post( '/bkp_jlib_ajax.php', {act:'set_keep_value', p1:set_value}, 
						function() { 
									$('#keep_value').val(set_value); 
									update_info('<p>updated number of backups to keep to: '+set_value+'</p>');
									}); 
						}


//-----------------------------------------------------------------------------------// 
function complete(){
			$('#progressbar').toggle(\"slow\").empty(); 
			$('.not_shown').css('visibility','visible');
			update_info('<p><i class=\"fa fa-check fa-lg\"></i> Process completed.</p>');
			
}


//-----------------------------------------------------------------------------------// 
function theLoop (i) {
  setTimeout(function () {
    progress(i, $('#progressbar'));
    if (--i) {          // If i > 0, keep going
      theLoop(i);       // Call the loop again, and pass it the current value of i
    } else { 
			complete();
			
			};
  }, 750); // wait 0.75 sec at each step
};
      
//-----------------------------------------------------------------------------------// 
function progress(percent, el) {
    var progressBarWidth = percent * el.width() / 100;
    el.find('div').animate({ width: progressBarWidth }, 300).html(percent + \"% \");
}

//-----------------------------------------------------------------------------------// 

function upload_backup() 
{
  var fd = new FormData($('#uploadForm'));    
	fd.append( 'upload_file', $('#upload_file')[0].files[0] );
	fd.append( 'act', 'load' );
	fd.append( 'submit_file','submit');
	
	$.ajax({
  			url: '/bkp_jlib_ajax.php',
  			data: fd,
  			processData: false,
  			contentType: false,
  			type: 'POST',
  			success: function(data){
							update_info(data);
							}
			});
}

//-----------------------------------------------------------------------------------// 
function ajaxCall(url,param) {

var message_status = $('<div />', { html: '<p id=\"message\"><i class=\"fa fa-cog fa-spin fa-lg\"></i> Please wait until restore finishes</p><p>&nbsp;</p> ', class:'message' }),
	progress_bar=$('<div />', {html: '<div id=\"progressbar\" class=\"round-blue\"><div class=\"progress-label\"></div></div> '}),
    close_btn = $('<button />', { text: 'Close', 'class': 'not_shown' });
	close_btn.css('visibility','hidden');


    $.ajax({
        url: url,
        data: param,
        method: 'post',
        timeout:120000,
        async:true,
        beforeSend: function(){
           
           dialogue( message_status.add(progress_bar).add(close_btn), '<i class=\"fa fa-info-circle\"></i> Status' );
           theLoop(100); // start x with percentage
        },
        success: function(data) {
									update_info(data);
								}
    	});
}


//-----------------------------------------------------------------------------------// 


//-----------------------------------------------------------------------------------// 
// Jquery logic
//-----------------------------------------------------------------------------------// 
$(document).ready(function() {

$.ajax({ cache:false, timeout:120000 });
	

// watch for backup now button press and refresh backup table
//-----------------------------------------------------------------------------------// 

$('[value=\"Backup now\"]' ).click(
	function(e){
				 
				setTimeout(function(){
										update_table();
										update_info(\" Backup executed \"); 
										}, 5000); // refresh table with a 5 sec delay because of the time needed to create a backup				
				});

// gets the curent value set in DB
//-----------------------------------------------------------------------------------// 
keep_value(); 

//prevent entering leters or symbols
$('#keep_value').on('keypress keyup blur',function (event){    
           $(this).val().replace(/[^\d].+/, \"\");
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
           
// watch the change for the number of backups
//-----------------------------------------------------------------------------------// 
$('#keep_value').change(function(){
		bkp_value = $('#keep_value').val();
		 
		if (bkp_value < 0) {
						set_keep_value('0');
						$('#keep_value').val(0);
						} else {
						
						if (bkp_value > 300 ) {
										$('#keep_value').val(300);
										set_keep_value('300');
										} else { 
												set_keep_value(bkp_value); 
												};
						};       
    });

// watch the click on delete backup
//-----------------------------------------------------------------------------------// 
$(document).on('click', '.bkp_delete',
     function(event) 
				{
				
				var fname = $(this).parent().parent().attr('id');
				var delete_message = '<p style=\"float:none; padding:10px 15px; clear:left; text-align:left;\"><i class=\"fa fa-trash-o\"></i> Do you want to <strong>delete</strong> file ? </p><p>' + fname + ' </p>';
				var delete_btn = '<strong>Delete</strong>';
				var keep_btn = '<strong>Keep</strong>';
				
				confirm_prompt( delete_message , delete_btn, keep_btn, function(yes)
						{
						if (yes){
								 $.post( '/bkp_jlib_ajax.php', {act:'delete_backup', p1:fname}, function(delete_data)
										{
										 update_table();
										 update_info(delete_data); 
										  
												
										});
								}
						}); // end call to confirm dialog
					});    

						
// watch the click on Download backup
//-----------------------------------------------------------------------------------// 
$(document).on('click', '.bkp_download',
	 function() 
				{
				window.location='/bkp_jlib_ajax.php?act=get_backup&p1='+$(this).parent().parent().attr('id');
				}
		); // end Watch Download click function


// watch the click on Restore backup
//-----------------------------------------------------------------------------------// 
$(document).on('click', '.bkp_restore',
     function() 
			{
			
			var fname = $(this).parent().parent().attr('id');
			var restore_message =  '<p style=\"float:none; padding:10px 15px; clear:left; text-align:left;\"><i class=\"fa fa-refresh\"></i> Do you want to <strong>restore</strong> configuration ? </p><p>'+fname+' </p>';
			var restore_btn = '<strong>Restore</strong>';
			var cancel_btn = '<strong>Cancel</strong>';
			
			confirm_prompt( restore_message, restore_btn, cancel_btn, function(restore) {
							if (restore) {
										ajaxCall('/bkp_jlib_ajax.php',{act:'restore_backup', p1:fname});
										}
						});
			
			}
	); // end watch BKP_Restore click function


// watch the click on upload backup
//-----------------------------------------------------------------------------------// 
$(document).on('click', '#upload_backup_file',
	 function(send) 
				{
				send.preventDefault();
				upload_backup();
				}
		); // end Watch Download click function

// watch the hover on MD5 info icon
//-----------------------------------------------------------------------------------// 
$(document).on('mouseover', '.md5_error, .md5_ok', function(ev) {
    // Bind the qTip within the event handler
    $(this).qtip({
        overwrite: false, 
        
        show: {
			effect: function() { $(this).fadeIn();},
            event: ev.type, 
            ready: true 
        },
        hide: {
            effect: function() {
                $(this).fadeOut();
            }
        },
        position: {
					my: 'bottom right',  // Position my top left...
					at: 'left top'
					
					}
    }, ev); // Pass through our original event to qTip
})


// End document.ready	
//-------------------------------------------------------------------------------------//	  
  });
})(jQuery);
");
