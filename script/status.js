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
            
            $("#min_value").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            $("#max_value").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            $("#min_value_wide").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            $("#max_value_wide").autoNumeric('init', {aSep: ',', aPad: false, vMax: '999999999999999999'});
            
            var vColor = "#" + $("#color").val();
            if (vColor == "") {
                        vColor = "#FF8800";
            }
            $('.color-box').colpick({
                        colorScheme:'dark',
                        layout:'rgbhex',
                        color:vColor,
                        onSubmit:function(hsb,hex,rgb,el) {
                            $(el).css('background-color', '#'+hex);
                            $(el).colpickHide();
                            $('#color').val(hex);
                        }
            }).css('background-color', vColor);

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
            location.href="/status/list.html";
}

function goSave() {
            var code = $("#code").val();
            var name = $("#name").val();
            var description = $("#description").val();
            var color = $("#color").val();
            var minValue = $("#min_value").val();
            var maxValue = $("#max_value").val();
            var minValueWide = $("#min_value_wide").val();
            var maxValueWide = $("#max_value_wide").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (color == "") {
                $("#obj").val("color");
                inputAlert("Please choose COLOR.");
            }
            else if (minValue == "") {
                $("#obj").val("min_value");
                inputAlert("Please enter MIN VALUE.");
            }
            else if (maxValue == "") {
                $("#obj").val("max_value");
                inputAlert("Please enter MAX VALUE.");
            }
            else if (minValueWide == "") {
                $("#obj").val("min_value_wide");
                inputAlert("Please enter MIN VALUE PER M2.");
            }
            else if (maxValueWide == "") {
                $("#obj").val("max_value_wide");
                inputAlert("Please enter MAX VALUE PER M2.");
            }
            else {
                //minValue = $("#min_value").autoNumericGet();
                //maxValue = $("#max_value").autoNumericGet();
                minValue = $("#min_value").autoNumeric("get");
                maxValue = $("#max_value").autoNumeric("get");
                minValueWide = $("#min_value_wide").autoNumeric("get");
                maxValueWide = $("#max_value_wide").autoNumeric("get");
                
                var dataString = "code=" + code + "&name=" + name + "&description=" + description + "&color=" + color +
                                    "&minValue=" + minValue + "&maxValue=" + maxValue + "&minValueWide=" + minValueWide + "&maxValueWide=" + maxValueWide;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/status_add.php",
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
            var color = $("#color").val();
            var minValue = $("#min_value").val();
            var maxValue = $("#max_value").val();
            var minValueWide = $("#min_value_wide").val();
            var maxValueWide = $("#max_value_wide").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (color == "") {
                $("#obj").val("color");
                inputAlert("Please choose COLOR.");
            }
            else if (minValue == "") {
                $("#obj").val("min_value");
                inputAlert("Please enter MIN VALUE.");
            }
            else if (maxValue == "") {
                $("#obj").val("max_value");
                inputAlert("Please enter MAX VALUE.");
            }
            else if (minValueWide == "") {
                $("#obj").val("min_value_wide");
                inputAlert("Please enter MIN VALUE PER M2.");
            }
            else if (maxValueWide == "") {
                $("#obj").val("max_value_wide");
                inputAlert("Please enter MAX VALUE PER M2.");
            }
            else {
                //minValue = $("#min_value").autoNumericGet();
                //maxValue = $("#max_value").autoNumericGet();
                minValue = $("#min_value").autoNumeric("get");
                maxValue = $("#max_value").autoNumeric("get");
                minValueWide = $("#min_value_wide").autoNumeric("get");
                maxValueWide = $("#max_value_wide").autoNumeric("get");
                
                var dataString = "id=" + id + "&code=" + code + "&name=" + name + "&description=" + description + "&color=" + color +
                                    "&minValue=" + minValue + "&maxValue=" + maxValue + "&minValueWide=" + minValueWide + "&maxValueWide=" + maxValueWide;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/status_update.php",
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