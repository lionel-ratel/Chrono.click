
CREATE TABLE IF NOT EXISTS `#__cck_more_webservices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `options` text NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `run_as` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(5120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `methods` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;

CREATE TABLE `#__cck_more_webservices_app_resources` (
  `id` int(11) NOT NULL,
  `id2` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`id2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(5120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured` tinyint(3) NOT NULL DEFAULT '0',
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_calls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `webservice` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  `request_format` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_object` varchar(255) NOT NULL,
  `request_options` text NOT NULL,
  `response` varchar(255) NOT NULL,
  `response_check` tinyint(3) NOT NULL DEFAULT '0',
  `response_format` varchar(10) NOT NULL,
  `response_identifier` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `options` text NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `standalone` tinyint(3) NOT NULL,
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `folder` int(11) NOT NULL DEFAULT 1,
  `type` varchar(50) NOT NULL,
  `description` varchar(5120) NOT NULL,
  `format` char(8) NOT NULL,
  `methods` varchar(50) NOT NULL,
  `options` text NOT NULL,
  `options2` text NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;


-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__cck_more_webservices_stack` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webservice` int(11) NOT NULL,
  `webservice_object` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `request` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `published` tinyint(3) NOT NULL DEFAULT 0,
  `stacked` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `executed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `attempts` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` int(10) UNSIGNED,
  `checked_out_time` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
