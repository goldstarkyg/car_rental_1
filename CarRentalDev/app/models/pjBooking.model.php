<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingModel extends pjAppModel
{
/**
 * The name of table's primary key. If PK is over 2 or more columns set this to boolean null
 *
 * @var string
 * @access public
 */
	var $primaryKey = 'id';
/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'bookings';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'uuid', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'ip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'type_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'car_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'pickup_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'return_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'from', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'to', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'start', 'type' => 'int', 'default' => '0'),
		array('name' => 'end', 'type' => 'int', 'default' => '0'),
		
		array('name' => 'rental_days', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'rental_hours', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price_per_day', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_per_day_detail', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'price_per_hour', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'price_per_hour_detail', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'car_rental_fee', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'extra_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'insurance', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'sub_total', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'tax', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'total_price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'required_deposit', 'type' => 'decimal', 'default' => ':NULL'),
		
		array('name' => 'payment_method', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'pickup_date', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'pickup_mileage', 'type' => 'int', 'default' => ':NULL'),
		
		array('name' => 'actual_dropoff_datetime', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'dropoff_mileage', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'security_deposit', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'txn_id', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'processed_on', 'type' => 'datetime', 'default' => ':NULL'),
		array('name' => 'created', 'type' => 'datetime', 'default' => ':NOW()'),
		array('name' => 'c_title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_name', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_phone', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_email', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_company', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_notes', 'type' => 'text', 'default' => ':NULL'),
		array('name' => 'c_address', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_city', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_state', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_zip', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'c_country', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'cc_type', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'cc_num', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_exp', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'cc_code', 'type' => 'blob', 'default' => ':NULL', 'encrypt' => 'AES'),
		array('name' => 'locale_id', 'type' => 'tinyint', 'default' => ':NULL')
	);
	


	public static function factory($attr=array())
	{
		return new pjBookingModel($attr);
	}
}
?>