
START TRANSACTION;

CREATE TABLE IF NOT EXISTS `working_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `monday_from` time DEFAULT '00:00:00',
  `monday_to` time DEFAULT NULL,
  `monday_dayoff` enum('T','F') DEFAULT 'F',
  `tuesday_from` time DEFAULT '00:00:00',
  `tuesday_to` time DEFAULT '23:59:00',
  `tuesday_dayoff` enum('T','F') DEFAULT 'F',
  `wednesday_from` time DEFAULT '00:00:00',
  `wednesday_to` time DEFAULT '23:59:00',
  `wednesday_dayoff` enum('T','F') DEFAULT 'F',
  `thursday_from` time DEFAULT '00:00:00',
  `thursday_to` time DEFAULT '23:59:00',  
  `thursday_dayoff` enum('T','F') DEFAULT 'F',
  `friday_from` time DEFAULT '00:00:00',
  `friday_to` time DEFAULT '23:59:00',
  `friday_dayoff` enum('T','F') DEFAULT 'F',
  `saturday_from` time DEFAULT '00:00:00',
  `saturday_to` time DEFAULT '23:59:00',
  `saturday_dayoff` enum('T','F') DEFAULT 'F',
  `sunday_from` time DEFAULT '00:00:00',
  `sunday_to` time DEFAULT '23:59:00',
  `sunday_dayoff` enum('T','F') DEFAULT 'F',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `is_dayoff` enum('T','F') DEFAULT 'F',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`,`date`),
  KEY `is_dayoff` (`is_dayoff`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `fields` VALUES (NULL, 'menuAddress', 'backend', 'Menu / Address', 'script', '2016-01-15 06:28:47');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Address', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuDefaultWorkingTime', 'backend', 'Menu / Default working time', 'script', '2016-01-15 06:29:11');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default working time', 'script');

INSERT INTO `fields` VALUES (NULL, 'menuCustomWorkingTime', 'backend', 'Menu / Custom working time', 'script', '2016-01-15 06:29:30');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_day', 'backend', 'Day of week', 'script', '2016-01-15 06:37:12');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Day of week', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_is', 'backend', 'Is Day off', 'script', '2016-01-15 06:37:34');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Is Day off', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_from', 'backend', 'Start time', 'script', '2016-01-15 06:37:55');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Start time', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_to', 'backend', 'End time', 'script', '2016-01-15 06:38:16');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'End time', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDefaultWTimeTitle', 'backend', 'Infobox / Default Working Time ', 'script', '2016-01-15 06:42:17');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Default Working Time', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoDefaultWTimeDesc', 'backend', 'Infobox / Default Working Time ', 'script', '2016-01-15 06:42:48');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Here you can set working time for this location only. Different working time can be set for each day of the week. You can also set days off. ', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCustomWTimeTitle', 'backend', 'Infobox / Custom Working Time ', 'script', '2016-01-15 06:43:22');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoCustomWTimeDesc', 'backend', 'Infobox / Custom Working Time ', 'script', '2016-01-15 06:44:08');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Using the form below you can set a custom working time for any date for this location only. Just select a date and set working time for it. Or you can just mark the date as a day off.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWT01', 'arrays', 'error_titles_ARRAY_AWT01', 'script', '2016-01-15 06:46:57');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Working time updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWT01', 'arrays', 'error_bodies_ARRAY_AWT01', 'script', '2016-01-15 06:47:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes made to the default working time have been saved.', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_custom', 'backend', 'Label / Custom', 'script', '2016-01-15 06:56:47');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom', 'script');

INSERT INTO `fields` VALUES (NULL, 'time_date', 'backend', 'Label / Date', 'script', '2016-01-15 06:57:04');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Date', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblAll', 'backend', 'Label / All', 'script', '2016-01-15 06:58:09');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWT02', 'arrays', 'error_titles_ARRAY_AWT02', 'script', '2016-01-15 07:06:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time added!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWT02', 'arrays', 'error_bodies_ARRAY_AWT02', 'script', '2016-01-15 07:07:06');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time has been added.', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateCustomWTimeTitle', 'backend', 'Infobox / Update working time', 'script', '2016-01-15 07:15:48');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Update working time', 'script');

INSERT INTO `fields` VALUES (NULL, 'infoUpdateCustomWTimeDesc', 'backend', 'Infobox / Update working time', 'script', '2016-01-15 07:16:19');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Make any changes no the form below and click "Save" button.', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_titles_ARRAY_AWT03', 'arrays', 'error_titles_ARRAY_AWT03', 'script', '2016-01-15 07:18:59');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Custom working time updated!', 'script');

INSERT INTO `fields` VALUES (NULL, 'error_bodies_ARRAY_AWT03', 'arrays', 'error_bodies_ARRAY_AWT03', 'script', '2016-01-15 07:19:30');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'All changes made to custom working time have been updated.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_1', 'arrays', 'wtime_arr_ARRAY_1', 'script', '2016-01-19 02:51:36');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location is not open at the selected time yet.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_2', 'arrays', 'wtime_arr_ARRAY_2', 'script', '2016-01-19 02:54:00');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location is closed at the selected time.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_3', 'arrays', 'wtime_arr_ARRAY_3', 'script', '2016-01-19 02:52:51');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location is not working on the selected date.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_4', 'arrays', 'wtime_arr_ARRAY_4', 'script', '2016-01-19 02:53:15');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return location is not open at the selected time yet.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_5', 'arrays', 'wtime_arr_ARRAY_5', 'script', '2016-01-19 02:53:43');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return location is closed at the selected time.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_6', 'arrays', 'wtime_arr_ARRAY_6', 'script', '2016-01-19 02:54:20');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return location is not working on the selected date.', 'script');

INSERT INTO `fields` VALUES (NULL, 'wtime_arr_ARRAY_7', 'arrays', 'wtime_arr_ARRAY_7', 'script', '2016-03-11 09:56:27');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Office is closed on the selected date/time.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblPickupWorkingTime', 'backend', 'Label / Pick-up location is not working at this time.', 'script', '2016-01-19 07:08:08');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Pick-up location is not working at this time.', 'script');

INSERT INTO `fields` VALUES (NULL, 'lblReturnWorkingTime', 'backend', 'Label / Return location is not working at this time.', 'script', '2016-01-19 07:17:02');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Return location is not working at this time.', 'script');

COMMIT;