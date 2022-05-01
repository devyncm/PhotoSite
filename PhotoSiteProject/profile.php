<?php

session_start();
include_once("debughelp.php");
include_once("dbfunctions.php");
require_once("connect.php");
$dbh = ConnectDB();

?>
<!-- This page display's a user's profile --!>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>
    Project | Profile
  </title>
  <script src=
  "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Profile</h3>
<hr>

<?php
// 1: viewing your own profile. 0: viewing someone else's profile
$myprofile = 1;
// the logged in user's id (if logged in)
$user_id = 0;
if(isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
}
// the user that owns the profile
$profile_user = 0;

// if no GET request and not logged in
if(!(isset($_GET["id"])) && !($user_id)) {
    $_SESSION["message"] = array(
        "You need to be logged in to view your profile.",
        "red",
    );
    header("Location: start");
    exit();
}

// if GET request (viewing someone else's profile)
if(isset($_GET["id"])) {
    // if the GET request is just the logged in user's id, redirect.
    if($user_id && $user_id == $_GET["id"]) {
	header("Location: profile");
	exit();
    }
    $myprofile = 0;
    $profile_user = getUser($_GET["id"]);
} else {
    $profile_user = getUser($user_id);
}

if($user_id) {
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
} else {
    echo("<table id='guestnavbar'>
	<tr>
	<td>You are not logged in.</td>
	<td><a href='start'>Log In</a></td>
	<td><a href='gallery'>Gallery</a></td>
	</tr>
	</table>");
}
if(isset($_SESSION["message"])) {
    // put message vars in local vars
    $text = $_SESSION["message"][0];
    $color = $_SESSION["message"][1];
    echo("<p id='message' style='color: "
	. $color . "'>" . $text . "</p>");
    unset($_SESSION["message"]);
}
// if profile user does not exist
if(!($profile_user)) {
    echo("<p class='center'>This profile could not be found.</p>");
} else {
    echo("<h2 class='center'>Profile of $profile_user->username</h2>");
    $date = strtotime($profile_user->joindate);
    $formatted_date = date("M jS, Y", $date);
    echo("<p class='center'>Registered on $formatted_date</p>");
    if(!(is_null($profile_user->profile_pic_id))) {
	$profile_pic = getProfilePic($profile_user->user_id)->filelocation;
	echo("<div class='center'>
	    <img class='profilepic' 
	    src='$profile_pic'/></div>");
    }

    if($myprofile) {
	echo("<form method='post' action='");
	if(isset($_POST["unset_ppic"]) 
	    && !(is_null($profile_user->profile_pic_id))) {
	    if(!(unsetProfilePic($user_id))) {
		$_SESSION["failed_removeppic"] =
		    "An error occurred while trying to remove your old
		    profile picture.";
	    } else {
		$_SESSION["message"] = array(
		    "Your profile picture was removed.",
                    "green"
		);
                // reload the current page
		header("Location: profile");
		exit();
	    }
	}
	echo("'>");
	$tableheader = "A profile picture can go here.";
        if(!(is_null($profile_user->profile_pic_id))) {
            $tableheader = "You can remove your profile picture.";
        }
	echo("<br><table class='inputtable'>
		<tr><th>$tableheader</th></tr>");
	if(isset($_SESSION["failed_removeppic"])) {
	    echo("<tr><td style='color:red'>");
	    echo($_SESSION["failed_removeppic"]);
	    echo("</tr></td>");
	    unset($_SESSION["failed_removeppic"]);
	}
	echo("<tr>");
	if(!(is_null($profile_user->profile_pic_id))) {
	    echo("<td><input type='submit' name='unset_ppic' 
		    value='Remove Profile Picture'>
		  </td>");
	} else {
	    echo("<td>You can set a photo as your profile picture on the 
		photo's page.</td>");
	}
	echo("</tr>
	      </table>
	    </form>
	    <br>
	    <table class='inputtable'>
	      <tr>
	        <th>Account Management</th>
	      </tr>
	      <tr>
	        <td><a href='changepassword'>Change your password</a></td>
	      </tr>
	      <tr>
		<td><a id='deleteaccount' style='color: red' 
		    href='start?deleteaccount'>Delete your account</a></td>
	      </tr>
	    </table>
	    ");
    }
    echo("<h2 class='center'>Uploaded Photos</h2>");
    $files = getUserPhotos($profile_user->user_id);
    if(!($files)) {
	echo("<p class='center'>No uploaded photos yet.</p>");
    } else {
	echo("<p class='center'>Click on a photo to visit its page.</p>");
	echo("<div class='gallery'>");
	foreach($files as $file) {
	    $photo = $file->filelocation;
	    $id = $file->photo_id;
	    echo("<a href='photo?id=$id'>
		<img src='$photo'/></a>");
	}
	echo("</div>");
    }
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
<script>
    $('#deleteaccount').on('click', function() {
	    return confirm('Are you sure you want to delete your account?\n'
		+ 'Press OK to confirm.');
    });
</script>
</body>
</html>
