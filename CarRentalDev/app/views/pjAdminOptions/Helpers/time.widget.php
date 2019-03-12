<?php
class TimeWidget
{
	function day($d = null, $name = 'day', $id = 'day', $class = 'select-mini', $empty = false)
	{
		?><select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php
		if ($empty !== false && is_array($empty))
		{
			?><option value="<?php echo $empty['value']; ?>"><?php echo stripslashes($empty['title']); ?></option><?php
		}
		foreach (range(1, 31) as $v)
		{
			if (strlen($v) == 1)
			{
				$v = '0' . $v;
			}
			
			if (!is_null($d) && $v == $d)
			{
				?><option value="<?php echo $v; ?>" selected="selected"><?php echo $v; ?></option><?php
			} else {
				?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php
			}
		}
		?></select><?php
	}
	
	function month($m = null, $format = null, $name = 'month', $id = 'month', $class = 'select-mini', $empty = false)
	{
		?><select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php
		if ($empty !== false && is_array($empty))
		{
			?><option value="<?php echo $empty['value']; ?>"><?php echo stripslashes($empty['title']); ?></option><?php
		}
		if (!is_null($format) && in_array($format, array('F', 'm', 'M', 'n')))
		{
	
		} else {
			$format = "m";
		}
		
		foreach (range(1, 12) as $v)
		{
			if (strlen($v) == 1)
			{
				$v = '0' . $v;
			}
			
			if (!is_null($m) && $v == $m)
			{
				?><option value="<?php echo $v; ?>" selected="selected"><?php echo date($format, mktime(0, 0, 0, $v, 1, 2000)); ?></option><?php
			} else {
				?><option value="<?php echo $v; ?>"><?php echo date($format, mktime(0, 0, 0, $v, 1, 2000)); ?></option><?php
			}
		}
		?></select><?php
	}
	
	function year($y = null, $left = null, $right = null, $name = 'year', $id = 'year', $class = 'select-mini', $empty = false)
	{
		?><select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php
		if ($empty !== false && is_array($empty))
		{
			?><option value="<?php echo $empty['value']; ?>"><?php echo stripslashes($empty['title']); ?></option><?php
		}
		$curr_year = date("Y");
			
		foreach (range($curr_year - (int) $left, $curr_year + 1 + (int) $right) as $v)
		{
			if (!is_null($y) && $v == $y)
			{
				?><option value="<?php echo $v; ?>" selected="selected"><?php echo $v; ?></option><?php
			} else {
				?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php
			}
		}
		?></select><?php
	}
	
	function hour($h = null, $name = 'hour', $id = 'hour', $class = 'select-mini', $attr = array(), $options=array())
	{
		$defaults = array('start' => 0, 'end' => 23, 'skip' => array());
		$opts = array_merge($defaults, $options);
		
		$attributes = NULL;
		foreach ($attr as $k => $v)
		{
			if (!in_array($k, array('name', 'id', 'class')))
			{
				$attributes .= ' ' . $k . '="'.$v.'"';
			}
		}
		?><select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>"<?php echo $attributes; ?>><?php
		foreach (range($opts['start'], $opts['end']) as $v)
		{
			if (in_array($v, $opts['skip'])) continue;
			
			if (strlen($v) == 1)
			{
				$v = '0' . $v;
			}
			
			if (!is_null($h) && $v == $h)
			{
				?><option value="<?php echo $v; ?>" selected="selected"><?php echo $v; ?></option><?php
			} else {
				?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php
			}
		}
		?></select><?php
	}
	
	function minute($m = null, $name = 'minute', $id = 'minute', $class = 'select-mini', $attr = array(), $step = 1, $options = array())
	{
		$defaults = array('start' => 0, 'end' => 59, 'skip' => array());
		$opts = array_merge($defaults, $options);
		
		$attributes = NULL;
		foreach ($attr as $k => $v)
		{
			if (!in_array($k, array('name', 'id', 'class')))
			{
				$attributes .= ' ' . $k . '="'.$v.'"';
			}
		}
		?><select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>"<?php echo $attributes; ?>><?php
		foreach (range($opts['start'], $opts['end']) as $v)
		{
			if (in_array($v, $opts['skip'])) continue;
			
			if ($step > 0 && $v % $step !== 0)
			{
				continue;
			}
			if (strlen($v) == 1)
			{
				$v = '0' . $v;
			}
			
			if (!is_null($m) && $v == $m)
			{
				?><option value="<?php echo $v; ?>" selected="selected"><?php echo $v; ?></option><?php
			} else {
				?><option value="<?php echo $v; ?>"><?php echo $v; ?></option><?php
			}
		}
		?></select><?php
	}
}
?>