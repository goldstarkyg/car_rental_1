
START TRANSACTION;

UPDATE `fields` SET `key` = 'error_titles_ARRAY_AX01', `label`='error_titles_ARRAY_AX01' WHERE `key` = "error_titles_ARRAY_AE01";
UPDATE `fields` SET `key` = 'error_titles_ARRAY_AX03', `label`='error_titles_ARRAY_AX03' WHERE `key` = "error_titles_ARRAY_AE03";
UPDATE `fields` SET `key` = 'error_titles_ARRAY_AX04', `label`='error_titles_ARRAY_AX04' WHERE `key` = "error_titles_ARRAY_AE04";
UPDATE `fields` SET `key` = 'error_titles_ARRAY_AX08', `label`='error_titles_ARRAY_AX08' WHERE `key` = "error_titles_ARRAY_AE08";

UPDATE `fields` SET `key` = 'error_bodies_ARRAY_AX01', `label`='error_bodies_ARRAY_AX01' WHERE `key` = "error_bodies_ARRAY_AE01";
UPDATE `fields` SET `key` = 'error_bodies_ARRAY_AX03', `label`='error_bodies_ARRAY_AX03' WHERE `key` = "error_bodies_ARRAY_AE03";
UPDATE `fields` SET `key` = 'error_bodies_ARRAY_AX04', `label`='error_bodies_ARRAY_AX04' WHERE `key` = "error_bodies_ARRAY_AE04";
UPDATE `fields` SET `key` = 'error_bodies_ARRAY_AX08', `label`='error_bodies_ARRAY_AX08' WHERE `key` = "error_bodies_ARRAY_AE08";

COMMIT;