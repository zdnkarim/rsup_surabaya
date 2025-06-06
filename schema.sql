create database rsup_surabaya;

use rsup_surabaya;

CREATE TABLE
	`users` (
		`id` int (11) NOT NULL AUTO_INCREMENT,
		`username` varchar(50) NOT NULL,
		`password` varchar(255) NOT NULL,
		`role` enum ('admin', 'editor', 'user') NOT NULL DEFAULT 'user',
		`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		UNIQUE KEY `username` (`username`)
	);

insert into
	users (username, password, role)
values
	(
		'admin',
		'$2y$10$0Cf5oJIxeqqN9HfRpsXq8uDeoUKYF3NwuRx.1STGLf2DRhUlig28.',
		'admin'
	), --username: admin, password: password123
	(
		'editor',
		'$2y$10$0Cf5oJIxeqqN9HfRpsXq8uDeoUKYF3NwuRx.1STGLf2DRhUlig28.',
		'editor'
	), --username: editor, password: password123
	(
		'user',
		'$2y$10$0Cf5oJIxeqqN9HfRpsXq8uDeoUKYF3NwuRx.1STGLf2DRhUlig28.',
		'user'
	) --username: user, password: password123
;

CREATE TABLE
	`datas` (
		`id` int NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`content` text NOT NULL,
		`category` varchar(255) NOT NULL,
		`user_id` int NOT NULL,
		`image_path` varchar(255) DEFAULT NULL,
		`pdf_path` varchar(255) DEFAULT NULL,
		`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		KEY `user_id` (`user_id`),
		CONSTRAINT `datas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
	)
