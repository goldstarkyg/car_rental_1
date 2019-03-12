
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_invalid_period', 'frontend', 'Label / Return Date/Time must be greater than Pick-up Date/Time', 'script', '2016-06-10 05:43:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return Date/Time must be greater than Pick-up Date/Time.', 'script');

COMMIT;