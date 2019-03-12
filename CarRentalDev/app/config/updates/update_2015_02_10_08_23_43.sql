
START TRANSACTION;

INSERT INTO `fields` VALUES (NULL, 'opt_o_authorize_timezone', 'backend', 'Options / Authorize.net time zone', 'script', '2015-02-10 08:22:51');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net time zone', 'script');

INSERT INTO `fields` VALUES (NULL, 'opt_o_authorize_md5_hash', 'backend', 'Options / Authorize.net MD5 hash', 'script', '2015-02-10 08:23:32');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Authorize.net MD5 hash', 'script');

UPDATE `options` SET `order`=27 WHERE `key`='o_allow_creditcard';
UPDATE `options` SET `order`=28 WHERE `key`='o_allow_bank';
UPDATE `options` SET `order`=29 WHERE `key`='o_bank_account';
UPDATE `options` SET `order`=30 WHERE `key`='o_allow_cash';
UPDATE `options` SET `order`=31 WHERE `key`='o_thankyou_page';
UPDATE `options` SET `order`=32 WHERE `key`='o_cancel_booking_page';

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_authorize_timezone', 3, '-43200|-39600|-36000|-32400|-28800|-25200|-21600|-18000|-14400|-10800|-7200|-3600|0|3600|7200|10800|14400|18000|21600|25200|28800|32400|36000|39600|43200|46800::0', 'GMT-12:00|GMT-11:00|GMT-10:00|GMT-09:00|GMT-08:00|GMT-07:00|GMT-06:00|GMT-05:00|GMT-04:00|GMT-03:00|GMT-02:00|GMT-01:00|GMT|GMT+01:00|GMT+02:00|GMT+03:00|GMT+04:00|GMT+05:00|GMT+06:00|GMT+07:00|GMT+08:00|GMT+09:00|GMT+10:00|GMT+11:00|GMT+12:00|GMT+13:00', 'enum', 25, 1, NULL),
(1, 'o_authorize_md5_hash', 3, NULL, NULL, 'string', 26, 1, NULL),
(1, 'o_fields_index', 99, 'd874fcc5fe73b90d770a544664a3775d', NULL, 'string', NULL, 0, NULL);

COMMIT;