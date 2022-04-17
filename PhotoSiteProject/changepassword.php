<?php

session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

?>
<!-- This page allows users to change their password. --!>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Change Password
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
<h3 class="center">Change Password</h3>
<hr>

<?php
if(!(isset($_SESSION["user_id"]))) {
    $_SESSION["message"] = "You need to be logged in to change your password.";
    header("Location: start.php");
    exit();
} else { ?>
<form action="" method="post">
<fieldset class="userfieldset">
<legend>Change your password</legend>
<table>
    <tr>
	<td>Old password:</td>
	<td><input name="old_password" id="old_password" type="password"/></td>
    </tr>
    <tr>
	<td>New password:</td>
	<td><input name="new_password" id="new_password" type="password"/></td>
	<td id="msg_changepwd" style="color:red">
<?php if(isset($_SESSION["failed_changepwd"])) {
    echo($_SESSION["failed_changepwd"]);
    unset($_SESSION["failed_changepwd"]);
    }
?>	</td>
    </tr>
    <tr>
	<td>Confirm new password:</td>
	<td><input name="new_confirm" id="new_confirm" type="password"/></td>
    </tr>
    <tr>
	<td><input type="submit" name="submit" value="Change"/></td>
    </tr>
</table>
</fieldset>
</form>
<?php } ?>
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
    var old_password_box = document.getElementById("old_password");
    var new_password_box = document.getElementById("new_password");
    var new_confirm_box = document.getElementById("new_confirm");
    old_password_box.oninput = inputConfirmPwd;
    new_password_box.oninput = inputConfirmPwd;
    new_confirm_box.oninput = inputConfirmPwd;
</script>
</body>
</html>
