<?php
session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

if(isset($_GET["logout"])) {
    session_unset();
}
?>
<!-- This page is a start page to register, login, or navigate the site --!>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Start Page
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
<h3 class="center">Start Page</h3>
<hr>

<?php
if (!(isset($_SESSION["user_id"]))) { 
    if(isset($_SESSION["message"])) {
	echo("<p style='margin-left:12%; color:red'>" . $_SESSION["message"] . "</p>");
	unset($_SESSION["message"]);
    }
?>
<form action="
<?php
    if(isset($_POST["register"])
    && isset($_POST["new_email"]) && isset($_POST["new_password"])) {
	// put post vars in local vars
	$username = $_POST["new_email"];
	$password = $_POST["new_password"];
	if($_POST["new_email"] == "") {
	    $_SESSION["failed_register"] = "Email field is required.";
	} else if($_POST["new_password"] == "") {
	    $_SESSION["failed_register"] = "Password field is required.";
	} else if(usernameIsLegal($username) == -1) {
	    $_SESSION["failed_register"] = "Did not register invalid email.";
	} else if(usernameExists($username) == 1) {
	    $_SESSION["failed_register"] = 
		"Did not register already existing email.";
	} else {
	    addUser($username, $password);
	    // get the new user's id so we can log them in
	    $user_id = getUserIDFromUsername($username);
	    if($user_id > -1) {
	        // add user's id to session
	        $_SESSION["user_id"] = $user_id;
	    } else {
	        $_SESSION["failed_register"] = "Failed to login new user.";
	    }
	}
	// reload the current page
	header("Location: start.php");
	exit();
    }
?>
" method="post">
<fieldset class="userfieldset">
<legend>New user? Register for an account</legend>
<table>
    <tr>
        <td>Email:</td>
	<td><input name="new_email" id="new_email" type="text"/></td>
	<td id="msg_register" rowspan="2" style="color:red">
<?php if(isset($_SESSION["failed_register"])) {
    echo($_SESSION["failed_register"]);
    unset($_SESSION["failed_register"]);
    }
?>      </td>
    </tr>
    <tr>
	<td>Password:</td>
	<td><input name="new_password" id="new_password" type="password"/></td>
    </tr>
    <tr>
	<td><input type="submit" name="register" value="Register"/></td>
    </tr>
</table>
</fieldset>
</form>
<br>

<form action="
<?php
    if(isset($_POST["signin"])
    && isset($_POST["email"]) && isset($_POST["password"])) {
	// put post vars in local vars
	$username = $_POST["email"];
	$password = $_POST["password"];
	if($_POST["email"] == "") {
	    $_SESSION["failed_login"] = "Email field is required.";
	} else if($_POST["password"] == "") {
	    $_SESSION["failed_login"] = "Password field is required.";
	} else {
	    // try to get user id
	    $user_id = getUserID($username, $password);
	    if($user_id > -1) {
		$_SESSION["user_id"] = $user_id;
	    } else {
	        // login failed
	        $_SESSION["failed_login"] = 
		    "Couldn't find a user with that 
		    email/password combination.";
	    }
	}
	// Reload the current page
	header("Location: start.php");
	exit();
    }
?>
" method="post">
<fieldset class="userfieldset">
<legend>Already have an account? Sign in</legend>
<table>
    <tr>
        <td>Email:</td>
	<td><input name="email" id="email" type="text"/></td>
	<td id="msg_login" rowspan="2" style="color:red">
<?php if(isset($_SESSION["failed_login"])) {
    echo($_SESSION["failed_login"]);
    unset($_SESSION["failed_login"]);
    }
?>	</td>
    </tr>
    <tr>
        <td>Password:</td>
        <td><input name="password" id="password" type="password"/></td>
    </tr>
    <tr>
	<td><input type="submit" name="signin" value="Sign In"/></td>
	<td><a href="recoverpassword.php">Forgot password?</a></td>
    </tr>
</table>
</fieldset>
</form>
<br>

<?php } else {
    $username = getUser($_SESSION["user_id"])->username;
    echo("<table id='navbar'>");
    echo("<tr>");
    echo("<td>Logged in as " . $username . "</td>");
    echo("<td><a href='start.php?logout'>Log Out</a></td>");
    echo("<td><a href='changepassword.php'>Change Password</a></td>");
    echo("</tr>");
    echo("</table>");
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
<a href="http://validator.w3.org/check/referer">HTML5</a> /

<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">
    CSS3 </a>
</span>
</footer>

<script>
    var logged_in = "<?php echo(isset($_SESSION["user_id"]) ? "1" : "0"); ?>";
    if(logged_in == false) {
	new_email_box = document.getElementById("new_email");
	new_email_box.addEventListener("keyup", keystruckRegister);
	email_box = document.getElementById("email");
	email_box.addEventListener("keyup", keystruckLogin);
	password_box = document.getElementById("password");
	password_box.addEventListener("keyup", keystruckLogin);
    }
</script>
</body>
</html>
