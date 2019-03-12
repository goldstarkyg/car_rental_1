
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_invalid_period");
UPDATE `multi_lang` SET `content` = 'Return Date/Time to NOT be earlier than Pick-up Date/Time' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;