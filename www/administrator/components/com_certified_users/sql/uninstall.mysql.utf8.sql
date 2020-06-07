DROP TABLE IF EXISTS `#__cud_users`;
DROP TABLE IF EXISTS `#__cud_certifications`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_certified_users.%');