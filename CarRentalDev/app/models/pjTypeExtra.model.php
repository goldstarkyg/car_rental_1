<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjTypeExtraModel extends pjAppModel
{
/**
 * The name of table associate with current model
 *
 * @var string
 * @access protected
 */
	var $table = 'types_extras';
/**
 * Table schema
 *
 * @var array
 * @access protected
 */
	protected $schema = array(
		array('name' => 'type_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL')
	);
	
	protected $validate = array(
	
	);
	

	public static function factory($attr=array())
	{
		return new pjTypeExtraModel($attr);
	}
}
?>