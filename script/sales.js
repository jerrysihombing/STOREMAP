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
            
            $("#quantity").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            $("#amount").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            
            $("#trans_date").datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: "dd-mm-yy"
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
            location.href="/sales/list.html";
}

function goSave() {
            var transDate = $("#trans_date").val();
            var brandName = $("#brand_name option:selected").val();
            var division = $("#division option:selected").val();
            var articleType = $("#article_type option:selected").val();
            var quantity = $("#quantity").val();
            var amount = $("#amount").val();
            var storeInit = $("#store_init").val();
             
            // validation here
            if (transDate == "") {
                $("#obj").val("trans_date");
                inputAlert("Please enter TRANS DATE.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please enter BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please enter DIVISION.");
            }
            else if (articleType == "") {
                $("#obj").val("article_type");
                inputAlert("Please enter ARTICLE TYPE.");
            }
            /*
            else if (quantity == "") {
                $("#obj").val("quantity");
                inputAlert("Please enter QUANTITY.");
            }
            */
            else if (amount == "") {
                $("#obj").val("amount");
                inputAlert("Please enter AMOUNT.");
            }
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            else {
                quantity = $("#quantity").autoNumeric("get");
                amount = $("#amount").autoNumeric("get");
                var dataString = "transDate=" + transDate + "&brandName=" + brandName + "&division=" + division + "&articleType=" + articleType + "&quantity=" + quantity + "&amount=" + amount + "&storeInit=" + storeInit;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/sales_add.php",
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
            var transDate = $("#trans_date_e").val();
            var brandName = $("#brand_name").val();
            var division = $("#division").val();
            var articleType = ($("#article_type").val() == "Obral" ? "1" : "0");
            var quantity = $("#quantity").val();
            var amount = $("#amount").val();
            var storeInit = $("#store_init").val();
             
            // validation here
            if (transDate == "") {
                $("#obj").val("trans_date");
                inputAlert("Please enter TRANS DATE.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please enter BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please enter DIVISION.");
            }
            else if (articleType == "") {
                $("#obj").val("article_type");
                inputAlert("Please enter ARTICLE TYPE.");
            }
            /*
            else if (quantity == "") {
                $("#obj").val("quantity");
                inputAlert("Please enter QUANTITY.");
            }
            */
            else if (amount == "") {
                $("#obj").val("amount");
                inputAlert("Please enter AMOUNT.");
            }
            else if (storeInit == "") {
                $("#obj").val("store_init");
                inputAlert("Please enter STORE INITIAL.");
            }
            else {
                quantity = $("#quantity").autoNumeric("get");
                amount = $("#amount").autoNumeric("get");
                var dataString = "id=" + id + "&transDate=" + transDate + "&brandName=" + brandName + "&division=" + division + "&articleType=" + articleType + "&quantity=" + quantity + "&amount=" + amount + "&storeInit=" + storeInit;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/sales_update.php",
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