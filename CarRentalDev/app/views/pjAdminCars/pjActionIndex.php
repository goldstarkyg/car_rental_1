<?php
if (isset($tpl['status']))
{
	$status = __('status', true);
	switch ($tpl['status'])
	{
		case 2:
			pjUtil::printNotice(NULL, $status[2]);
			break;
	}
} else {
	if (isset($_GET['err']))
	{
		$titles = __('error_titles', true);
		$bodies = __('error_bodies', true);
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionIndex"><?php __('lblAllCars'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionAvailability"><?php __('lblAvailability'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoCarsTitle', true), __('infoCarsBody', true)); ?>
	
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminCars" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button" value="<?php __('btnAddCar'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search w150" placeholder="<?php __('btnSearch'); ?>" />
		</form>
		
		<?php
		$filter = __('filter', true);
		?>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";

	pjGrid.queryString = "";
	<?php
	if (isset($_GET['type_id']) && (int) $_GET['type_id'] > 0)
	{
		?>pjGrid.queryString += "&type_id=<?php echo (int) $_GET['type_id']; ?>";<?php
	}
	?>
					                                                                                     
	optionArray = [];
	<?php foreach ($tpl['location_arr'] as $location) { ?>
		optionArray.push({label: "<?php echo $location['name'] ?>", value:  <?php echo $location['id'] ?>});
	<?php } ?>
		
	var locationObject = new Object() ;
	locationObject = optionArray;
	
	var myLabel = myLabel || {};
	myLabel.o_unit = "<?php echo $tpl['option_arr']['o_unit'] ?>";
	myLabel.car_location = "<?php __('car_location'); ?>";
	myLabel.car_mileage = "<?php __('car_mileage'); ?>";
	myLabel.car_bookings = "<?php __('car_bookings'); ?>";
	myLabel.car_reg = "<?php __('car_reg'); ?>";
	myLabel.car_make_model = "<?php __('car_make_model'); ?>";
	myLabel.car_type = "<?php __('car_type'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php __('filter_ARRAY_active'); ?>";
	myLabel.inactive = "<?php __('filter_ARRAY_inactive'); ?>";
	myLabel.delete_selected = "<?php __('cr_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('cr_delete_confirmation'); ?>";
	</script>
	<?php
}
?>