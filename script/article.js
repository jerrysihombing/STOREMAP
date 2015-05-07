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

function showProcessing() {    
	$("#processing").css("visibility", "visible");
}

function hideProcessing() {    
	$("#processing").css("visibility", "hidden");
}

function goBack() {
            location.href="/article/list.html";
}

function goSave() {
            var plu8 = $("#plu8").val();
            var articleType = $("#article_type option:selected").val();
            //var articleCode = $("#article_code").val();
            var articleCode = "";
            var description = $("#description").val();
            var brandName = $("#brand_name option:selected").val();
            var division = $("#division option:selected").val();
            //var storeInit = $("#store_init").val();
            var storeInit = "";
            
            // validation here
            if (plu8 == "") {
                $("#obj").val("plu8");
                inputAlert("Please enter PLU (8 DIGITS).");
            }
            else if (articleType == "") {
                $("#obj").val("article_type");
                inputAlert("Please select ARTICLE TYPE.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please select BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please select DIVISION.");
            }
            /*
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            */
            else {
                var dataString = "plu8=" + plu8 + "&articleType=" + articleType + "&articleCode=" + articleCode + "&description=" + description + "&brandName=" + brandName + "&division=" + division + "&storeInit=" + storeInit;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/article_add.php",
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
            var plu8 = $("#plu8").val();
            var articleType = $("#article_type option:selected").val();
            //var articleCode = $("#article_code").val();
            var articleCode = "";
            var description = $("#description").val();
            var brandName = $("#brand_name option:selected").val();
            var division = $("#division option:selected").val();
            //var storeInit = $("#store_init").val();
            var storeInit = "";
             
            // validation here
            if (plu8 == "") {
                $("#obj").val("plu8");
                inputAlert("Please enter PLU (8 DIGITS).");
            }
            else if (articleType == "") {
                $("#obj").val("article_type");
                inputAlert("Please select ARTICLE TYPE.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please select BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please select DIVISION.");
            }
            /*
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            */
            else {
                var dataString = "id=" + id + "&plu8=" + plu8 + "&articleType=" + articleType + "&articleCode=" + articleCode + "&description=" + description + "&brandName=" + brandName + "&division=" + division + "&storeInit=" + storeInit;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/article_update.php",
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
    //$("#brandUploaded").css("visibility", "hidden");
}