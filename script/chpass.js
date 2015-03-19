// JavaScript Document

var ajaxInProgress = false;

$(function() {
	$("#btnSave").click(function() {
		goChangePassword();
	});
	
	// alert
	$("#input-alert").dialog({
		modal: true,
		autoOpen: false,
		height:220,
		width:350,
		resizable: false,
		buttons: {                    
			OK: function() {
				var obj = $("#obj").val();
				$(this).dialog("close");
				if (obj) {
					$("#"+obj).focus();
				}
			}
		}
	});
	
	// success
	$("#input-success").dialog({
		modal: true,
		autoOpen: false,
		height:220,
		width:350,
		resizable: false,
		buttons: {                    
			OK: function() {
				$(this).dialog("close");				
			}
		}
	});
	
	// error
	$("#input-error").dialog({
		modal: true,
		autoOpen: false,
		height:220,
		width:350,
		resizable: false,
		buttons: {                    
			OK: function() {
				$(this).dialog("close");                        
			}
		}
	});
});

function inputAlert(msg) {	
	$("#alert-message").html(msg);
	$("#input-alert").dialog("open");
}

function inputSuccess() {
    $("#input-success").dialog("open");
}

function inputError(msg) {
	$("#error-message").html(msg);
    $("#input-error").dialog("open");
}

function goChangePassword() {
	
	var bValid = false;
				
	// get form variables
	var old_passwd = $("#old_passwd").val();		
	var new_passwd = $("#new_passwd").val();		
	var new_passwd_confirm = $("#new_passwd_confirm").val();	
	
	// validation here
	if (old_passwd == "") {
		$("#obj").val("old_passwd");
        inputAlert("Please enter Current Password.");
	}	
	else if (new_passwd == "") {
		$("#obj").val("new_passwd");
        inputAlert("Please enter New Password.");
	}					
	else if (new_passwd_confirm == "") {
		$("#obj").val("new_passwd_confirm");
        inputAlert("Please re-type New Password.");
	}
	else if (new_passwd != new_passwd_confirm) {
		$("#obj").val("new_passwd_confirm");
        inputAlert("Re-type Password didn't match with Password.");
	}
	else {
		
		var dataString = "old_passwd=" + old_passwd + "&new_passwd=" + new_passwd + "&new_passwd_confirm=" + new_passwd_confirm;
		$.ajax({
			type: "POST",
			url: "../php/exe/change_password.php",
			data: dataString,
			beforeSend: function() {
				ajaxInProgress = true;
			},
			success: function(data, textStatus, xhr) {
				if (data.substr(0, 7) == "Success") {
					inputSuccess();
				}
				else {	
					inputError(data);
				}								
			},
			error: function(xhr, textStatus, errorThrown) {
				inputError(errorThrown);
			},
			complete: function(xhr, textStatus) {
				ajaxInProgress = false;
			}		
		});
	}
	
}

