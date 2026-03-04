
ALTER TABLE `#__cck_more_webservices_stack` ADD `attempts` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `executed`;

ALTER TABLE `#__cck_more_webservices_calls` ADD `response_check` TINYINT(3) NOT NULL DEFAULT '0' AFTER `response`;

ALTER TABLE `#__cck_more_webservices_resources` ADD `options2` TEXT NOT NULL AFTER `options`;

ALTER TABLE `#__cck_more_webservices_calls` ADD UNIQUE `idx_name` (`name`);