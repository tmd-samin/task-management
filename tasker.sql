-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `users`;

-- Create `projects` table
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT(3) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `short_name` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create `tasks` table
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` INT(3) NOT NULL AUTO_INCREMENT,
  `project_id` INT(3) NOT NULL,
  `task_num` INT(3) NOT NULL,
  `task_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `task_desc` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `state` INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_task` (`project_id`, `task_num`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Create `users` table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(2) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  `password` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `role` ENUM('Administrator', 'User') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'User',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Insert sample data into `users` table
INSERT INTO `users` (`login`, `password`, `role`) 
VALUES ('admin', MD5('admin'), 'Administrator')
ON DUPLICATE KEY UPDATE `password` = VALUES(`password`), `role` = VALUES(`role`);
