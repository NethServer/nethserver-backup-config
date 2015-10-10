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
    
}

.link_img:hover, .bkp_delete:hover, .bkp_download:hover, .bkp_restore:hover  {
	opacity:1;
	cursor: pointer;
		
}

/**
 * Twitter Bootstrap style.
 *
 * Tested with IE 8, IE 9, Chrome 18, Firefox 9, Opera 11.
 * Does not work with IE 7.
 */

.qtip-bootstrap-alert{
	/** Taken from Bootstrap body */
	font-size: 14px;
	line-height: 20px;
	color: #333333;
    min-width: 200px;
	/** Taken from Bootstrap .popover */
	padding: 1px;
	background-color: #ffffff;
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

	.qtip-bootstrap-alert !important .qtip-titlebar{
		/** Taken from Bootstrap .popover-title */
		padding: 8px 14px;
		margin: 0;
		font-size: 14px;
		color: #ffffff;
        font-weight: bold;
		line-height: 18px;
		background-color: #cc0000;
		border-bottom: 1px solid #ebebeb;
		-webkit-border-radius: 5px 5px 0 0;
		-moz-border-radius: 5px 5px 0 0;
		border-radius: 5px 5px 0 0;
	}

		.qtip-bootstrap-alert .qtip-titlebar .qtip-close{
			border: 1px solid #e3a1a1;
            background: #cc0000;
            color: #ffffff;
            font-weight: bold;
			right: 11px;
			top: 45%;
			border-style: none;
		}

	.qtip-bootstrap-alert .qtip-content{
		
        display: inline-block;
		padding: 15px auto;
        margin: auto auto;
        float:none;
        
        
	}

.qtip-content p{
    padding:20;
    text-align: center;
    margin: auto, auto;
    display: block;
    
}

.qtip-content input[type=button]{
    
    text-align: center;
    margin: auto, auto;
    display: inline-block;
    
}

	.qtip-bootstrap-alert .qtip-icon{
		
		
	}

	.qtip-bootstrap-alert .qtip-icon .ui-icon{
		
			width: auto;
			height: auto;

			/* Taken from Bootstrap .close */
			float: right;
			font-size: 20px;
			font-weight: bold;
			line-height: 18px;
			color: #FFFFFF;
			text-shadow: 0 1px 0 #ffffff;
			opacity: 0.6;
			filter: alpha(opacity=60);
		}

		.qtip-bootstrap-alert .qtip-icon .ui-icon:hover{
			/* Taken from Bootstrap .close:hover */
			color: #FFFFFF;
			text-decoration: none;
			cursor: pointer;
			opacity: 1;
			filter: alpha(opacity=100);
		}



/* IE9 fix - removes all filters */
.qtip:not(.ie9haxors) div.qtip-content,
.qtip:not(.ie9haxors) div.qtip-titlebar{
	filter: none;
	-ms-filter: none;
}


/* --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- --- */
  
  
");


function format_bytes($size) {
	$units = array(' B', ' KiB', ' MiB', ' GiB', ' TiB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++)
		$size /= 1024;
	return round($size, 2) . $units[$i];
}


function table_backup() {
    
    $html = '        
        <div id="ui-table-contain" class="ui-widget">
            <table id="bkptable" class="ui-widget ui-widget-content">
            <thead>
                <tr class="ui-widget-header">
                    <td>File Name</td>
                    <td style="width: 66px;">Size</td>
                    <td style="width: 73px;">Delete</td>
                    <td style="width: 92px;">Download</td>
                    <td style="width: 81px;">Restore</td>
                </tr></thead><tbody>';


    $files = glob('backup/*.{xz}', GLOB_BRACE);
    foreach ($files as $value) {
        if (($value != ".") && ($value != "..") && ($value != ".htaccess") && (!is_dir('backup/'.$value))) {
			$value= str_replace("backup/","",$value);
            $html .= '
                <tr id="' . $value . '">
                    <td>' . $value . '</td>
                    <td>' . format_bytes(filesize('backup/' . $value)) . '</td>
                    <td ><span class="bkp_delete" > Delete </span></td>
                    <td ><span class="bkp_download" > Download </span></td>
                    <td ><span class="bkp_restore" > Restore </span></td>
                </tr>';
        }
    }
    $html .= '
    </tbody></table></div>
    '; 
    return $html;
   
};

echo $view->header()->setAttribute('template', $T('backup_config_Header'));
echo "<div>".$T('current_backup_label').":</div>";

echo "<div id=\"table_holder\">".table_backup()."</div>";
echo "<dl class='rlc_module'>";
echo "</dl>";

echo "<div id='bc_module_warning' class='ui-state-highlight'><span class='ui-icon ui-icon-info'></span>".$T('backup_config_label')."</div>";
echo $view->buttonList()
    ->insert($view->button('Execute', $view::BUTTON_SUBMIT))
    ->insert($view->button('Help', $view::BUTTON_HELP))
;


// DEBUG // 

echo '<div id="debug_div" style="background-color:#CCCC99"></div>';
// END DEBUG //

$view->includeJavascript("

// CONFIRM PROMPT
function confirm_prompt(question, callback) {
    var message = $('<div />', { 
        html: question
    }),
    spacer = $('<span />', { 
        html: '   '
    }),    
    ok = $('<button /> ', { 
        text: 'Yes',
        click: function() { callback(true); }
    }),
    cancel = $('<button />', { 
        text: 'No',
        click: function() { callback(false); }
    });
    
    dialogue(message.append(ok).append(spacer).append(cancel), 'Attention!');
}






// qTip2 DIALOG
function dialogue(content, title) {
    $('<div />').qtip({
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
            modal: {
                on: true,
                blur: false
            }
        },
        hide: false,
        style:{ 
                classes: 'qtip-bootstrap-alert',
                
              },
       
        events: {
            render: function(event, api) {
                $('button', api.elements.content).click(function() {
                	api.hide();
                });
            },
            hide: function(event, api) { api.destroy(); }
        }
    });
}



(function($){

function update_table(){
 $.post( \"/bkp_jlib_ajax.php\", {act:'bkp_table'}, function(data) { $(\"#table_holder\").empty().html(data); });
 }; 


$(document).ready(function() {

$.ajax({ cache:false });
	
	// watch for backup now button press and refresh backup table
	
	$('.Buttonlist').on('click', '.submit', function() { update_table();  });
	
	$('#bkptable tbody tr ').on('click', '.bkp_delete',
     function() 
				{
				var fname = $(this).parent().parent().attr('id');
				confirm_prompt('<p> Delete: </br>'+fname+' ? </p>', function(yes) {
							if (yes) {
										$.post( '/bkp_jlib_ajax.php', {act:'delete_backup', p1:fname}, function(data) 
												{
												$('#debug_div').empty().html(data); 
												update_table(); 
												});
								};
						});
				
				
				}
	);

	$('#bkptable tbody tr ').on('click', '.bkp_download',
     function() 
				{
				window.location='/bkp_jlib_ajax.php?act=get_backup&p1='+$(this).parent().parent().attr('id');
				}
	);

	$('#bkptable tbody tr ').on('click', '.bkp_restore',
     function() 
			{
			var fname = $(this).parent().parent().attr('id');
			confirm_prompt('<p>Restore: '+fname+' ? </p>', function(restore) {
							if (restore) {
										$.post( '/bkp_jlib_ajax.php', {act:'restore_backup', p1:fname}, function(data) { $('#debug_div').empty().html(data); });
										};
						});
			
			}
	);
	
	  
  });
})(jQuery);
");
