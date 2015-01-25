CREATE TABLE IF NOT  EXISTS `bim_migrations` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` LONGTEXT COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci