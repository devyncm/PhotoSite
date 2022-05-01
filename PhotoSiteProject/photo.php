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
    Project | Photo
  </title>
  <meta charset="utf-8" />
  <meta name="Author" content="Devyn Melendez" />
  <meta name="generator" content="vim" />
  <link rel="stylesheet" href="style.css">
</head>

<body>

<h1 class="center">Project</h1>
<h3 class="center">Photo</h3>
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
if(isset($_SESSION["message"])) {
    // put message vars in local vars
    $text = $_SESSION["message"][0];
    $color = $_SESSION["message"][1];
    echo("<p id='message' style='color: "
        . $color . "'>" . $text . "</p>");
    unset($_SESSION["message"]);
}

$photo = 0;
if(isset($_GET["id"])) {
    $photo_id = $_GET["id"];
    $photo = getPhoto($photo_id);
}
if(!($photo)) {
    echo("<p class='center'>This photo could not be found.</p>");
} else {
    $name = $photo->uploadname;
    echo("<h2 class='center'>$name</h2>");
    $path = $photo->filelocation;
    echo("<div class='center'><img class='bordered' src='$path'/></div>");
    $caption = $photo->caption;
    if($caption) {
	echo("<p style='width: 80%; word-break: break-all' 
	    class='center'><i>$caption</i></p>");
    }
    $date = strtotime($photo->uploaddatetime);
    $formatted_date = date('M jS, Y \a\t g:i A', $date);
    $uploader = getUser($photo->user_id);
    echo("<p class='center'>Uploaded on $formatted_date by 
	<a href='profile?id=$uploader->user_id'>$uploader->username</a></p>");
    
    $user = 0;
    if(isset($_SESSION["user_id"])) {
	$user = getUser($_SESSION["user_id"]);
    }
    // if logged in and the photo is your photo
    if($user && $user->user_id == $photo->user_id) {
	echo("<form method='post' action='");
	if(isset($_POST["unset_ppic"])) {
	    unsetProfilePic($user->user_id);
	    $_SESSION["message"] = array(
		"This photo is no longer your profile picture.",
		"green"
	    );
	    header("Location: photo?id=$photo->photo_id");
	    exit();
	} else if(isset($_POST["set_ppic"])) {
	    setProfilePic($user->user_id, $photo->photo_id);
	    $_SESSION["message"] = array(
                "This photo is now set as your profile picture.",
                "green"
            );
            header("Location: photo?id=$photo->photo_id");
            exit();
	} else if(isset($_POST["delete_photo"])) {
	    deletePhoto($photo->photo_id);
	    $_SESSION["message"] = array(
                "The photo was successfully deleted.",
                "green"
	    );
	    header("Location: profile");
	    exit();
	}
	echo("'><table class='inputtable'>
	    <tr><th colspan='2'>Manage Photo</th></tr>
	    <tr>");
	// if this photo is your profile picture
	if($user->profile_pic_id == $photo->photo_id) {
	    echo("<td><input type='submit' name='unset_ppic' 
		value='Remove Profile Picture'></td>");
	} else {
	    echo("<td><input type='submit' name='set_ppic'
		value='Set as Profile Picture'></td>");
	}
	echo("<td><input type='submit' name='delete_photo'
	    value='Delete Photo'></td>
	    </tr>
	    </table>
	    </form>");
    }

    echo("<h2 class='center'>Comments</h2>");
    if(isset($_SESSION["user_id"])) {
	echo("<form method='post' action='");
	if(isset($_POST["add_comment"])) {
	    if(isset($_POST["comment"]) 
		&& addComment($user->user_id, $photo->photo_id, 
		$_POST["comment"])) {
		$_SESSION["message"] = array(
		    "Your comment was added.",
		    "green"
		);
		header("Location: photo?id=$photo->photo_id");
		exit();
	    } else {
		$_SESSION["failed_addcomment"] = "Your comment was invalid. 
		    Please try again.";
	    }
	}
	echo("'><table class='inputtable'>
	    <tr>
	      <th>Comment on this photo</th>
	    </tr>
	    <tr>
	      <td><textarea id='comment' name='comment' rows='5' cols='70' 
		maxlength='256' placeholder='256 characters max'></textarea>
	      </td>
	    </tr>");
	if(isset($_SESSION["failed_addcomment"])) {
	    echo("<tr><td style='color: red'>");
	    echo($_SESSION["failed_addcomment"]);
	    echo("</tr></td>");
	    unset($_SESSION["failed_addcomment"]);
	}
	echo("<tr>
	      <td><input type='submit' name='add_comment' 
		value='Add Comment'></td>
	    </tr>
	  </table>
	</form><br>");
    } else {
	echo("<p class='center'><a href='start'>Log in</a> 
	    to comment on this photo.</p>");
    }
    $comments = getPhotoComments($photo->photo_id);
    if(!($comments)) {
        echo("<p class='center'>No one has commented on this photo yet.</p>");
    } else {
	echo("<table class='comments'>
	    <tr><th id='usercol'>User</th><th id='commentcol'>Comment</th>
		<th id='datecol'>Date</th></tr>");
	foreach($comments as $comment) {
	    $commenter_id = $comment->user_id;
	    $commenter = 0;
	    $profile_pic = 0;
	    if($commenter_id) {
		$commenter = getUser($commenter_id);
		if($commenter->profile_pic_id) {
		    $profile_pic = getPhoto($commenter->profile_pic_id);
		}
	    }
	    $commentdate = strtotime($comment->commentdatetime);
	    $formatted_commentdate = date('M jS, Y \a\t g:i A', $commentdate);
	    $text = $comment->comment_text;
	    $img = 0;
	    echo("<tr><td style='text-align: center'>");
	    if($commenter) {
		echo("<a href='profile?id=$commenter_id'>$commenter->username");
		if($profile_pic) {
		    echo("<br><img src='$profile_pic->filelocation'>");
		}
		echo("</a>");
	    } else {
		echo("[deleted]");
	    }
	    echo("</td>
		<td style='vertical-align: top'>$text</td>
		<td style='text-align: center'>$formatted_commentdate");
	    // if user is logged in
	    // and either owns the photo or owns the comment
	    if($user && ($user->user_id == $photo->user_id 
		|| $user->user_id == $commenter_id)) {
		echo("<br><br><form method='post'>
		    <button type='submit' name='delete_comment' 
		    	value='$comment->comment_id'>Delete Comment</button>
		    </form>");
	    }
	    echo("</td></tr>");
	}
	echo("</table><br>");
	if(isset($_POST["delete_comment"])) {
	    deleteComment($_POST["delete_comment"]);
	    $_SESSION["message"] = array(
		"Comment successfully deleted.",
		"green"
		);
	    header("Location: photo?id=$photo->photo_id");
	    exit();
	}
    }
} 
?>
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
