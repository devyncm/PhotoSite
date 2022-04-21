<?php

session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

?>
<!-- This page allows a password to be reset. --!>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Reset Password
  </title>
  <script src=
  "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="ajaxfunctions.js"></script>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Reset Password</h3>
<hr>
<?php
if(isset($_SESSION["user_id"])) {
    header('Location: start.php');
    exit();
} else if(!(isset($_GET["token"]))) {
    $_SESSION["message"] = array(
	"No recovery token was provided.",
	"red",
    );
    header('Location: recoverpassword.php');
    exit();
} else {
    $id = checkUserToken($_GET["token"]);
    if($id < 0) {
	$_SESSION["message"] = array(
	    "The provided recovery token was invalid or expired.",
	    "red",
	);
	header('Location: recoverpassword.php');
	exit();
    }
    if(isset($_SESSION["message"])) {
        // put message vars in local vars
        $text = $_SESSION["message"][0];
        $color = $_SESSION["message"][1];
        echo("<p style='margin-left:12%; color:"
            . $color . "'>" . $text . "</p>");
        unset($_SESSION["message"]);
    }
    echo("<form action='");
    if(isset($_POST["new_password"]) && isset($_POST["new_confirm"])) {
	// put post vars in local vars
	$new_password = $_POST["new_password"];
	$new_confirm = $_POST["new_confirm"];
	if($new_password == "") {
	    $_SESSION["failed_resetpwd"] = "Old password is required.";
	} else if($new_confirm == "") {
	    $_SESSION["failed_resetpwd"] = "Please confirm new password.";
	} else if($new_password != $new_confirm) {
	    $_SESSION["failed_resetpwd"] = 
		"Your new password and confirmed new password didn't match.";
	} else if(checkPasswordMatch($new_password, $id) > 0) {
	    $_SESSION["failed_resetpwd"] = 
		"Your new password can't be the same as your old password.";
	} else {
	    changePassword($new_password, $id);
	    $_SESSION["message"] = array(
		"Your password was successfully reset.",
		"green",
	    );
	    // delete user's tokens
	    deleteUserTokens($id);
	    header('Location: start.php');
	    exit();
	}
	// Reload the current page
	header("Location: resetpassword.php?token=$token");
	exit();
    }
    echo("' method='post'>");
?>
<fieldset class="userfieldset">
<legend>Reset your password</legend>
<table>
    <tr>
	<td>New password:</td>
	<td><input name="new_password" id="new_password" size="30" 
	    maxlength="100" type="password"/></td>
	<td id="msg_resetpwd" rowspan="2" style="color:red">
<?php
    if(isset($_SESSION["failed_resetpwd"])) {
	echo($_SESSION["failed_resetpwd"]);
	unset($_SESSION["failed_resetpwd"]);
    }
?>	</td>
    </tr>
    <tr>
	<td>Confirm new password:</td>
	<td><input name="new_confirm" id="new_confirm" size="30" 
	    maxlength="100" type="password"/></td>
    </tr>
    <tr>
	<td><input type="submit" name="submit" value="Reset"/></td>
    </tr>
</table>
</fieldset>
</form>
<br>
<div class="center"><a href="start.php">Back to Login</a></div>
<?php
}
var_dump($_SESSION);
?>
<br>

<footer style="border-top: 1px solid blue">
 <a href="http://elvis.rowan.edu/~melend53/" 
    title="Link to my home page">
    D. Melendez 
 </a>

<span style="float: right;">
<a href="https://validator.w3.org/check?uri=referer"
   referrerpolicy="no-referrer-when-downgrade">HTML5</a> /
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">
    CSS3 </a>
</span>
</footer>
<script>
    var new_password_box = document.getElementById("new_password");
    var new_confirm_box = document.getElementById("new_confirm");
    new_password_box.oninput = inputConfirmPwdReset;
    new_confirm_box.oninput = inputConfirmPwdReset;
</script>
</body>
</html>
