<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWorkingTimeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'working_times';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'location_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'monday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'tuesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'wednesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'thursday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'friday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'saturday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_dayoff', 'type' => 'enum', 'default' => 'T'),
		array('name' => 'sunday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_dayoff', 'type' => 'enum', 'default' => 'T')
	);
	
	public static function factory($attr=array())
	{
		return new pjWorkingTimeModel($attr);
	}
	
	public function getWorkingTime($location_id)
	{
		$arr = $this->reset()
			 ->where('location_id', $location_id)
			 ->findAll()
			 ->getData();
		return !empty($arr) ? $arr[0] : array();
	}
	
	public function init($location_id)
	{
		$data['location_id'] = $location_id;
		$weekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
		foreach ($weekdays as $day)
		{
			$data[$day . '_from']       = '00:00:00';
			$data[$day . '_to']         = '23:59:59';
		}
		return $this->reset()->setAttributes($data)->insert()->getInsertId();
	}
}