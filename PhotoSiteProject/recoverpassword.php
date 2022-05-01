<?php

session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

?>
<!-- This page allows a recovery email to be sent. --!>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Recover Password
  </title>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Recover Password</h3>
<hr>
<?php
if(isset($_SESSION["user_id"])) {
    header('Location: start');
    exit();
} else {
    if(isset($_SESSION["message"])) {
	// put message vars in local vars
	$text = $_SESSION["message"][0];
	$color = $_SESSION["message"][1];
	echo("<p id='message' style='color: "
	    . $color . "'>" . $text . "</p>");
	unset($_SESSION["message"]);
    }
    echo("<form action='");
    if(isset($_POST["email"])) {
	$email = $_POST["email"];
	if($email == "") {
	    $_SESSION["failed_recover"] = "Email field is required.";
	} else {
	    $id = getUserIDFromUsername($email);
	    if(!($id)) {
		$_SESSION["failed_recover"] = "Email is not registered.";
	    } else {
		// remove user's old tokens
		deleteUserTokens($id);    
		// create and add token to database
		$now = time();
		$token = bin2hex(random_bytes(30));
		addUserToken($id, $token, $now);
		// put together and send the email
		$host = "elvis.rowan.edu";
		$site = "Project";
		$resetsite = 
		    "/~melend53/PhotoSiteProject/resetpassword.php";
		$myemail = "noreply@elvis.rowan.edu";
		// put together the email
		$subject = "$site: Password Recovery";
		$headers = "From: $myemail \r\n" .
		    "Reply-To: $myemail \r\n" .
	    	    'X-Mailer: PHP/' . phpversion();
		$message = "Forgot your password at $site?\r\n\r\n" .
		    "To reset your password, please click this link:\r\n\r\n" .
		    "http://$host$resetsite?token=$token \r\n" .
		    "This link will expire in 1 hour. \r\n\r\n" .
		    "(If you did not request this, \r\n" .
		    "just ignore this message.)\r\n";

		mail($email, $subject, $message, $headers);
		$_SESSION["message"] = array(
		    "A recovery link was sent to $email.",
		    "green",
		);
	    }
	}
	// Reload the current page
	header('Location: recoverpassword');
	exit();
    }
    echo("' method='post'>");
?>
<fieldset class="userfieldset">
<legend>Enter your email to send a recovery link</legend>
<table>
    <tr>
	<td>Email:</td>
	<td><input name="email" size="30" maxlength="30" type="text"/></td>
	<td style="color:red">
<?php
    if(isset($_SESSION["failed_recover"])) {
	echo($_SESSION["failed_recover"]);
	unset($_SESSION["failed_recover"]);
    }
?>	</td>
    </tr>
    <tr>
	<td><input type="submit" name="submit" value="Send"/></td>
    </tr>
</table>
</fieldset>
</form>
<br>
<div class="center"><a href="start">Back to Login</a></div>
<?php
} ?>
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

</body>
</html>
