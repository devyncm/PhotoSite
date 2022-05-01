<?php
session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

if(isset($_GET["logout"])) {
    session_unset();
} else if(isset($_GET["deleteaccount"]) && isset($_SESSION["user_id"])) {
    deleteUser($_SESSION["user_id"]);
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
if(isset($_SESSION["message"])) {
    // put message vars in local vars
    $text = $_SESSION["message"][0];
    $color = $_SESSION["message"][1];
    echo("<p id='message' style='color: "
        . $color . "'>" . $text . "</p>");
    unset($_SESSION["message"]);
}
if (!(isset($_SESSION["user_id"]))) { 
    echo("<form action='");
    if(isset($_POST["register"])
    && isset($_POST["new_email"]) && isset($_POST["new_password"])) {
	// put post vars in local vars
	$username = $_POST["new_email"];
	$password = $_POST["new_password"];
	if($username == "") {
	    $_SESSION["failed_register"] = "Email field is required.";
	} else if($password == "") {
	    $_SESSION["failed_register"] = "Password field is required.";
	} else if(!(usernameIsLegal($username))) {
	    $_SESSION["failed_register"] = "Did not register invalid email.";
	} else if(usernameExists($username)) {
	    $_SESSION["failed_register"] = 
		"Did not register already existing email.";
	} else {
	    addUser($username, $password);
	    // get the new user's id so we can log them in
	    $user_id = getUserIDFromUsername($username);
	    if($user_id) {
	        // add user's id to session
	        $_SESSION["user_id"] = $user_id;
	    } else {
	        $_SESSION["failed_register"] = "Failed to login new user.";
	    }
	}
	// reload the current page
	header("Location: start");
	exit();
    }
    echo("' method='post'>");
?>
<fieldset class="userfieldset">
<legend>New user? Register for an account</legend>
<table>
    <tr>
        <td>Email:</td>
	<td><input name="new_email" id="new_email" size="30" maxlength="31" 
	    type="text"/></td>
	<td id="msg_register" rowspan="2" style="color:red">
<?php if(isset($_SESSION["failed_register"])) {
    echo($_SESSION["failed_register"]);
    unset($_SESSION["failed_register"]);
    }
?>      </td>
    </tr>
    <tr>
	<td>Password:</td>
	<td><input name="new_password" id="new_password" size="30" 
	    maxlength="100" type="password"/></td>
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
	if($username == "") {
	    $_SESSION["failed_login"] = "Email field is required.";
	} else if($password == "") {
	    $_SESSION["failed_login"] = "Password field is required.";
	} else {
	    // try to get user id
	    $user_id = getUserID($username, $password);
	    if($user_id) {
		$_SESSION["user_id"] = $user_id;
	    } else {
	        // login failed
	        $_SESSION["failed_login"] = 
		    "Couldn't find a user with that 
		    email/password combination.";
	    }
	}
	// Reload the current page
	header("Location: start");
	exit();
    }
?>
" method="post">
<fieldset class="userfieldset">
<legend>Already have an account? Sign in</legend>
<table>
    <tr>
        <td>Email:</td>
	<td><input name="email" id="email" size="30" maxlength="30" 
	    type="text"/></td>
	<td id="msg_login" rowspan="2" style="color:red">
<?php if(isset($_SESSION["failed_login"])) {
    echo($_SESSION["failed_login"]);
    unset($_SESSION["failed_login"]);
    }
?>	</td>
    </tr>
    <tr>
        <td>Password:</td>
	<td><input name="password" id="password" size="30" maxlength="100" 
	    type="password"/></td>
    </tr>
    <tr>
	<td><input type="submit" name="signin" value="Sign In"/></td>
	<td><a href="recoverpassword">Forgot password?</a></td>
    </tr>
</table>
</fieldset>
</form>
<br>
<p class='center'>Visit the <a href='gallery'>gallery</a> 
  to see what amazing photos people are sharing.</p>
<?php
    $photo_count = countTodaysPhotos();
    if($photo_count) {
	$newest_photo = getNewestPhoto();
	$plural = "";
	if($photo_count > 1) {
	    $plural = "s";
	}
	echo("<p class='center'><b>$photo_count new photo$plural 
	    uploaded today!</b></p>");
	echo("<div class='center'><a href='photo?id=$newest_photo->photo_id'>
	    <img class='bordered' style='width: 8%' 
		src='$newest_photo->filelocation'></a></div>");
    }
?>
<h2 class='center'>Once you sign up, you can ...</h2>
<ul style='display: table; margin: 0 auto'>
  <li>Upload photos</li>
  <li>Customize and manage your profile</li>
  <li>Leave comments on photos</li>
  <li>... and more!</li>
</ul>
<br>
<?php } else {
    $username = getUser($_SESSION["user_id"])->username;
    echo("<table id='navbar'>
	<tr>
	<td>Logged in as " . $username . "</td>
	<td><a href='profile'>Your Profile</a></td>
	<td><a href='upload'>Upload</a></td>
	<td><a href='gallery'>Gallery</a></td>
	<td><a href='start?logout'>Log Out</a></td>
	</tr>
	</table>");
?>
<h3 class="center">Welcome to the website!</h3>
<div class='container'>
  <p>View <a href='profile'>your profile</a> to browse your uploaded photos 
	and manage your account.</p>
  <p>The <a href='upload'>upload</a> page lets you upload a new photo, 
	visible on your profile and in the gallery.</p>
  <p>Visit the <a href='gallery'>gallery</a> to see what everyone's sharing 
	with the world.</p>
</div>
<?php
} ?>
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
