<?php
include_once("debughelp.php");
require_once("connect.php");
$dbh = ConnectDB();
# This file contains functions that work with the database.
// Check if a username is legal.
function usernameIsLegal($username) {
    $email_pattern = '/^\S+@\S+\.\S+$/';
    if(preg_match($email_pattern, $username)) {
        return 1;
    }
    return -1;
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
	return 1;
	}
	return -1;
    } catch(PDOException $e) {
	die('PDOException in usernameExists(): '
	    . $e.getMessage());
    }
}

// Add a user to the database.
function addUser($username, $password) {
    global $dbh;
    try {
	$query = "INSERT INTO photo_users
	    VALUES(default, CURDATE(), :username, :password, NULL)";
	$stmt = $dbh->prepare($query);
	// hash the password
	$hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
	$stmt->bindParam('username', $username);
	$stmt->bindParam('password', $hashed_pwd);
	$stmt->execute();
    } catch(PDOException $e) {
	die('PDOException in addUser(): '
	    . $e.getMessage());
    }
}

// Retrieve an entire user object using a user id.
function getUser($id){
    global $dbh;
    try {
	$query = "SELECT * FROM photo_users
	    WHERE user_id = :id";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam('id', $id);
	$stmt->execute();
	// should only return one user
	$user = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($user) > 0) {
	    return $user[0];
	}
	// return -1 if no user found
	return -1;
    } catch (PDOException $e) {
	die('PDOException in getUser(): '
	    . $e.getMessage());
    }
}

// Retrieve a user id given a username and password.
function getUserID($username, $password) {
    global $dbh;
    try {
        $query = "SELECT user_id, password FROM photo_users
            WHERE username = :username";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam('username', $username);
        $stmt->execute();
        // should only return one user id
	$user = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($user) > 0) {
	    $hashed_pwd = $user[0]->password;
	    if(password_verify($password, $hashed_pwd)) {
		return $user[0]->user_id;
	    }
	}	
        // return -1 if no user found
        return -1;
    } catch (PDOException $e) {
        die('PDOException in getUserID(): '
            . $e.getMessage());
    }
}

function getUserIDFromUsername($username) {
    global $dbh;
    try {
	$query = "SELECT user_id FROM photo_users
	    WHERE username = :username";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('username', $username);
	$stmt->execute();
	// should only return one user id
	$user = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($user) > 0) {
	    return $user[0]->user_id;
	}
	// return -1 if no user found
	return -1;
    } catch(PDOException $e) {
	die('PDOException in getUserIDFromUsername(): '
	    . $e.getMessage());
    }
}
?>
