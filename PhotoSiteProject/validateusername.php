<?php
include_once("debughelp.php");
require_once("connect.php");
$dbh = ConnectDB();
# This file's purpose is to verify usernames for use with Ajax functions.
if(isset($_GET["username"])) {
    $username = $_GET["username"];
    if(!(usernameIsLegal($username))) {
	echo("nogood");
    } else if(usernameExists($username)) {
	echo("inuse");
    } else {
	echo("ok");
    }
} else {
    echo("This file is intended to be used with Ajax to validate usernames.");
}

// Check if a username format is legal.
function usernameIsLegal($username) {
    $email_pattern = '/^\S+@\S+\.\S+$/';
    if(preg_match($email_pattern, $username)) {
	return true;
    }
    return false;
}
// Check if a username already exists in the database.
function usernameExists($username) {
    global $dbh;
    try {
        $query = "SELECT count(*) AS cnt FROM photo_users
            WHERE username = :username";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam('username', $username);
        $stmt->execute();
        $count = $stmt->fetchAll(PDO::FETCH_OBJ);
        if($count[0]->cnt > 0) {
	    return true;
	}
        return false;
    } catch(PDOException $e) {
        die('PDOException in validateusername.usernameExists(): '
            . $e.getMessage());
    }
}
?>
