
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'front_menu_1_of_5', 'frontend', 'Lable / Step 1 of 5 - When and Where', 'script', '2015-11-20 04:14:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 1 of 5 - When and Where', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_2_of_5', 'frontend', 'Lable / Step 2 of 5 - Choose A Car', 'script', '2015-11-20 04:15:14');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2 of 5 - Choose A Car', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_3_of_5', 'frontend', 'Lable / Step 3 of 5 - Price And Extras', 'script', '2015-11-20 04:15:49');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 3 of 5 - Price And Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_4_of_5', 'frontend', 'Lable / Step 4 of 5 - Checkout', 'script', '2015-11-20 04:17:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 4 of 5 - Checkout', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_5_of_5', 'frontend', 'Lable / Step 4 of 5 - Finish', 'script', '2015-11-20 04:17:44');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 4 of 5 - Finish', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_step1', 'frontend', 'Label / Step 1 - When And Where', 'script', '2015-11-20 04:18:41');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 1 - When And Where', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_step2', 'frontend', 'Label / Step 2 - Choose A Car', 'script', '2015-11-20 04:19:05');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 2 - Choose A Car', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_step3', 'frontend', 'Label / Step 3 - Price And Extras', 'script', '2015-11-20 04:19:34');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 3 - Price And Extras', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_step4', 'frontend', 'Label / Step 4 - Checkout', 'script', '2015-11-20 04:19:56');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 4 - Checkout', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_menu_step5', 'frontend', 'Label / Step 4 - Finish', 'script', '2015-11-20 04:20:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Step 4 - Finish', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_v_email_invalid', 'frontend', 'Front Label / Validate Email', 'script', '2015-11-20 09:09:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Email is invalid', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_v_notes', 'frontend', 'Front Label / Validate Notes', 'script', '2015-11-20 09:17:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes is required', 'script');

INSERT INTO `fields` VALUES (NULL, 'front_4_v_company', 'frontend', 'Front Label / Validate Company name', 'script', '2015-11-20 09:17:59');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Company name is required', 'script');

COMMIT;