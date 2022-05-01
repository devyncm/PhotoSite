<?php

session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Upload
  </title>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Upload</h3>
<hr>
<?php
$user_id = null;
if(!(isset($_SESSION["user_id"]))) {
$_SESSION["message"] = array(
    "You need to be logged in to upload photos.",
    "red",
);
header("Location: start");
exit();
} 
$user_id = $_SESSION["user_id"];
$username = getUser($user_id)->username;
echo("<table id='navbar'>
    <tr>
    <td>Logged in as " . $username . "</td>
    <td><a href='profile'>Your Profile</a></td>
    <td><a href='upload'>Upload</a></td>
    <td><a href='gallery'>Gallery</a></td>
    <td><a href='start?logout'>Log Out</a></td>
    </tr>
    </table>");

if(isset($_SESSION["message"])) {
    // put message vars in local vars
    $text = $_SESSION["message"][0];
    $color = $_SESSION["message"][1];
    echo("<p id='message' style='color: "
        . $color . "'>" . $text . "</p>");
    unset($_SESSION["message"]);
    }
?>
<p class="center">You can upload photos here (.png, .jpg, .jpeg, or .gif).</p>
<p class="center">Uploaded photos will appear on your profile 
  and in the gallery.</p>
<form enctype='multipart/form-data' method='post' action='
<?php
if(isset($_FILES["photo"])) {
    $caption = null;
    if(isset($_POST["caption"]) && trim($_POST["caption"]) != '') {
	$caption = trim($_POST["caption"]);
    }
    if(!(uploadPhoto($user_id, $_FILES["photo"], $caption))) {
	$_SESSION["failed_upload"] = "Your photo failed to upload or it is 
	    a duplicate. Make sure it is a compatible file type.";
    } else {
	$_SESSION["message"] = array(
	    "Your photo was successfully uploaded.",
	    "green"
        );
        // reload the current page
        header("Location: upload");
	exit();
    }
} ?>
'>
  <table class='inputtable'>
    <tr>
      <td><input type='file' name='photo' accept='image/*'></td>
    </tr>
<?php
if(isset($_SESSION["failed_upload"])) {
    echo("<tr><td style='color:red'>");
    echo($_SESSION["failed_upload"]);
    echo("</tr></td>");
    unset($_SESSION["failed_upload"]);
} ?>
    <tr>
      <td><label for='caption'>Add a caption (optional)</label></td>
    </tr>
    <tr>
      <td><textarea id='caption' name='caption' rows='3' cols='70' 
	    maxlength='128' placeholder='128 characters max'></textarea></td>
    </tr>
    <tr>
      <td><input type='submit' name='submit' value='Upload Photo'></td>
    </tr>
  </table>
</form>
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
