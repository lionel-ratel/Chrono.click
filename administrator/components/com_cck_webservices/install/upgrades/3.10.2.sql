
CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_stack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webservice` int(11) NOT NULL,
  `webservice_object` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT 0,
  `stacked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `executed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__cck_more_webservices_auths` DROP `name`;