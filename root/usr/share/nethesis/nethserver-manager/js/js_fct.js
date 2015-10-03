/* ----- ----- ----- */
function jqui_confirm(title, msg, btn_title, fct, width, icons) {
	if (typeof(width)==='undefined') { width = 300; };
	if (typeof(icons)==='undefined') { icons = true; };
	jqui_c = $( "#dialog-confirm" ).dialog({
		width: width,
		title: title,
		autoOpen: false,
		resizable: false,
		height: "auto",
		dialogClass: "confirm_jqui",
		modal: true,
        open: function(event, ui){
            beep('snd/pageturn.wav');
        },
		buttons: [
			{
				text: btn_title,
				icons: {primary: "ui-icon-check"},
				click: function() {
					$( this ).dialog( "close" );
					$( this ).dialog( "destroy" );
					fct();
				}
			},
			{
				text: "Cancel",
				click: function() {
					$( this ).dialog( "close" );
					$( this ).dialog( "destroy" );
				}
			}
		]
	});
	$("#confirm_msg").html(msg);
	if (icons == true) {
		$("#dialog-confirm .ui-icon-alert").show();
	} else {
		$("#dialog-confirm .ui-icon-alert").hide();
	}	
	jqui_c.dialog("open");
};

function jqui_alert(title, msg, fct, width) {
	if (typeof(width)==='undefined' || width=="" ) { width = 300; };
	if (typeof(fct)==='undefined' || fct=="") { fct = function() {}; };
	jqui_a = $( "#dialog-message" ).dialog({
		title: title,
		width: width,
		autoOpen: false,
		resizable: false,
		modal: true,
		height: "auto",
		dialogClass: "alert_jqui",
        open: function(event, ui){
            beep('snd/pageturn.wav');
        },
        close: function( event, ui ){
        	$(this).find("#alert_msg").html("");
        },
		buttons: {
			Ok: function() {
				if (fct() != false) {
				$( this ).dialog( "close" );
				$( this ).dialog( "destroy" );
				}
			}
		}
	});
	
	$("#alert_msg").html(msg);
	jqui_a.dialog("open");
};
