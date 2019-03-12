
START TRANSACTION;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_website_seo', 1, 'Yes|No::No', NULL, 'enum', 18, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'opt_o_website_seo', 'backend', 'Options / Enable SEO friendly links', 'script', NULL);

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Enable SEO friendly links', 'script');

COMMIT;