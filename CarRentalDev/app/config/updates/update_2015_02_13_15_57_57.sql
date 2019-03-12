
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_sms_confirmation_message_text', 'backend', 'Option / Email Tokens', 'script', '2015-02-13 15:54:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available Tokens:<br/><br/>{BookingID}', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_admin_sms_payment_message_text', 'backend', 'Option / Email Tokens', 'script', '2015-02-13 15:54:24');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Available Tokens:<br/><br/>{BookingID}', 'script');

COMMIT;