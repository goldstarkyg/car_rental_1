
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_time_period', 'backend', 'Options / Time format', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Time format', 'script');

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_time_period', 1, '12hours|24hours::12hours', '12 hours|24 hours', 'enum', 4, 1, NULL);

COMMIT;