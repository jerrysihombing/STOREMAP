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
            $("#data_value").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            
            $("#map_code").change(function() {
                        var mapCode = $("option:selected", this).val();
                        var dataString = "mapCode=" + mapCode;
                        $.ajax({
                                type: "POST",
                                url: "../php/exe/load_coordinate_by_map.php",
                                data: dataString,
                                beforeSend: function() {
                                    ajaxInProgress = true;
                                    showProcessing();
                                },
                                success: function(data, textStatus, xhr) {
                                        $("#storemap_code").html(data);
                                },
                                error: function(xhr, textStatus, errorThrown) {
                                    inputError(errorThrown);
                                },
                                complete: function(xhr, textStatus) {
                                    ajaxInProgress = false;
                                    hideProcessing();
                                }
                            });
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
            location.href="/data/list.html";
}

function goSave() {
            var mapCode = $("#map_code option:selected").val();
            var storemapCode = $("#storemap_code option:selected").val();
            var dataCategory = $("#data_category option:selected").val();
            var dataValue = $("#data_value").val();
            var dataMonth = $("#data_month option:selected").val();
            var dataYear = $("#data_year option:selected").val();  
            var description = $("#description").val();
             
            // validation here
            if (mapCode == "") {
                $("#obj").val("map_code");
                inputAlert("Please select MAP.");
            }
            else if (storemapCode == "") {
                $("#obj").val("storemap_code");
                inputAlert("Please select SECTION.");
            }
            else if (dataCategory == "") {
                $("#obj").val("data_category");
                inputAlert("Please select CATEGORY.");
            }
            else if (dataValue == "") {
                $("#obj").val("data_value");
                inputAlert("Please enter VALUE.");
            }
            else if (dataMonth == "") {
                $("#obj").val("data_month");
                inputAlert("Please select MONTH.");
            }
            else if (dataYear == "") {
                $("#obj").val("data_year");
                inputAlert("Please select YEAR.");
            }
            else {
                dataValue = $("#data_value").autoNumeric("get");
                
                var dataString = "mapCode=" + mapCode + "&storemapCode=" + storemapCode + "&dataCategory=" + dataCategory + "&dataValue=" + dataValue + "&dataMonth=" + dataMonth + "&dataYear=" + dataYear + "&description=" + description;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/data_add.php",
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
            var mapCode = $("#map_code").val();
            var storemapCode = $("#storemap_code").val();
            var dataCategory = $("#data_category").val();
            var dataValue = $("#data_value").val();
            var dataMonth = $("#data_month").val();
            var dataYear = $("#data_year").val();  
            var description = $("#description").val();
             
            // validation here
            if (mapCode == "") {
                $("#obj").val("map_code");
                inputAlert("Please select MAP.");
            }
            else if (storemapCode == "") {
                $("#obj").val("storemap_code");
                inputAlert("Please select SECTION.");
            }
            else if (dataCategory == "") {
                $("#obj").val("data_category");
                inputAlert("Please select CATEGORY.");
            }
            else if (dataValue == "") {
                $("#obj").val("data_value");
                inputAlert("Please enter VALUE.");
            }
            else if (dataMonth == "") {
                $("#obj").val("data_month");
                inputAlert("Please select MONTH.");
            }
            else if (dataYear == "") {
                $("#obj").val("data_year");
                inputAlert("Please select YEAR.");
            }
            else {
                dataValue = $("#data_value").autoNumeric("get");
                
                var dataString = "id=" + id + "&mapCode=" + mapCode + "&storemapCode=" + storemapCode + "&dataCategory=" + dataCategory + "&dataValue=" + dataValue + "&dataMonth=" + dataMonth + "&dataYear=" + dataYear + "&description=" + description;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/data_update.php",
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
}