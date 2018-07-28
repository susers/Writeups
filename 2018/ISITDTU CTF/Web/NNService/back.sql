CREATE TABLE IF NOT EXISTS `users` (
  `id` int(32) primary key auto_increment,
  `username` varchar(100) UNIQUE KEY,
  `nickname` varchar(100) UNIQUE KEY,
  `password` varchar(32),
  `email` varchar(100) UNIQUE KEY
);

CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(32) primary key auto_increment,
  `user_id` int(32),
  `title` varchar(100),
  `content` varchar(500)
);

CREATE TABLE IF NOT EXISTS `avatar` (
    `id` int(32) primary key auto_increment,
    `data` blob,
    `user_id` int(32) UNIQUE KEY,
    `filepath` varchar(100),
    `photo_type` varchar(20)
);

