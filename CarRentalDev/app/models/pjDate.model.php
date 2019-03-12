<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjDateModel extends pjAppModel
{
	protected $primaryKey = 'id';

	protected $table = 'dates';

	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'start_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'end_time', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'is_dayoff', 'type' => 'enum', 'default' => 'F')
	);

	public static function factory($attr=array())
	{
		return new pjDateModel($attr);
	}
	
	public function getDate($location_id, $date)
	{
		$arr = $this
			->where('location_id', $location_id)
			->where('date', $date)
			->limit(1)
			->findAll()
			->getData();
		return !empty($arr) ? $arr[0] : array();
	}
}
?>