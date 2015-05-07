// JavaScript Document

var ajaxInProgress = false;

$(function() {		
	
	$("#tabs").tabs();
	
	// -- user -- //
	loadUserTable();
	// -- role -- //
	loadRoleTable();
	// -- backup -- //
	//listingBackup();	
	
	$("#isPasswdReset").click(function() {
		var status = $(this).prop("checked");
		$("#passwd_reset").prop("disabled", !status);
	});
	
	// role add dialog
	$("#role_add").dialog({
		modal: true,
		autoOpen: false,
		height:540,
		width:620,
		resizable: false,
		buttons: {
			Save: function() {
				var bValid = false;
				var	tips = $("#validate-tips-4");
				var	text_tips = $("#text-tips-3");
				tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
				// get form variables
				var role_name = $("#role_name").val();	
				var description = $("#description").val();	
				var menu_items = getMenuItems();
						
				// validation here
				if (role_name == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter Role Name.");
					$("#role_name").focus();
				}	
				else if (menu_items == "") {
					tips.addClass("validate_error");
					text_tips.text("Please select at least one menu.");
				}									
				else {
					bValid = true;
				}
				
				if (bValid) {
				
					var dataString = "role_name=" + role_name + "&description=" + description + "&menu_items=" + menu_items;	
					$.ajax({
						type: "POST",
						url: "../php/exe/role_add.php",
						data: dataString,
						beforeSend: function() {
							tips.addClass("validate_proccess");		
							text_tips.text("inserting.. please wait... ");
							ajaxInProgress = true;
						},
						success: function(data, textStatus, xhr) {
							if (data.substr(0, 7) == "Success") {
								tips.removeClass("validate_proccess").addClass("validate_done");
								text_tips.text("Data was added successfully.");
								roleTable.fnDraw();
								reloadRoles();                     
							}
							else {
								tips.removeClass("validate_proccess").addClass("validate_error");
								text_tips.text(data);
							}	
						},
						error: function(xhr, textStatus, errorThrown) {
							tips.removeClass("validate_proccess").addClass("validate_error");
							text_tips.text(errorThrown);
						},
						complete: function(xhr, textStatus) {
							ajaxInProgress = false;
						}										
					});
								
				}
				
				//$(this).dialog("close");
			},
			Close: function() {
				$("#validate-tips-4").removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
				$("#text-tips-3").text("");				
				$("#role_name").val("");	
				$("#description").val("");	
				$("input:checkbox.menu_item").each(function() {
					$(this).prop('checked', false);
				});
				
				$(this).dialog("close");
			}
		}
	});
	
	// role edit dialog
	$("#role_edit").dialog({
		modal: true,
		autoOpen: false,
		height:540,
		width:620,
		resizable: false,
		buttons: {
			Save: function() {
				var bValid = false;
				var	tips = $("#validate-tips-5");
				var	text_tips = $("#text-tips-4");
				tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
				// get form variables
				var id_role = $("#id_role").val();		
				var role_name = $("#role_name_e").val();	
				var description = $("#description_e").val();	
				var menu_items = getMenuItems_e();
						
				// validation here
				if (role_name == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter Role Name.");
					$("#role_name_e").focus();
				}	
				else if (menu_items == "") {
					tips.addClass("validate_error");
					text_tips.text("Please select at least one menu.");
				}									
				else {
					bValid = true;
				}
				
				if (bValid) {
					
					var dataString = "id=" + id_role + "&role_name=" + role_name + "&description=" + description + "&menu_items=" + menu_items;
					$.ajax({
						type: "POST",
						url: "../php/exe/role_update.php",
						data: dataString,
						beforeSend: function() {
							tips.addClass("validate_proccess");		
							text_tips.text("updating.. please wait... ");
							ajaxInProgress = true;
						},
						success: function(data, textStatus, xhr) {
							if (data.substr(0, 7) == "Success") {
								tips.removeClass("validate_proccess").addClass("validate_done");
								text_tips.text("Data was updated successfully.");
								roleTable.fnDraw();	                     
							}
							else {
								tips.removeClass("validate_proccess").addClass("validate_error");
								text_tips.text(data);
							}	
						},
						error: function(xhr, textStatus, errorThrown) {
							tips.removeClass("validate_proccess").addClass("validate_error");
							text_tips.text(errorThrown);
						},
						complete: function(xhr, textStatus) {
							ajaxInProgress = false;
						}
					});
								
				}
				
				//$(this).dialog("close");
			},
			Close: function() {
				$("#validate-tips-5").removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
				$("#text-tips-4").text("");				
				$("#role_name").val("");	
				$("#description").val("");	
				$("input:checkbox.menu_item").each(function() {
					$(this).prop('checked', false);
				});
				
				$(this).dialog("close");
			}
		}
	});
	
	// role delete dialog
	$("#role_delete").dialog({
		modal: true,
		autoOpen: false,
		//height:180,
		//width:180,
		resizable: false,
		buttons: {
			Yes: function() {	
				var id = $("#id_role").val();
				
				var dataString = "id=" + id;		
				$.ajax({
					type: "POST",
					url: "../php/exe/role_delete.php",
					data: dataString,
					beforeSend: function() {
						ajaxInProgress = true;
					},
					success: function(data, textStatus, xhr) {
						if (data.substr(0, 7) == "Success") {
							// cannot close dialog from here, just do reload table		
							roleTable.fnDraw();
							userTable.fnDraw();
							reloadRoles();                       
						}
						else {
							// do nothing
						}	
					},
					error: function(xhr, textStatus, errorThrown) {
					},
					complete: function(xhr, textStatus) {
						ajaxInProgress = false;
					}
				});			
				
				$(this).dialog("close");
			},
			No: function() {
				$(this).dialog("close");
			}
		}
	});
	
	// user add dialog
	$("#user_add").dialog({
		modal: true,
		autoOpen: false,
		height:310,
		width:500,
		resizable: false,
		buttons: {
			Save: function() {
				var	tips = $("#validate-tips-2");
				var	text_tips = $("#text-tips");
				tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
				// get form variables
				var user_id = $("#user_id").val();	
				var passwd = $("#passwd").val();	
				var passwd_confirm = $("#passwd_confirm").val();	
				var user_name = $("#user_name").val();	
				//var email = $("#email").val();
				//var departement = $("#departement option:selected").val();
				var email = "none";
				var departement = "none";
				var branch_code = $("#branch_code option:selected").val();
			
				// validation here
				if (user_id == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter User ID.");
					$("#user_id").focus();
				}	
				else if (passwd == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter Password.");
					$("#passwd").focus();
				}					
				else if (passwd_confirm == "") {
					tips.addClass("validate_error");
					text_tips.text("Please re-type Password.");
					$("#passwd_confirm").focus();
				}
				else if (passwd != passwd_confirm) {
					tips.addClass("validate_error");
					text_tips.text("Re-type Password didn't match with Password.");
					$("#passwd_confirm").focus();
				}
				else if (user_name == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter User Name.");
					$("#user_name").focus();
				}
				else if (branch_code == "") {
					tips.addClass("validate_error");
					text_tips.text("Please select Store Initial.");
					$("#branch_code").focus();
				}
				else {
					
					var dataString = "user_id=" + user_id + "&passwd=" + passwd + "&passwd_confirm=" + passwd_confirm + "&user_name=" + user_name + "&email=" + email + "&departement=" + departement + "&branch_code=" + branch_code;
					$.ajax({
						type: "POST",
						url: "../php/exe/user_add.php",
						data: dataString,
						beforeSend: function() {
							tips.addClass("validate_proccess");		
							text_tips.text("inserting.. please wait... ");
							ajaxInProgress = true;
                        },
						success: function(data, textStatus, xhr) {
							if (data.substr(0, 7) == "Success") {
								tips.removeClass("validate_proccess").addClass("validate_done");
								text_tips.text("Data was added successfully.");
								userTable.fnDraw();
								reloadRolelessUsers();                        
							}
							else {	
								tips.removeClass("validate_proccess").addClass("validate_error");
								text_tips.text(data);
							}	
                        },
						error: function(xhr, textStatus, errorThrown) {
							tips.removeClass("validate_proccess").addClass("validate_error");
							text_tips.text(errorThrown);
                        },
                        complete: function(xhr, textStatus) {
                            ajaxInProgress = false;
                        }
					});
				}
				
				//$(this).dialog("close");
			},
			Close: function() {
				$("#validate-tips-2").removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
				$("#text-tips").text("");
				$("#user_id").val("");	
				$("#passwd").val("");	
				$("#passwd_confirm").val("");	
				$("#user_name").val("");
				$("#branch_code").val("");
				
				$(this).dialog("close");
			}
		}
	});
	
	// user edit dialog
	$("#user_edit").dialog({
		modal: true,
		autoOpen: false,
		height:340,
		width:500,
		resizable: false,
		buttons: {
			Save: function() {
				var	tips = $("#validate-tips-3");
				var	text_tips = $("#text-tips-2");
				tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
				// get form variables
				var id_user = $("#id_user").val();
				var user_id = $("#user_id_e").val();		
				var user_name = $("#user_name_e").val();	
				var email = "none";
				var departement = "none";
				var role_name = $("#user_role_name_e option:selected").val();	
				var active = $("#active_e option:selected").val();	
				var isPasswdReset = $("#isPasswdReset").prop("checked");
				var passwd_reset = $("#passwd_reset").val();
				var branch_code = $("#branch_code_e option:selected").val();
						
				// validation here
				if (user_id == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter User ID.");
					$("#user_id_e").focus();
				}				
				else if (user_name == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter User Name.");
					$("#user_name_e").focus();
				}
				else if (isPasswdReset && passwd_reset == "") {
					tips.addClass("validate_error");
					text_tips.text("Please enter Reset Password.");
					$("#passwd_reset").focus();
				}
				else if (branch_code == "") {
					tips.addClass("validate_error");
					text_tips.text("Please select Store Initial.");
					$("#branch_code_e").focus();
				}
				else {
					
					var dataString = "id=" + id_user + "&user_id=" + user_id + "&user_name=" + user_name + "&email=" + email + "&departement=" + departement  +
									 "&active=" + active + "&role_name=" + role_name + "&isPasswdReset=" + isPasswdReset + "&passwd_reset=" + passwd_reset + "&branch_code=" + branch_code;
					$.ajax({
						type: "POST",
						url: "../php/exe/user_update.php",
						data: dataString,
						beforeSend: function() {
							tips.addClass("validate_proccess");		
							text_tips.text("updating.. please wait... ");
							ajaxInProgress = true;
                        },
						success: function(data, textStatus, xhr) {
							if (data.substr(0, 7) == "Success") {
								tips.removeClass("validate_proccess").addClass("validate_done");
								text_tips.text("Data was updated successfully.");
								userTable.fnDraw();
								reloadRolelessUsers();                       
							}
							else {	
								tips.removeClass("validate_proccess").addClass("validate_error");
								text_tips.text(data);
							}	
                        },
						error: function(xhr, textStatus, errorThrown) {
							tips.removeClass("validate_proccess").addClass("validate_error");
							text_tips.text(errorThrown);
                        },
                        complete: function(xhr, textStatus) {
                            ajaxInProgress = false;
                        }												
					});
				}a
				
				//$(this).dialog("close");
			},
			Close: function() {
				$("#validate-tips-3").removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
				$("#text-tips-2").text("");
				$("#user_id").val("");	
				$("#user_name").val("");
				$("#isPasswdReset").prop("checked", false);
				$("#passwd_reset").val("");
				$("#passwd_reset").prop("disabled", true);
				$("#branch_code").val("");
				
				$(this).dialog("close");
			}
		}
	});
	
	// user delete dialog
	$("#user_delete").dialog({
		modal: true,
		autoOpen: false,
		//height:180,
		//width:180,
		resizable: false,
		buttons: {
			Yes: function() {	
				var id = $("#id_user").val();
				
				var dataString = "id=" + id;		
				$.ajax({
					type: "POST",
					url: "../php/exe/user_delete.php",
					data: dataString,
					beforeSend: function() {
						ajaxInProgress = true;
					},
					success: function(data, textStatus, xhr) {
						if (data.substr(0, 7) == "Success") {
							// cannot close dialog from here, just do reload table		
							userTable.fnDraw();	
							reloadRolelessUsers();                     
						}
						else {	
							// do nothing
						}	
					},
					error: function(xhr, textStatus, errorThrown) {
					},
					complete: function(xhr, textStatus) {
						ajaxInProgress = false;
					}		
				});			
				
				$(this).dialog("close");
			},
			No: function() {
				$(this).dialog("close");
			}
		}
	});
	
	// backup delete dialog
	$("#backup_delete").dialog({
		modal: true,
		autoOpen: false,
		height:220,
		width:330,
		resizable: false,
		buttons: {
			Yes: function() {	
				// get form variables
				var backup_name = $("#backup_name").val();		
			
				var dataString = "backup_name=" + backup_name;	
				$.ajax({
					type: "POST",
					url: "../php/exe/backup_delete.php",
					data: dataString,
					beforeSend: function() {
						ajaxInProgress = true;
					},
					success: function(data, textStatus, xhr) {
						if (data.substr(0,5) == "Error") {
							// do nothing	
						}
						else {
							$("#validate-tips-7").removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
							$("#text-tips-6").text("");
							listingBackup();
						}	
					},
					error: function(xhr, textStatus, errorThrown) {
					},
					complete: function(xhr, textStatus) {
						ajaxInProgress = false;
					}	
				});			
				
				$(this).dialog("close");
			},
			No: function() {
				$(this).dialog("close");
			}
		}
	});
	
	$("#chkAll").click(function() {
		$(".menu_item").prop("checked", $(this).prop("checked"));	
	});
	
	$("#chkAll_e").click(function() {
		$(".menu_item_e").prop("checked", $(this).prop("checked"));	
	});
			
});

function doDownload(v) {
	//location.href="?op=admin&ac=getbackup&filename=" + v;
	location.href="/admin/getbackup/" + v + ".html";
}

function doBackup() {
	var	tips = $("#validate-tips-7");
	var	text_tips = $("#text-tips-6");
	tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
	tips.addClass("validate_proccess");		
	text_tips.text("backing up.. please wait... ");
				
	$.ajax({
		type: "POST",
		url: "../php/exe/db_backup.php",
		data: null,
		beforeSend: function() {
			ajaxInProgress = true;
		},
		success: function(data, textStatus, xhr) {
			if (data.substr(0,5) == "Error") {
				tips.removeClass("validate_proccess").addClass("validate_error");
				text_tips.text(data);					
			}
			else if (data.indexOf("exec") != -1) {
				tips.removeClass("validate_proccess").addClass("validate_error");
				text_tips.text("Error: Failed to backup database. Function exec() has been disabled for security reasons.");					
			}	
			else {
				tips.removeClass("validate_proccess").addClass("validate_done");
				text_tips.text(data);
				listingBackup();
			}
		},
		error: function(xhr, textStatus, errorThrown) {
			tips.removeClass("validate_proccess").addClass("validate_error");
			text_tips.text(errorThrown);
		},
		complete: function(xhr, textStatus) {
			ajaxInProgress = false;
		}	
	});
					
	
}

function goDeleteBackup(v) {
	$("#backup_to_delete").html(v);
	$("#backup_name").val(v);
		
	$("#backup_delete").dialog("open");
}


// --- assign role --- //

function goAssignRole() {
	var	tips = $("#validate-tips-6");
	var	text_tips = $("#text-tips-5");
	tips.removeClass("validate_error").removeClass("validate_warning").removeClass("validate_done");
	
	// get form variables
	var user_id = $("#assign_role_user_id option:selected").val();		
	var role_name = $("#assign_role_role_name option:selected").val();		
	
	// validation here
	if (user_id == "") {
		tips.addClass("validate_error");
		text_tips.text("Please select User.");
	}					
	else if (role_name == "") {
		tips.addClass("validate_error");
		text_tips.text("Please select Role.");
	}
	else {
		
		var dataString = "user_id=" + user_id + "&role_name=" + role_name;
		$.ajax({
			type: "POST",
			url: "../php/exe/assign_role.php",
			data: dataString,
			beforeSend: function() {
				tips.addClass("validate_proccess");		
				text_tips.text("assigning.. please wait... ");
				ajaxInProgress = true;
			},
			success: function(data, textStatus, xhr) {
				if (data.substr(0, 7) == "Success") {
					tips.removeClass("validate_proccess").addClass("validate_done");
					text_tips.text("Role assigned successfully.");
					userTable.fnDraw();		
					reloadRolelessUsers();                   
				}
				else {	
					tips.removeClass("validate_proccess").addClass("validate_error");
					text_tips.text(data);
				}	
			},
			error: function(xhr, textStatus, errorThrown) {
				tips.removeClass("validate_proccess").addClass("validate_error");
				text_tips.text(errorThrown);
			},
			complete: function(xhr, textStatus) {
				ajaxInProgress = false;
			}		
		});
	}
	
}

// --- eo assign role --- //

function getMenuItems() {
	var text = "";
	
	$("input:checkbox.menu_item:checked").each(function() {
		text += $(this).val() + "#";
	});
	
	text = text.substr(0, text.length-1);

	return text;
}

function getMenuItems_e() {
	var text = "";
	
	$("input:checkbox.menu_item_e:checked").each(function() {
		text += $(this).val() + "#";
	});
	
	text = text.substr(0, text.length-1);

	return text;
}

function goAddRole() {
	$("#role_add").dialog("open");
}

function goEditRole(v) {
	var aVal = v.split("#");
	
	$("#id_role").val(aVal[0]);
	$("#role_name_e").val(aVal[1]);
	$("#description_e").val(aVal[2]);
	
	var dataString = "id_role=" + aVal[0];		
	$.ajax({
		type: "POST",
		url: "../php/exe/role_detail_load.php",
		data: dataString,
		beforeSend: function() {
			ajaxInProgress = true;
		},
		success: function(data, textStatus, xhr) {
			if (data.substr(0,5) == "Error") {
				// do nothing	
			}
			else {
				// clear table first
				$("#tbl_menu_e tr:gt(0)").remove();		
				//$("#tbl_menu_e tr:eq(0)").remove();		
				// build rows
				$("#tbl_menu_e > tbody:last").append(data);
				
				$("#role_edit").dialog("open");
			}	
		},
		error: function(xhr, textStatus, errorThrown) {
		},
		complete: function(xhr, textStatus) {
			ajaxInProgress = false;
		}		
	});
		
	
}

function goDeleteRole(v) {
	var aVal = v.split("#");
	
	$("#id_role").val(aVal[0]);
	$("#role_to_delete").html(aVal[1]);
		
	$("#role_delete").dialog("open");
}

function goAddUser() {
	$("#user_add").dialog("open");
}

function goEditUser(v) {
	var aVal = v.split("#");
	
	$("#id_user").val(aVal[0]);
	$("#user_id_e").val(aVal[1]);
	$("#user_name_e").val(aVal[2]);
	$("#email_e").val(aVal[3]);
	$("#branch_code_e").val(aVal[4]);
	$("#departement_e").val(aVal[5]);
	$("#user_role_name_e").val(aVal[6]);
	$("#active_e").val((aVal[7] == "Y" ? "1" : "0"));
		
	$("#user_edit").dialog("open");
}

function goDeleteUser(v) {
	var aVal = v.split("#");
	
	$("#id_user").val(aVal[0]);
	$("#user_to_delete").html(aVal[1]);
		
	$("#user_delete").dialog("open");
}

// --- user --- //

function loadUserTable() {
	userTable = $('#tbl_user').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"bJQueryUI": true,
		"bStateSave": false,
		"sAjaxSource": "../php/exe/user_list.php",
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
			 {"bSearchable": false, "bVisible": false, "asSorting": [ "desc", "asc" ]},
			 {"bSearchable": false, "bVisible": false, "asSorting": [ "desc", "asc" ]},
			 null,
			 null,
			 null,
			 {"bSearchable": false, "bSortable": false},
			 {"bSearchable": false, "bSortable": false}			
		],
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
			"sSearch": "Search:&nbsp; "
		},
		"iDisplayLength": 10/*,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			if ( jQuery.inArray(aData[0], gaiSelected) != -1 )
			{
				$(nRow).addClass('row_selected');
			}
			return nRow;
		} */
	} );
}

// --- role --- //

function loadRoleTable() {
	
	roleTable = $('#tbl_role').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"bJQueryUI": true,
		"bStateSave": false,
		"sAjaxSource": "../php/exe/role_list.php",
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
			 {"bSearchable": false, "bSortable": false},
			 {"bSearchable": false, "bSortable": false}			
		],
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
			"sSearch": "Search:&nbsp; "
		},
		"iDisplayLength": 10/*,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			if ( jQuery.inArray(aData[0], gaiSelected) != -1 )
			{
				$(nRow).addClass('row_selected');
			}
			return nRow;
		} */
	} );
	
	/* Add events */
	//$("#tbl_role tbody").on("click", "tr td", function () {
		// get column index		
		
		//var col = roleTable.fnGetPosition(this)[1];  // returns array of 3 indexes [row, col_visible, col_all]
		
		//if (col == 0) {
			//var role_name = $(this).text();			
			//showInfo(role_name);
		//}										
	//});

	
}

// --- backup --- //

function listingBackup() {
	
	$.ajax({
		type: "POST",
		url: "../php/exe/listing_backup.php",
		data: null,
		beforeSend: function() {
			ajaxInProgress = true;
		},
		success: function(data, textStatus, xhr) {
			if (data.substr(0,5) == "Error") {
				// do nothing	
			}
			else {
				// clear table first				
				$("#tbl_backup tr:gt(0)").remove();		
				// build rows
				$("#tbl_backup > tbody:last").append(data);
				//alert("ga gagal");
			}	
		},
		error: function(xhr, textStatus, errorThrown) {
		},
		complete: function(xhr, textStatus) {
			ajaxInProgress = false;
		}	
	});

}


function sortoptions(sort) {
	var $this = $(this);
	// sort
	$this.sortOptions(sort.dir == "asc" ? true : false);
}

function reloadRolelessUsers() {
	// clear option first
	$("#assign_role_user_id").removeOption(/./);
	$("#assign_role_user_id").addOption("", " --- Select user --- ");	
	$("#assign_role_user_id").ajaxAddOption("../php/exe/user_load_roleless.php", {}, false, sortoptions, [{"dir":"asc"}]);
}

function reloadRoles() {
	// clear option first
	$("#assign_role_role_name").removeOption(/./);
	$("#assign_role_role_name").addOption("", " --- Select role --- ");	
	$("#assign_role_role_name").ajaxAddOption("../php/exe/role_load.php", {}, false, sortoptions, [{"dir":"asc"}]);
}