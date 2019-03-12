
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "front_1_v_err_dates");
UPDATE `multi_lang` SET `content` = 'You can only rent a car at least {HOURS} hour(s).' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;