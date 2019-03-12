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
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex"><?php __('lblAllTypes'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('lblAllExtras'); ?></a></li>
		</ul>
	</div>
	
	<?php pjUtil::printNotice(__('infoTypesTitle', true), __('infoTypesBody', true)); ?>
	<?php
	$filter = __('filter', true);
	?>
	<div class="b10">
		<div class="float_left t3">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
				<input type="hidden" name="controller" value="pjAdminTypes" />
				<input type="hidden" name="action" value="pjActionCreate" />
				<input type="submit" class="pj-button" value="<?php __('btnAdd'); ?>" />
			</form>
		</div>
		
		<div class="float_right t5">
			<a href="#" class="pj-button btn-all"><?php echo $filter['all']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="T"><?php echo $filter['active']; ?></a>
			<a href="#" class="pj-button btn-filter btn-status" data-column="status" data-value="F"><?php echo $filter['inactive']; ?></a>
		</div>
		<br class="clear_both" />
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
	var pjGrid = pjGrid || {};
	pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";

	var myLabel = myLabel || {};
	myLabel.type_image = "<?php __('type_image'); ?>";
	myLabel.type = "<?php __('lblType'); ?>";
	myLabel.type_car_models = "<?php __('type_car_models'); ?>";
	myLabel.type_num_cars = "<?php __('type_num_cars'); ?>";
	myLabel.status = "<?php __('lblStatus'); ?>";
	myLabel.active = "<?php __('filter_ARRAY_active'); ?>";
	myLabel.inactive = "<?php __('filter_ARRAY_inactive'); ?>";
	myLabel.delete_selected = "<?php __('cr_delete_selected'); ?>";
	myLabel.delete_confirmation = "<?php __('cr_delete_confirmation'); ?>";
	</script>
	<?php
}
?>