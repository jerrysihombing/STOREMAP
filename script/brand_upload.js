var ajaxInProgress = false;

var opts = {
            lines: 13, // The number of lines to draw
            length: 20, // The length of each line
            width: 10, // The line thickness
            radius: 24, // The radius of the inner circle
            corners: 1, // Corner roundness (0..1)
            rotate: 0, // The rotation offset
            direction: 1, // 1: clockwise, -1: counterclockwise
            color: '#000', // #rgb or #rrggbb or array of colors
            speed: 1, // Rounds per second
            trail: 60, // Afterglow percentage
            shadow: false, // Whether to render a shadow
            hwaccel: false, // Whether to use hardware acceleration
            className: 'spinner', // The CSS class to assign to the spinner
            zIndex: 2e9, // The z-index (defaults to 2000000000)
            top: '50%', // Top position relative to parent
            left: '50%' // Left position relative to parent
};

$(document).ajaxStart(function () {
            showProcessing();
}).ajaxStop(function () {
            hideProcessing();
});

$(function() {         
                        
            $("#elx").spin(opts);
                            
            // alert
            $("#input-alert").dialog({
                modal: true,
                autoOpen: false,
                height:220,
                width:350,
                resizable: false,
                buttons: {                    
                    OK: function() {
                        $(this).dialog("close");
                        var obj = $("#obj").val();
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
                        //goReset();
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
            
            // alert
            $("#upload-alert").dialog({
                modal: true,
                autoOpen: false,
                height:220,
                width:350,
                resizable: false,
                buttons: {                    
                    OK: function() {
                        $(this).dialog("close");
                        var obj = $("#obj").val();
                        if (obj) {
                                    $("#"+obj).focus();
                        }
                    }
                }
            });
            
            // success
            $("#upload-success").dialog({
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
            $("#upload-error").dialog({
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
            
            $("#btnSave").click(function() {
                        goValidate();
            });
            
            /*
            $("#btnUpdate").click(function() {
                        goUpdate();                        
            });
            */
            
            $("#btnBack").click(function() {
                        goBack();
            });
            
            /*
            $("#btnReset").click(function() {
                        goReset();
            });
            */
            
            $("#btnUpload").click(function() {
                        if (!ajaxInProgress) {
                            ajaxInProgress = true;
                            $("#trans_file_2").val("");
                            $("#brandUploaded").css("visibility", "hidden");
                            $("#dwLog").css("visibility", "hidden");
                            $("#logInvalid").val("");
                            goUpload();
                        }
                        else {
                            uploadAlert("Process still in progress. Please wait until current process is finished.");
                        }
            });
            /*
            $("#btnEditUpload").click(function() {
                        if (!ajaxInProgress) {
                            ajaxInProgress = true;
                            goEditUpload();
                        }
                        else {
                            uploadAlert("Process still in progress. Please wait until current process is finished.");
                        }
            });
            */
            
            $("#dwForm").click(function() {
                        dwForm();
            });
            
            $("#dwLog").click(function() {
                        var id = $("#logInvalid").val();
                        goLogPreview(id);
            });
            
});

function dwForm() {
            location.href="/brand/dwform-brand.html";
}

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

function uploadAlert(msg) {	
	$("#upload-alert-message").html(msg);
	$("#upload-alert").dialog("open");
}

function uploadSuccess() {    
	$("#upload-success").dialog("open");
}

function uploadError(msg) {
	$("#upload-error-message").html(msg);
    $("#upload-error").dialog("open");
}

function showProcessing() {    
	$("#processing").css("visibility", "visible");
}

function hideProcessing() {    
	$("#processing").css("visibility", "hidden");
}

function goBack() {
            location.href="/brand/list.html";
}

function goUpload() {
	var transfile = $("#trans_file").val();
	var idx = transfile.lastIndexOf("\\")		
	if (idx != -1) {
		transfile = transfile.substr(idx+1);
	}			
    	
	// validation here
	if (transfile == "") {
		$("#obj").val("trans_file");
        uploadAlert("Please choose file to upload.");
        ajaxInProgress = false;
	}		
	else {
		$.ajaxFileUpload({
			url: "../php/exe/brand_upload.php",
			secureuri: false,
			fileElementId: "trans_file",
			dataType: "json",
			success: function(data, status) {
				if (typeof(data.error) != "undefined") {
					if (data.error != "") {
						uploadError(data.error);                        
					}
					else {
						uploadSuccess();
						$("#brandUploaded").css("visibility", "visible");
                        $("#trans_file_2").val(data.msg);
					}
				}
                ajaxInProgress = false;
			},
			error: function(data, e) {
				if (e == "error") {					
					uploadError("Error occured while trying to upload file. Please try again later.");
				}
				else {					
					uploadError("Unknown error.");
				}
                ajaxInProgress = false;
			}
		});
	}
	
}

function goValidate() {
            var fileToExe = $("#trans_file_2").val()
             
            // validation here
            if (fileToExe == "") {
                inputAlert("Please upload the .xls file.");
            }
            else {
                var dataString = "fileToExe=" + fileToExe;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/brand_upload_validation.php",
                        data: dataString,
                        beforeSend: function() {
                            ajaxInProgress = true;
                            showProcessing();
                        },
                        success: function(data, textStatus, xhr) {
                                if (data.substr(0, 7) == "Success") {
                                    //inputSuccess();                            
                                    goSubmit();
                                }
                                else if (data.substr(0, 13) == "Invalid found") {
                                    $("#logInvalid").val(data.substr(15));
                                    $("#dwLog").css("visibility", "visible");
                                    inputError("Invalid/exist item found. Please refer to log file.");
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
                            hideProcessing();
                        }
                    });
            }

}

function goSubmit() {
            var fileToExe = $("#trans_file_2").val()
             
            var dataString = "fileToExe=" + fileToExe;
            $.ajax({
                    type: "POST",
                    url: "../php/exe/brand_upload_submit.php",
                    data: dataString,
                    beforeSend: function() {
                        ajaxInProgress = true;
                        showProcessing();
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
                        hideProcessing();
                    }
                });
}

function goReset() {
    $("input").each(function() {		
		$(this).val("");
	});
    //$("#brandUploaded").css("visibility", "hidden");
}

function goLogPreview(id) {
	var newId = id.replace(/\//g, "-"); 

    //alert(newId); return;
    var url = "/brand/dwlog/"+newId+".html";
	
	child = Popup(url);
}

function Popup(url) {	
	var popHt = 435;
	//var popHt = 550;
	var popWd = 600;
	//var popWd = 970;
	var popX = 200;
	var popY = 50;
						 
	//var windowAttr = "location=yes,statusbar=no,directories=no,menubar=yes,titlebar=no,toolbar=no,dependent=no";
	var windowAttr = "location=yes,statusbar=no,directories=no,menubar=yes,titlebar=no,toolbar=no,dependent=no";
	windowAttr += ",width=" + popWd + ",height=" + popHt;
	windowAttr += ",resizable=yes,screenX=" + popX + ",screenY=" + popY + ",personalbar=no,scrollbars=yes";

	var newWin = window.open(url, "_blank",  windowAttr);

	return newWin;
}
