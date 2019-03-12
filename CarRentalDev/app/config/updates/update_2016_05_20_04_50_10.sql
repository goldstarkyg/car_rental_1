
START TRANSACTION;

ALTER TABLE `locations` ADD COLUMN `notify_email` enum('T','F') DEFAULT 'T' AFTER `thumb`;

INSERT INTO `fields` VALUES (NULL, 'location_email_notify', 'backend', 'Locations / Email notifications', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email notifications', 'script');

INSERT INTO `fields` VALUES (NULL, 'location_email_notify_tip', 'backend', 'Locations / Email notifications tooltip', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Send New reservation, Payment confirmation, and Reservation Cancellation email notifications to this email address.', 'script');

COMMIT;