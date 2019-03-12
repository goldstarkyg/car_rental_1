
START TRANSACTION;

ALTER TABLE `extras` ADD COLUMN `type` enum('single','multi') DEFAULT 'multi' AFTER `count`;

INSERT INTO `fields` VALUES (NULL, 'extra_type', 'backend', 'Extras / Type', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Type', 'script');

INSERT INTO `fields` VALUES (NULL, 'extra_single', 'backend', 'Extras / Single', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Single', 'script');

INSERT INTO `fields` VALUES (NULL, 'extra_multi', 'backend', 'Extras / Multi', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Multi', 'script');

COMMIT;