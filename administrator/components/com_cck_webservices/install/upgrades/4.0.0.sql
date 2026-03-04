
ALTER TABLE `#__cck_more_webservices` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_webservices_apps` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_webservices_auths` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_webservices_calls` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_webservices_stack` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;
ALTER TABLE `#__cck_more_webservices_resources` MODIFY `checked_out_time` datetime NULL DEFAULT NULL;

UPDATE `#__cck_more_webservices` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_webservices_apps` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_webservices_auths` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_webservices_calls` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_webservices_stack` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';
UPDATE `#__cck_more_webservices_resources` SET `checked_out_time` = NULL WHERE `checked_out_time` = '0000-00-00 00:00:00';

ALTER TABLE `#__cck_more_webservices` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_webservices_apps` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_webservices_auths` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_webservices_calls` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_webservices_stack` MODIFY `checked_out` INT UNSIGNED;
ALTER TABLE `#__cck_more_webservices_resources` MODIFY `checked_out` INT UNSIGNED;

UPDATE `#__cck_more_webservices` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_webservices_apps` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_webservices_auths` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_webservices_calls` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_webservices_stack` SET `checked_out` = NULL WHERE `checked_out` = 0;
UPDATE `#__cck_more_webservices_resources` SET `checked_out` = NULL WHERE `checked_out` = 0;

ALTER TABLE `#__cck_more_webservices_apps` ADD `auth_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `name`;
ALTER TABLE `#__cck_more_webservices_auths` ADD `featured` TINYINT(3) NOT NULL DEFAULT '0' AFTER `options`;
ALTER TABLE `#__cck_more_webservices_apps` ADD `methods` VARCHAR(50) NOT NULL AFTER `description`;