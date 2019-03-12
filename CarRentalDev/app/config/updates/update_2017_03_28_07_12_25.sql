
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_google_map_api', 1, '', NULL, 'string', 19, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_google_map_api', 'backend', 'Options / Google Map API key', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Google Map API key', 'script');

COMMIT;