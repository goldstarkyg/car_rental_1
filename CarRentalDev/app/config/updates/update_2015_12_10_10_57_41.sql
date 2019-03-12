
START TRANSACTION;

ALTER TABLE `locations` ADD `thumb` VARCHAR(255) NULL;

INSERT INTO `fields` VALUES (NULL, 'lblLocationThumb', 'backend', 'Locations / Thumbnail', 'script', '2015-12-10 10:01:29');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Thumb', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationDeleteThumbTitle', 'backend', 'Locations / Delete thumbnail (title)', 'script', '2015-12-10 10:01:33');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Delete thumbnail', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblLocationDeleteThumbContent', 'backend', 'Locations / Delete thumbnail (content)', 'script', '2015-12-10 10:01:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Are you sure you want to delete the thumbnail?', 'script');

COMMIT;