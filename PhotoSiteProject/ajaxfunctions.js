"use strict";
// This file contains ajax functions to dynamically do things
function keystruckRegister(event) {
    var msg = document.getElementById("msg_register");
    var email = document.getElementById("new_email").value;
    if(email.length == 0) {
	msg.textContent = "";
	return;
    } else if(email.length > 30) {
	msg.textContent = "Email cannot exceed 30 characters";
	return;
    }
    
    $.get("validateusername.php", { username: email }, function(data) {
	switch((data.valueOf()).trim()) {
	    case "ok":
		msg.textContent = "";
		break;
	    case "nogood":
		msg.textContent = "Invalid email format";
		break;
	    case "inuse":
		msg.textContent = "Email is taken";
		break;
	    default:
		msg.textContent = "Error";
	}
    });
}

function keystruckLogin(event) {
    var msg = document.getElementById("msg_login");
    msg.textContent = "";
}

function inputConfirmPwd(event) {
    var old_password = document.getElementById("old_password").value;
    var new_password = document.getElementById("new_password").value;
    var new_confirm = document.getElementById("new_confirm").value;
    var msg = document.getElementById("msg_changepwd");

    if(new_password.length == 0 || new_confirm.length == 0) {
	msg.textContent = "";
    } else if(old_password == new_password) {
	msg.textContent = "New password cannot be the same as old password"; 
    } else if(new_password != new_confirm) {
	msg.textContent = "Confirmed password must match new password";
    } else {
	msg.textContent = "";
    }
}

function inputConfirmPwdReset(event) {
    var new_password = document.getElementById("new_password").value;
    var new_confirm = document.getElementById("new_confirm").value;
    var msg = document.getElementById("msg_resetpwd");
    
    if(new_password.length == 0 || new_confirm.length == 0) {
	msg.textContent = "";
    } else if(new_password != new_confirm) {
	msg.textContent = "Confirmed password must match new password";
    } else {
	msg.textContent = "";
    }
}
