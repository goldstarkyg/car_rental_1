
START TRANSACTION;

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "type_empty_extra");

UPDATE `multi_lang` SET `content` = 'No extras added. Manage extras {STAG}here{ETAG}.' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;