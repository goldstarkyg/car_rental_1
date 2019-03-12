
START TRANSACTION;

ALTER TABLE `bookings` ADD COLUMN `c_notes` text DEFAULT NULL AFTER `c_company`;

INSERT INTO `options` (`foreign_id`, `key`, `tab_id`, `value`, `label`, `type`, `order`, `is_visible`, `style`) VALUES
(1, 'o_bf_include_notes', 4, '1|2|3::2', 'No|Yes|Yes (required)', 'enum', 14, 1, NULL);

INSERT INTO `fields` VALUES (NULL, 'front_4_notes', 'frontend', 'Label / Notes', 'script', '2015-07-09 03:56:26');

SET @id := (SELECT LAST_INSERT_ID());

INSERT INTO `multi_lang` VALUES (NULL, @id, 'pjField', '::LOCALE::', 'title', 'Notes', 'script');

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_email_confirmation_message_text");
UPDATE `multi_lang` SET `content` = 'Available Tokens:<br/><br/>{Title}<br >{Name}<br >{Email}<br >{Phone}<br >{Country}<br >{City}<br >{State}<br >{Zip}<br >{Address}<br >{Company}<br >{Notes}<br >{DtFrom}{DtTo}<br >{PickupLocation}<br >{ReturnLocation}<br >{Type}<br >{Extras}<br >{BookingID}<br >{UniqueID}<br >{Deposit}<br >{Total}<br >{Tax}<br >{Security}<br >{Insurance}<br >{PaymentMethod}<br >{CCType}<br >{CCNum}<br >{CCExp}<br >{CCSec}<br >{CancelURL}<br >' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_email_payment_message_text");
UPDATE `multi_lang` SET `content` = 'Available Tokens:<br/><br/>{Title}<br >{Name}<br >{Email}<br >{Phone}<br >{Country}<br >{City}<br >{State}<br >{Zip}<br >{Address}<br >{Company}<br >{Notes}<br >{DtFrom}{DtTo}<br >{PickupLocation}<br >{ReturnLocation}<br >{Type}<br >{Extras}<br >{BookingID}<br >{UniqueID}<br >{Deposit}<br >{Total}<br >{Tax}<br >{Security}<br >{Insurance}<br >{PaymentMethod}<br >{CCType}<br >{CCNum}<br >{CCExp}<br >{CCSec}<br >{CancelURL}<br >' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

SET @id := (SELECT `id` FROM `fields` WHERE `key` = "opt_o_email_cancel_message_text");
UPDATE `multi_lang` SET `content` = 'Available Tokens:<br/><br/>{Title}<br >{Name}<br >{Email}<br >{Phone}<br >{Country}<br >{City}<br >{State}<br >{Zip}<br >{Address}<br >{Company}<br >{Notes}<br >{DtFrom}{DtTo}<br >{PickupLocation}<br >{ReturnLocation}<br >{Type}<br >{Extras}<br >{BookingID}<br >{UniqueID}<br >{Deposit}<br >{Total}<br >{Tax}<br >{Security}<br >{Insurance}<br >{PaymentMethod}<br >{CCType}<br >{CCNum}<br >{CCExp}<br >{CCSec}<br >{CancelURL}<br >' WHERE `foreign_id` = @id AND `model` = "pjField" AND `field` = "title";

COMMIT;