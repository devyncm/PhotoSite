CREATE TABLE `photo_users` (
  `user_id` int(6) NOT NULL auto_increment,
  `joindate` date NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(60) NOT NULL,
  `profile_pic_id` int(8),
  PRIMARY KEY  (`user_id`),
  CONSTRAINT userexists UNIQUE KEY (username)
);

CREATE TABLE `photo_users_tokens` (
  `token_id` int(6) NOT NULL auto_increment,
  `user_id` int(6) NOT NULL,
  `token` char(60) NOT NULL,
  `time` int(60) NOT NULL,
  PRIMARY KEY (`token_id`)
);

# If you want to use SQL constraints for extra error-checking, see
# http://elvis.rowan.edu/~kilroy/awp/Wk9.3-sql/BetterKeys.txt
# When adding users, the command will look something like this:
#
# insert into photo_users
#    values(default, "2013-08-08", "bob",
#    "$2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a", "");
#
# where the password field is the result of the PHP password_hash()
# function.  Using placeholders and prepared queries, it will look
# like this:
#
#  $newuser_query = 
#    insert into photo_users values(default, :date, :name, :pword, "");
#
# and use password_hash() when you call bindParam():
#
#        $pword = password_hash($_POST['pword'], PASSWORD_DEFAULT);
#
# This will save the password to the database encrypted, instead of plain
# text.  When someone logs in, your select will have to have a WHERE clause
# such as:
#
#   '.... WHERE username=:name)'
#
# and you'll fetch their data out.  Then you can use password_verify()
# to check the result:
#
#    if ( password_verify( $_POST['pword'], $userinfo->password ) ) {
#        // right password, log them in
#    } else {
#        // wrong password, make them try again
#    }
#
# For an example, see the "passwordtest.php" file in this folder, and
# run "php passwordtest.php" to see what it does.

CREATE TABLE `photo_files` (
  `photo_id` int(8) NOT NULL auto_increment,
  `uploaddate` date,
  `uploadname` varchar(128),
  `caption` varchar(128),      # check the caption for special chars
  `filelocation` varchar(256), # probably want to remove special chars
  PRIMARY KEY  (`photo_id`)
);

# Note that the next two tables do NOT specify foreign key constraints;
# if you want to add that, see:
# http://elvis.rowan.edu/~kilroy/awp/Wk9.3-sql/BetterKeys.txt
#
# You probably want "on delete cascade", so if an account is
# deleted all the associated picture are deleted, and all the
# comments on those pictures are deleted.  Test carefully!

CREATE TABLE `photo_user_links` (
  `connection_id` int(8) NOT NULL auto_increment,
  `user_id` int(6),
  `photo_id` int(8),
  PRIMARY KEY  (`connection_id`)
);

CREATE TABLE `photo_comments` (
  `comment_id` int(8) NOT NULL auto_increment,
  `user_id` int(6), # user who LEFT the comment!
  `photo_id` int(8),
  `comment_text` varchar(128),
  PRIMARY KEY  (`comment_id`)
);
