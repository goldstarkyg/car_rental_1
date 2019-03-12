<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjExtraModel extends pjAppModel
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
	var $table = 'extras';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'per', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'count', 'type' => 'smallint', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => 'multi'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	protected $validate = array(
	
	);
	
	public $i18n = array('name');

	public static function factory($attr=array())
	{
		return new pjExtraModel($attr);
	}
}
?>