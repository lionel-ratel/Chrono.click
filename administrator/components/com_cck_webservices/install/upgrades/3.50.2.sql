
UPDATE `#__cck_more_webservices_apps` SET `type`='resources' WHERE `type` = "";

ALTER TABLE `#__cck_more_webservices_apps` ADD `nonce` VARBINARY(255) NOT NULL AFTER `methods`;

ALTER TABLE `#__cck_more_webservices_apps` ADD `featured` TINYINT(3) NOT NULL DEFAULT '0' AFTER `options`;