<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjTypeModel extends pjAppModel
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
	var $table = 'types';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'passengers', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'luggages', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'doors', 'type' => 'tinyint', 'default' => ':NULL'),
		array('name' => 'transmission', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'thumb_path', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'default_distance', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'extra_price', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'price_per_day', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'price_per_hour', 'type' => 'float', 'default' => ':NULL'),
		array('name' => 'rent_type', 'type' => 'enum', 'default' => 'day'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $validate = array(
	
	);
	
	public $i18n = array('name','description');

	public static function factory($attr=array())
	{
		return new pjTypeModel($attr);
	}
}
?>