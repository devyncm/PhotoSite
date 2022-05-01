<?php
include_once("debughelp.php");
require_once("connect.php");
$dbh = ConnectDB();
# This file contains functions that work with the database.

// Check if a username is legal.
function usernameIsLegal($username) {
    $email_pattern = '/^\S+@\S+\.\S+$/';
    if(strlen($username) <= 30 && preg_match($email_pattern, $username)) {
        return 1;
    }
    return 0;
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
	return 0;
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
	// return 0 if no user found
	return 0;
    } catch (PDOException $e) {
	die('PDOException in getUser(): '
	    . $e.getMessage());
    }
}


// Delete a user given their user id.
function deleteUser($id) {
    global $dbh;
    // delete user's archive of photos
    $dir = "./UPLOADED/archive/$id";
    if(file_exists($dir)) {
	// empty out the directory first
	foreach(scandir($dir) as $file) {
	    if($file == "." || $file == "..") {
		continue;
	    }

	    if(!(is_dir($file))) {
		unlink("$dir/$file");
	    }
	}
	// remove the now-empty directory
	rmdir($dir);
    }
    try {
	$query = "DELETE FROM photo_users
	    WHERE user_id = :id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	// Foreign key constraints mean that the user's photo entries
	// (and those photos' comment entries) should auto-delete. 
    } catch(PDOException $e) {
	die('PDOException in deleteUser(): '
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
        // return 0 if no user found
        return 0;
    } catch (PDOException $e) {
        die('PDOException in getUserID(): '
            . $e.getMessage());
    }
}

// Retrieve a user's id based on their username.
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
	// return 0 if no user found
	return 0;
    } catch(PDOException $e) {
	die('PDOException in getUserIDFromUsername(): '
	    . $e.getMessage());
    }
}

// Verify if a password is correct for a given user id
function checkPasswordMatch($password, $id) {
    $hashed_pwd = getUser($id)->password;
    if(password_verify($password, $hashed_pwd)) {
	return 1;
    }
    return 0;
}

// Change a user's password
function changePassword($password, $id) {
    global $dbh;
    try {
	$query = "UPDATE photo_users
	    SET password = :password
	    WHERE user_id = :id";
	$stmt = $dbh->prepare($query);
        // hash the password
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam('password', $hashed_pwd);
        $stmt->bindParam('id', $id);
	$stmt->execute();
    } catch(PDOException $e) {
	die('PDOException in changePassword(): '
	    . $e.getMessage());
    }
}

// Add a temporary token to allow users to recover their password
function addUserToken($id, $token, $time) {
    global $dbh;
    try {
	$query = "INSERT INTO photo_users_tokens
	    VALUES(default, :user_id, :token, :time)";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('user_id', $id);
	$stmt->bindParam('token', $token);
	$stmt->bindParam('time', $time);
	$stmt->execute();
    } catch(PDOException $e) {
	die('PDOException in addUserToken(): '
	    . $e.getMessage());
    }
}

// Checks if a token is valid
function checkUserToken($token) {
    global $dbh;
    try {
	$query = "SELECT user_id, time FROM photo_users_tokens
	    WHERE token = :token";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('token', $token);
	$stmt->execute();
	$token = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($token) > 0) {
	    // the token expires after 1 hour
	    $expiration = 3600;
	    $token_time = $token[0]->time;
	    $diff = time() - $token_time;
	    if($diff <= $expiration) {
		return $token[0]->user_id;
	    }
	}
	// return 0 if the token was invalid
	return 0;
    } catch(PDOException $e) {
	die('PDOException in checkUserToken(): '
	    . $e.getMessage());
    }
}

// Delete all of a user's tokens
function deleteUserTokens($id) {
    global $dbh;
    try {
	$query = "DELETE FROM photo_users_tokens
	    WHERE user_id = :id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	return 1;
    } catch(PDOException $e) {
	die('PDOException in deleteUserTokens(): '
	    . $e.getMessage());
    }
    return 0;
}

// Create a photo archive for a given user if it doesn't already exist
function createUserArchive($id) {
    if(!(file_exists("./UPLOADED/archive/$id"))) {
        mkdir("./UPLOADED/archive/$id", 0777);
        chmod("./UPLOADED/archive/$id", 0777);
    }
}

// Set a photo as a user's profile picture using their ids
function setProfilePic($user_id, $photo_id) {
    global $dbh;	
    try {
	$query = "UPDATE photo_users
	    SET profile_pic_id = :photo_id
	    WHERE user_id = :user_id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('photo_id', $photo_id);
	$stmt->bindParam('user_id', $user_id);
	$stmt->execute();
	return 1;
    } catch(PDOException $e) {
	die('PDOException in setProfilePic(): '
	    . $e.getMessage());
    }
    return 0;
}

// Retrieve profile picture information given a user id
function getProfilePic($id) {
    global $dbh;
    try {
	$query = "SELECT f.* FROM photo_files f 
	    JOIN photo_users u ON f.photo_id = u.profile_pic_id
	    WHERE u.user_id = :id";
    	$stmt = $dbh->prepare($query);
    	$stmt->bindParam('id', $id);
    	$stmt->execute();
    	$file = $stmt->fetchAll(PDO::FETCH_OBJ);
    	if(count($file) > 0) {
	    return $file[0];
    	}
    } catch(PDOException $e) {
	die('PDOException in getProfilePic(): '
	    . $e.getMessage());
    }
    return 0;
}

// Unset a user's profile picture given a user id
function unsetProfilePic($id) {
    global $dbh;
    try {
	$query = "UPDATE photo_users
	    SET profile_pic_id = NULL
	    WHERE user_id = :id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	return 1;
    } catch(PDOException $e) {
	die('PDOException in unsetProfilePic(): '
	    . $e.getMessage());
    }
    return 0;
}

// Upload a photo by a given user with an optional caption
function uploadPhoto($id, $file, $caption) {
    global $dbh;
    createUserArchive($id);
    // make sure the file was uploaded
    if(!(is_uploaded_file($file["tmp_name"]))) {
        return 0;
    }
    $filename = $file["name"];
    // replace special chars with underscore in file name
    $filename = preg_replace("/[^a-zA-Z0-9.]/", '_', $filename);
    $targetname = "./UPLOADED/archive/$id/$filename";
    // check if file extension is supported
    $supported_image = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );
    $ext = strtolower(pathinfo($targetname, PATHINFO_EXTENSION));
    if(!(in_array($ext, $supported_image))) {
        return 0;
    }
    if(!(file_exists($targetname)) && copy($file["tmp_name"], $targetname)) {
        chmod($targetname, 0444);
	try {
	    // replace special chars with underscore in caption
	    $newcaption = $caption;
	    if($newcaption) {
		$newcaption = preg_replace("/[^a-zA-Z0-9.!? ]/", '', 
		    $newcaption);
	    }
            // create a database entry for the photo 
            $query = "INSERT INTO photo_files
                VALUES(default, :id, NOW(), :filename,
                    IFNULL(:caption, NULL), :filelocation)";
	    $stmt = $dbh->prepare($query);
	    $stmt->bindParam('id', $id);
	    $stmt->bindParam('filename', $filename);
	    $stmt->bindParam('caption', $newcaption);
            $stmt->bindParam('filelocation', $targetname);
	    $stmt->execute();
	    return 1;
	} catch(PDOException $e) {
	    die('PDOException in uploadPhoto(): '
		. $e.getMessage());
	}
    }
    return 0;
}

// Retrieve a photo given its photo id
function getPhoto($id) {
    global $dbh;
    try {
	$query = "SELECT * FROM photo_files
	    WHERE photo_id = :id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	$file = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($file) > 0) {
            return $file[0];
        }
    } catch(PDOException $e) {
        die('PDOException in getPhoto(): '
            . $e.getMessage());
    }
    return 0;
}

// Delete a photo given its photo id
function deletePhoto($id) {
    global $dbh;
    $photo = getPhoto($id);
    if($photo) {
    	try {
    	    $query = "DELETE FROM photo_files
		WHERE photo_id = :id";
    	    $stmt = $dbh->prepare($query);
	    $stmt->bindParam('id', $id);
	    $stmt->execute();
	    // Once executed, related entries in other tables should be
	    // deleted automatically thanks to foreign key constraints
	    // Now delete the actual photo
	    if(file_exists($photo->filelocation)) {
                unlink($photo->filelocation);
                return 1;
            }
	} catch(PDOException $e) {
	    die('PDOException in deletePhoto(): '
		. $e.getMessage());
	}
    }
    return 0;
}

// Retrieve all the photos uploaded by a specific user.
function getUserPhotos($id) {
    global $dbh;
    try {
	$query = "SELECT * FROM photo_files
	    WHERE user_id = :id
	    ORDER BY uploaddatetime DESC";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	$files = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($files) > 0) {
            return $files;
	}
    } catch(PDOException $e) {
	die('PDOException in getUserPhotos(): '
	    . $e.getMessage());
    }
    return 0;
}

// Retrieve all uploaded photos.
function getAllPhotos() {
    global $dbh;
    try {
	$query = "SELECT * FROM photo_files 
		ORDER BY uploaddatetime DESC";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $files = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($files) > 0) {
            return $files;
        }
    } catch(PDOException $e) {
	die('PDOException in getAllPhotos(): '
	    . $e.getMessage());
    }
}

// Add a user's comment to a photo.
function addComment($user_id, $photo_id, $text) {
    global $dbh;
    try {
	// remove special chars from text
        $newtext = trim(preg_replace("/[^a-zA-Z0-9.!? ]/", '', $text));
	if(!($newtext)) {
	    return 0;
	}
	$query = "INSERT INTO photo_comments
		VALUES(default, :user_id, :photo_id, NOW(), :text)";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('user_id', $user_id);
	$stmt->bindParam('photo_id', $photo_id);
	$stmt->bindParam('text', $newtext);
	$stmt->execute();
	return 1;
    } catch(PDOException $e) {
	die('PDOException in addComment(): '
	    . $e.getMessage());
    }
    return 0;
}

// Get all of a photo's comments using a photo id.
function getPhotoComments($id) {
    global $dbh;
    try {
	$query = "SELECT * FROM photo_comments
		WHERE photo_id = :id 
		ORDER BY commentdatetime";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	$comments = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($comments) > 0) {
            return $comments;
        }
    } catch(PDOException $e) {
        die('PDOException in getPhotoComments(): '
            . $e.getMessage());
    }
    return 0;
}

// Delete a comment given its comment id.
function deleteComment($id) {
    global $dbh;
    try {
	$query = "DELETE FROM photo_comments
	    WHERE comment_id = :id";
	$stmt = $dbh->prepare($query);
	$stmt->bindParam('id', $id);
	$stmt->execute();
	return 1;
    } catch(PDOException $e) {
        die('PDOException in deleteComment(): '
            . $e.getMessage());
    }
    return 0;
}

function countTodaysPhotos() {
    global $dbh;
    try {
	$query = "SELECT COUNT(*) AS cnt FROM photo_files
	    WHERE DATE(uploaddatetime) = CURDATE()";
	$stmt = $dbh->prepare($query);
	$stmt->execute();
	$photo_count = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($photo_count) > 0) {
	    return $photo_count[0]->cnt;
	}
    } catch(PDOException $e) {
	die('PDOException in countTodaysPhotos(): '
	    . $e.getMessage());
    }
    return 0;
}

function getNewestPhoto() {
    global $dbh;
    try {
        $query = "SELECT * FROM photo_files
	    ORDER BY uploaddatetime DESC 
	    LIMIT 1";
        $stmt = $dbh->prepare($query);
        $stmt->execute();
        $photo = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(count($photo) > 0) {
            return $photo[0];
        }
    } catch(PDOException $e) {
        die('PDOException in getNewestPhoto(): '
            . $e.getMessage());
    }
    return 0;
}
?>
