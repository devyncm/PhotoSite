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
    Project | Gallery
  </title>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Gallery</h3>
<hr>

<?php
if(isset($_SESSION["user_id"])) {
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
} else {
    echo("<table id='guestnavbar'>
        <tr>
        <td>You are not logged in.</td>
        <td><a href='start'>Log In</a></td>
        <td><a href='gallery'>Gallery</a></td>
        </tr>
        </table>");
}
echo("<p class='center'>The gallery contains all the photos 
    everyone has uploaded.</p>");
$files = getAllPhotos();
if(!($files)) {
    echo("<p class='center'>No one has uploaded any photos yet.</p>");
} else {
    echo("<p class='center'>Click on a photo to visit its page.</p>");
    echo("<div class='gallery'>");
    foreach($files as $file) {
	$photo = $file->filelocation;
	$id = $file->photo_id;
	echo("<a href='photo?id=$id'>
	    <img src='$photo'/></a>");
    }
    echo("</div><br>");
} ?>

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
