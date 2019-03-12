
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblDashAvailCarsToday");
UPDATE `multi_lang` SET `content` = 'available cars now' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "lblDashAvailCarToday");
UPDATE `multi_lang` SET `content` = 'available car now' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;