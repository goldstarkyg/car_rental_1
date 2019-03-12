
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_bf_include_captcha', 4, '1|2|3::3', 'No|Yes|Yes (required)', 'enum', 15, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_bf_captcha', 'backend', 'Options / Captcha', 'script', NULL);
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_captcha', 'frontend', 'Label / Captcha', 'script', '2017-03-28 06:13:53');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_v_captcha', 'frontend', 'Label / Captcha is required.', 'script', '2017-03-28 06:17:07');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha is required.', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_v_captcha_incorrect', 'frontend', 'Label / Captcha is incorrect.', 'script', '2017-03-28 06:17:24');
SET @id := (SELECT LAST_INSERT_ID());
INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Captcha is incorrect.', 'script');

COMMIT;