
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'lblPhoneNotAvailable', 'backend', 'Label / Phone number not available', 'script', '2014-08-04 12:37:52');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'There is no phone number available for this reservation.', 'script');

COMMIT;