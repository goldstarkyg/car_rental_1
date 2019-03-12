<select name="car_id" id="car_id" class="pj-form-field w200 required">
	<option value="">-- <?php __('lblChoose'); ?> --</option>
	<?php
	foreach ($tpl['car_arr'] as $v)
	{
		?><option value="<?php echo $v['car_id']; ?>"><?php echo stripslashes($v['make'] . " " . $v['model'] . " - " . $v['registration_number']); ?></option><?php
	}
	?>
</select>
