
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_1_v_err_length', 'frontend', 'Label / error bookingl length', 'script', '2014-08-14 11:25:35');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'You can only rent a car at least {DAYS} day(s).', 'script');

COMMIT;