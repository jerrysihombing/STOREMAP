var ajaxInProgress = false;
var isWarned = false;

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
            
            $("#wide").autoNumeric('init', {aSep: ',', aDec: '.' , aPad: false, vMax: '9999999999'});
            
            // view alert
            $("#view-alert").dialog({
                modal: true,
                autoOpen: false,
                height:220,
                width:350,
                resizable: false,
                buttons: {                    
                    OK: function() {
                        var map = $("#map").val();
                        $(this).dialog("close");
                        window.open("/section/map/" + map + ".html", "mapCoordinate");
                    }
                }
            });
            
            $("#shape").change(function() {
                        var shape = $("option:selected", this).val();
                        
                        if (shape == "circle") {
                                    $("#coordinate_rect").css("display", "none");
                                    $("#coordinate_circle").css("display", "block");
                                    $("#coordinate_poly").css("display", "none");
                        }
                        else if (shape == "poly") {
                                    $("#coordinate_rect").css("display", "none");
                                    $("#coordinate_circle").css("display", "none");
                                    $("#coordinate_poly").css("display", "block");
                        }
                        else {
                                    $("#coordinate_rect").css("display", "block");
                                    $("#coordinate_circle").css("display", "none");
                                    $("#coordinate_poly").css("display", "none");
                        }
            });
            
            $("#map_code").change(function() {
                        var mapCode = $("option:selected", this).val();
                        var aMapCode = mapCode.split("#");
                        var mapId = "";
                        if (aMapCode[0] != null) {
                                    mapId = aMapCode[0];
                        }
                        if (mapId != "") {
                                    viewAlert(mapId);
                        }
            });
            
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
            location.href="/section/list.html";
}

function goSave() {
            var code = $("#code").val();
            var name = $("#name").val();
            var description = $("#description").val();
            var brandName = $("#brand_name option:selected").val();
            var division = $("#division option:selected").val();
            var map = $("#map_code option:selected").val();
            var aMap = map.split("#");
            var mapCode = "";
            if (aMap[1] != null) {
                        mapCode = aMap[1];
            }
            var shape = $("#shape option:selected").val();
            
            var topLeft = $("#top_left").val();
            var bottomRight = $("#bottom_right").val();
            var radius = $("#radius").val();
            var center = $("#center").val();
            var coordinate = $("#coordinate").val();
            
            var wide = $("#wide").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please choose BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please choose DIVISION.");
            }
            else if (mapCode == "") {
                $("#obj").val("map_code");
                inputAlert("Please choose MAP CODE.");
            }
            else if (shape == "") {
                $("#obj").val("shape");
                inputAlert("Please choose SHAPE.");
            }
            else if (shape == "rect" && (topLeft == "" || bottomRight == "")) {
                if (topLeft == "") {
                        $("#obj").val("top_left");
                        inputAlert("Please enter TOP-LEFT.");        
                }
                else if (bottomRight == "") {
                        $("#obj").val("bottom_right");
                        inputAlert("Please enter BOTTOM-RIGHT.");
                }
            }
            else if (shape == "circle" && (radius == "" || center == "")) {
                if (radius == "") {
                        $("#obj").val("radius");
                        inputAlert("Please enter RADIUS.");        
                }
                else if (center == "") {
                        $("#obj").val("center");
                        inputAlert("Please enter CENTER.");
                }
            }
            else if (shape == "poly" && coordinate == "") {
                        $("#obj").val("coordinate");
                        inputAlert("Please enter MULTIPLE X, Y.");        
            }
            else if (wide == "") {
                $("#obj").val("wide");
                inputAlert("Please enter WIDE.");
            }
            else {
                wide = $("#wide").autoNumeric("get");
                var dataString = "code=" + code + "&name=" + name + "&description=" + description + "&brandName=" + brandName + "&division=" + division + "&mapCode=" + mapCode + "&shape=" + shape +
                                 "&topLeft=" + topLeft + "&bottomRight=" + bottomRight + "&radius=" + radius + "&center=" + center + "&coordinate=" + coordinate + "&wide=" + wide;
                $.ajax({
                        type: "POST",
                        url: "../php/exe/coordinate_add.php",
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
            var brandName = $("#brand_name option:selected").val();
            var division = $("#division option:selected").val();
            var map = $("#map_code option:selected").val();
            var aMap = map.split("#");
            var mapCode = "";
            if (aMap[1] != null) {
                        mapCode = aMap[1];
            }
            var shape = $("#shape option:selected").val();
            
            var topLeft = $("#top_left").val();
            var bottomRight = $("#bottom_right").val();
            var radius = $("#radius").val();
            var center = $("#center").val();
            var coordinate = $("#coordinate").val();
             
            // validation here
            if (code == "") {
                $("#obj").val("code");
                inputAlert("Please enter CODE.");
            }
            else if (name == "") {
                $("#obj").val("name");
                inputAlert("Please enter NAME.");
            }
            else if (brandName == "") {
                $("#obj").val("brand_name");
                inputAlert("Please choose BRAND NAME.");
            }
            else if (division == "") {
                $("#obj").val("division");
                inputAlert("Please choose DIVISION.");
            }
            else if (mapCode == "") {
                $("#obj").val("map_code");
                inputAlert("Please choose MAP CODE.");
            }
            else if (shape == "") {
                $("#obj").val("shape");
                inputAlert("Please choose SHAPE.");
            }
            else if (shape == "rect" && (topLeft == "" || bottomRight == "")) {
                if (topLeft == "") {
                        $("#obj").val("top_left");
                        inputAlert("Please enter TOP-LEFT.");        
                }
                else if (bottomRight == "") {
                        $("#obj").val("bottom_right");
                        inputAlert("Please enter BOTTOM-RIGHT.");
                }
            }
            else if (shape == "circle" && (radius == "" || center == "")) {
                if (radius == "") {
                        $("#obj").val("radius");
                        inputAlert("Please enter RADIUS.");        
                }
                else if (center == "") {
                        $("#obj").val("center");
                        inputAlert("Please enter CENTER.");
                }
            }
            else if (shape == "poly" && coordinate == "") {
                        $("#obj").val("coordinate");
                        inputAlert("Please enter MULTIPLE X, Y.");        
            }
            else if (wide == "") {
                $("#obj").val("wide");
                inputAlert("Please enter WIDE.");
            }
            else {
                wide = $("#wide").autoNumeric("get");
                var dataString = "id=" + id + "&code=" + code + "&name=" + name + "&description=" + description + "&brandName=" + brandName + "&division=" + division + "&mapCode=" + mapCode + "&shape=" + shape +
                                 "&topLeft=" + topLeft + "&bottomRight=" + bottomRight + "&radius=" + radius + "&center=" + center + "&coordinate=" + coordinate + "&wide=" + wide;
                $.ajax({
                        type: "POST",
                        url: "../../php/exe/coordinate_update.php",
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

function viewAlert(map) {
    $("#map").val(map);
    
    if (isWarned) {
            window.open("/section/map/" + map + ".html", "mapCoordinate");
    }
    else {
            $("#view-alert").dialog("open"); 
            isWarned = true;
    }
}
