// JavaScript Document

function goValidate() {
	if (document.getElementById("user_id").value=="") {
		document.getElementById("user_id").focus();
	}
	else if (document.getElementById("passwd").value=="") {
		document.getElementById("passwd").focus();
	}
	else {
		document.getElementById("flogin").submit();
	}
}

function goValidate_2(e) {
	var keynum;
	//var keychar;
	
	if (window.event) { // IE	
		keynum = e.keyCode;
	}
	else if (e.which) { // Netscape/Firefox/Opera	
		keynum = e.which;
	}
	//keychar = String.fromCharCode(keynum);
	
	if (keynum == 13) {	
		goValidate();
		//if (document.getElementById("passwd").value!="") {
			//document.getElementById("flogin").submit();
		//}		
	}
}

function goValidate_3(e) {
	var keynum;
	//var keychar;
	
	if (window.event) { // IE	
		keynum = e.keyCode;
	}
	else if (e.which) { // Netscape/Firefox/Opera	
		keynum = e.which;
	}
	//keychar = String.fromCharCode(keynum);
	
	if (keynum == 13) {	
		goValidate();
		//if (document.getElementById("user_id").value!="") {
			//document.getElementById("passwd").focus();
		//}
	}
}
