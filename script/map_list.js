var oTable;
var isWarned = false;

$(function() {
            
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
                        window.open("/map/view/" + map + ".html", "mapViewer");
                    }
                }
            });
            
            // alert
            $("#delete-alert").dialog({
                modal: true,
                autoOpen: false,
                height:220,
                width:350,
                resizable: false,
                buttons: {                    
                    Yes: function() {	
                        var id = $("#id").val();
                        
                        var dataString = "id=" + id;		
                        $.ajax({
                            type: "POST",
                            url: "../php/exe/map_delete.php",
                            data: dataString,
                            beforeSend: function() {
                            },
                            success: function(data, textStatus, xhr) {
                                if (data.substr(0, 7) == "Success") {
                                    // cannot close dialog from here, just do reload table		                                    
                                    deleteSuccess(); 
                                    reloadTable();                                    
                                }
                                else {                                    
                                    deleteError(data);                        
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {        
                                    deleteError(errorThrown);
                            },
                            complete: function(xhr, textStatus) {
                            }
                        });
                        $(this).dialog("close");
                    },
                    No: function() {
                        $(this).dialog("close");
                    }
                }
            });
            
            // success
            $("#delete-success").dialog({
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
            $("#delete-error").dialog({
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
            
            oTable = $('#listTable').dataTable( {
                        "bProcessing": true,
                        "bServerSide": true,
                        "bJQueryUI": true,
                        "bStateSave": false,
                        "sAjaxSource": "../php/exe/map_list.php",
                        "bPaginate": true,
                        "sPaginationType": "full_numbers",
                        "bAutoWidth": false,
                        "bFilter": true,
                        "bSort": true,
                        "bInfo": true,
                        "iDisplayStart": 0,
                        "aoColumns": [
                             null,
                             null,
                             null,
                             null,                             
                             //{"asSorting": [ "desc", "asc" ]},
                             {"bSearchable": false, "bSortable": false, "sClass": "al_center"},
                             {"bSearchable": false, "bSortable": false, "sClass": "al_center"},
                             {"bSearchable": false, "bSortable": false, "sClass": "al_center"}			
                        ],
                        "aaSorting": [[ 0, "desc" ]],
                        "bLengthChange": false,
                        //"sDom": '<"H"lfr>t<"F"ip>', //-> default
                        //"sDom": '<"H"lr>t<"F"ip>',
                        "oLanguage": {
                            "sLengthMenu":  "Display <select>" + 
                                            "<option value='10'>10</option>" + 
                                            "<option value='20'>20</option>" + 
                                            "<option value='30'>30</option>" + 
                                            "<option value='40'>40</option>" + 
                                            "<option value='50'>50</option>" + 
                                            "</select> records per page.",
                            "sSearch": "Name: " 
                        },
                        "fnServerData": function(sSource, aoData, fnCallback) {
                            /* Add some extra data to the sender */
                            aoData.push({"name": "s_code", "value": $("#s_code").val()});
                            aoData.push({"name": "s_name", "value": $("#s_name").val()});
                            aoData.push({"name": "s_description", "value": $("#s_description").val()});
                            aoData.push({"name": "s_store_init", "value": $("#s_store_init").val()});
                            aoData.push({"name": "s_adv", "value": $("#s_adv").val()});	
                            $.getJSON(sSource, aoData, function (json) {
                                /* Do whatever additional processing you want on the callback, then tell DataTables */								 
                                fnCallback(json)
                            });
                        },
                        "iDisplayLength": 20/*,
                        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                            if ( jQuery.inArray(aData[0], gaiSelected) != -1 )
                            {
                                $(nRow).addClass('row_selected');
                            }
                            return nRow;
                        } */
            } );
                        
            // use this plugin to replace some code below
            oTable.fnFilterOnReturn();
            
            // for show / hide search form
            $("#search_container").hide();
            $('#adv_main').click(function(){
                $("#search_container").slideToggle();
            });
            
});

function goSearch() {
	$(".dataTables_filter input").val("");
	$("#s_adv").val("yes");
	
	oTable.fnDraw();
}

function goReset() {
	$("input").each(function() {		
		$(this).val("");
	});
	$("#s_adv").val("no");	
    
	oTable.fnFilter("");
}

function viewAlert(map) {
    $("#map").val(map);
    
    if (isWarned) {
            window.open("/map/view/" + map + ".html", "mapViewer");
    }
    else {
            $("#view-alert").dialog("open");       
            isWarned = true;
    }
}

function deleteAlert(id, code) {
    $("#id").val(id);
	$("#delete-id").html(code);
	$("#delete-alert").dialog("open");
}

function deleteSuccess() {
    $("#delete-success").dialog("open");
}

function deleteError(msg) {
	$("#error-message").html(msg);
    $("#delete-error").dialog("open");
}

function reloadTable() {
	oTable.fnDraw();
}
