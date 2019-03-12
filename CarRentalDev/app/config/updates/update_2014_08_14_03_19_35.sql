
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_3_security_deposit', 'frontend', 'Label / Security deposit', 'script', '2014-08-14 10:19:17');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Security deposit', 'script');

COMMIT;