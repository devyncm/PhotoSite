CREATE TABLE `photo_users` (
  `user_id` int(6) NOT NULL auto_increment,
  `joindate` date NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` char(60) NOT NULL,
  `profile_pic_id` int(8),
  PRIMARY KEY  (`user_id`),
  CONSTRAINT userexists  
    UNIQUE KEY (username), 
  CONSTRAINT no_photo_users_profile_pic
    FOREIGN KEY (profile_pic_id) REFERENCES photo_files(photo_id) 
    ON DELETE SET NULL
);

CREATE TABLE `photo_users_tokens` (
  `token_id` int(6) NOT NULL auto_increment,
  `user_id` int(6) NOT NULL,
  `token` char(60) NOT NULL,
  `time` int(60) NOT NULL,
  PRIMARY KEY (`token_id`),
  CONSTRAINT no_photo_users_tokens_user
    FOREIGN KEY (user_id) REFERENCES photo_users(user_id) 
    ON DELETE CASCADE
);

CREATE TABLE `photo_files` (
  `photo_id` int(8) NOT NULL auto_increment,
  `user_id` int(6) NOT NULL,
  `uploaddate` date,
  `uploadname` varchar(128),
  `caption` varchar(128),      # check the caption for special chars
  `filelocation` varchar(256), # probably want to remove special chars
  PRIMARY KEY  (`photo_id`), 
  CONSTRAINT no_photo_files_user
    FOREIGN KEY (user_id) REFERENCES photo_users(user_id) 
    ON DELETE CASCADE
);

CREATE TABLE `photo_comments` (
  `comment_id` int(8) NOT NULL auto_increment,
  `user_id` int(6), # user who LEFT the comment!
  `photo_id` int(8),
  `comment_text` varchar(128),
  PRIMARY KEY  (`comment_id`),
  CONSTRAINT no_photo_comments_photo
    FOREIGN KEY (photo_id) REFERENCES photo_files(photo_id) 
    ON DELETE CASCADE
);
