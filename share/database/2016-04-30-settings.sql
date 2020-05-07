ALTER TABLE `settings` CHANGE `val` `value_production` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';

ALTER TABLE `settings` ADD `value_development` VARCHAR(255) NOT NULL AFTER `name`;
UPDATE `settings` SET `value_development` = `value_production`;

ALTER TABLE `settings` ADD `enable` TINYINT(1) NOT NULL AFTER `edit`;
UPDATE `settings` SET `enable` = 1;

DELETE FROM `settings` WHERE `settings`.`name` = 'production';

INSERT INTO
    `settings` (`name`, `value_development`, `value_production`, `type`, `title`, `description`, `edit`, `enable`)
VALUES
    ('app_domain', 'dev.juangiordanda.com.ar', 'www.juangiordanda.com.ar', 'string', 'Application domain.', 'Application domain.', '0', '1');
