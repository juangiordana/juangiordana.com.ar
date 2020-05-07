CREATE TABLE `users` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `sign_up_date` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `users_emails` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `main` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  UNIQUE KEY `email` (`user_id`, `email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_meta` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED NOT NULL,
  `last_activity` int(11) UNSIGNED NOT NULL,
  `ip_address` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users_settings` (
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `administrator` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `email_html` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
