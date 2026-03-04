
CREATE TABLE `#__cck_more_webservices_app_resources` (
  `id` int(11) NOT NULL,
  `id2` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`,`id2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;