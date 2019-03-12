<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCarTypeModel extends pjAppModel
{

/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'cars_types';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'car_id', 'type' => 'int', 'default' => ':NULL'),	
		array('name' => 'type_id', 'type' => 'int', 'default' => ':NULL')		
	);
	
	protected $validate = array(
	
	);
	

	public static function factory($attr=array())
	{
		return new pjCarTypeModel($attr);
	}
}
?>