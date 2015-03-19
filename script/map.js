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
                        goSave();                        
            });
            
            $("#btnUpdate").click(function() {
                        goUpdate();                        
            });
            
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
                            goUpload();
                        }
                        else {
                            uploadAlert("Process still in progress. Please wait until current process is finished.");
                        }
            });
            $("#btnEditUpload").click(function() {
                        if (!ajaxInProgress) {
                            ajaxInProgress = true;
                            goEditUpload();
                        }
                        else {
                            uploadAlert("Process still in progress. Please wait until current process is finished.");
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
            location.href="/map/list.html";
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
	}		
	else {
		$.ajaxFileUpload({
			url: "../php/exe/map_upload.php",
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
						$("#mapUploaded").css("visibility", "visible");
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

function goEditUpload() {
	var transfile = $("#trans_file").val();
	var idx = transfile.lastIndexOf("\\")		
	if (idx != -1) {
		transfile = transfile.substr(idx+1);
	}			
    	
	// validation here
	if (transfile == "") {
		$("#obj").val("trans_file");
        uploadAlert("Please choose file to upload.");
	}		
	else {
		$.ajaxFileUpload({
			url: "../../php/exe/map_upload.php",
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
						$("#mapUploaded").css("visibility", "visible");
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

function goSave() {
            var code = $("#code").val();
            var name = $("#name").val();
            var description = $("#description").val();
            var storeInit = $("#store_init").val();
            var map = $("#trans_file_2").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            else if (map == "") {
                $("#obj").val("trans_file");
                inputAlert("Please upload THE MAP.");
            }
            else {
                var dataString = "code=" + code + "&name=" + name + "&description=" + description + "&storeInit=" + storeInit + "&map=" + map;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/map_add.php",
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

}

function goUpdate() {
            var id = $("#id").val();
            var code = $("#code").val();
            var name = $("#name").val();
            var description = $("#description").val();
            var storeInit = $("#store_init").val();
            var map = $("#trans_file_2").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            else if (map == "") {
                $("#obj").val("trans_file");
                inputAlert("Please upload THE MAP.");
            }
            else {
                var dataString = "id=" + id + "&code=" + code + "&name=" + name + "&description=" + description + "&storeInit=" + storeInit + "&map=" + map;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/map_update.php",
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

}

function goReset() {
    $("input").each(function() {		
		$(this).val("");
	});
    $("#mapUploaded").css("visibility", "hidden");
}