Devyn Melendez's Adv. Web Programming Final Project
Files:

-- CreateTable.sql
	This file simply shows all of the "CREATE TABLE" SQL statements for
each of the database tables used for the website.

-- ajaxfunctions.js
	This file contains JavaScript functions used to let the user know if,
for example, their new username or password are invalid, before they try and
submit it. These functions are not the only form of verification, as the php
files also validate input before trying to insert into the database.

-- changepassword.php
	This file allows registered users to change their password. It asks for
their old password, new password, and to confirm their new password. JavaScript
lets them know if they made a mistake like the old password and new password are
the same or if the new password doesn't match the confirmed password. This is
checked again once the user submits their form.
	Unregistered users get redirected to the start page.

-- connect.php
	This file was provided by Professor Provine and is used to connect to
the MySQL database for this website.

-- dbfunctions.php
	This file contains PHP functions that interact with the database, such
as inserting/getting/deleting users, photos, tokens, and comments. These 
functions are nestled into one file so that the other files can include them
without having to retype them all.

-- debughelp.php
	A file provided by Professor Provine for help with debugging PHP errors.

-- gallery.php
	This file allows both unregistered and registered users to view all of
the photos uploaded by all of the users, in one place. These photos are in
thumbnail form. Users can click on the thumbnails to go to the photo's page.

-- photo.php
	This file allows both unregistered and registered users to view a photo
in its full size (full size as in no wider than the page). The page also 
displays the photo's name, upload date/time, caption (if it exists), and the
user who uploaded it. A link is provided to the user's profile page.
	If the photo has comments, the comments are displayed at the bottom of
the page. If a user is logged in, they can add a comment. Comments show who 
added the comment (and their profile picture, if the user has set one) and the 
date/time of the comment. The owner of a comment can delete their comment from 
the page. The owner of the photo can delete any comment from the photo's page.
Comment text is stripped of special characters before being entered into the
database.
	Users who have deleted their accounts will show up as "[deleted]" next
to their comments.
	The owner of the photo can choose to set the photo as their profile
picture, which allows the photo to show up on their profile page and next to 
their comments. If the photo is already their profile picture, the owner can
unset it. The owner can also delete the photo, which will delete the page and 
its comments.

-- profile.php
	This file displays a user's profile. Unregistered and registered users
can view profile pages. A profile page displays the user's username, date of
registration, their profile picture (if they have one set), and their uploaded
photos. Their uploaded photos are in thumbnail form here and clicking on them
navigates to the photo's individual page.
	The owner of the profile will be reminded they can set a profile 
picture. If they already have one set, they can unset their profile picture 
here. 
	The owner can also manage their account here. They can navigate to the
"change password" page, or delete their account. JavaScript asks the user if 
they really want to delete their account before the account is deleted from
the database. Deletion of an account deletes the user's photos, comments, and
tokens, as a result of foreign key constraints.

-- recoverpassword.php
	This file allows unregistered users who can't remember their password to
send a recovery email to their registered email address. If a registered user
tries to navigate to this page, they get redirected to the start page. 
	If a recovery email is successfully sent (the email address has to 
exist in the database), a database entry for a token is created. A token is an
alphanumeric 60-character string. The recovery email includes a link to 
"resetpassword.php" with the token inside the query string.

-- resetpassword.php
	This file allows unregistered users (registered users get redirected to
the start page) to reset their password once they have their recovery token. 
Recovery emails contain a link to this page with the recovery token as part of
the query string. An invalid, expired, or nonexistant token will cause a 
redirect back to the "recoverpassword.php" page.
	Tokens expire after an hour. Since tokens are stored in the database
alongside the time they were created, this file can check if it's been over an
hour since the token was created.
	If a valid token is provided, the user's tokens are deleted from the
database, and the user can now enter in and confirm a new password. JavaScript
lets the user know if the new password doesn't match the confirmed password.
New passwords are validated before being put into the database.

-- start.php
	This file acts as a start page for both unregistered and registered
users. If unregistered, users can fill out forms to either sign in or register
for a new account. New usernames/passwords are validated by JavaScript as the 
user types them in, and are validated before being entered into the database. 
Passwords are stored as hashes in the database.
	Unregistered users can see how many new photos have been uploaded today,
and see the newest photo uploaded. Clicking on the thumbnail brings the user
to the photo's page. They can also see what features they get if they register.
Unregistered users are encouraged to visit the gallery.
	Registered users are welcomed on this page along with a description of
the pages linked to in the navbar.

-- style.css
	This file is the CSS stylesheet used by the website.

-- upload.php
	This file allows registered users to upload photos. Unregistered users
are redirected to the start page. Uploaded photos appear both on the user's 
profile and in the gallery. Uploaded photos must be of type .png, .jpg, .jpeg,
or .gif. Users can optionally include a caption with their photo, which will
display beneath the photo on the photo's page. Photos and captions are validated
before being entered into the database. Captions are stripped of special
characters.

-- validateusername.php
	This file is exclusively used by the ajaxfunctions.js file to verify
whether a username is legal (AKA, it looks like an email address) and whether it
already exists in the database or not.
