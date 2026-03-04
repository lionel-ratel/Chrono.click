
ALTER TABLE `#__cck_more_webservices_calls` ADD `request_format` VARCHAR(50) NOT NULL AFTER `request`;
ALTER TABLE `#__cck_more_webservices_calls` ADD `request_method` VARCHAR(50) NOT NULL AFTER `request_format`;

ALTER TABLE `#__cck_more_webservices_stack` CHANGE `webservice_object` `webservice_object` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

UPDATE `#__cck_more_webservices_calls` SET `request_format`="nvp" WHERE `request_format` = '';

ALTER TABLE `#__cck_more_webservices_calls` ADD UNIQUE `idx_name` (`name`);