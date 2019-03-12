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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
	$date_from = isset($_POST['date_from']) ? pjUtil::formatDate(date('Y-m-d', strtotime($_POST['date_from'])), "Y-m-d", $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format']);
	$date_to = isset($_POST['date_to']) ? pjUtil::formatDate(date('Y-m-d', strtotime($_POST['date_to'])),"Y-m-d", $tpl['option_arr']['o_date_format']) : date($tpl['option_arr']['o_date_format'], time() + (7 * 86400));
	
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionIndex"><?php __('lblAllCars'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionAvailability"><?php __('lblAvailability'); ?></a></li>
		</ul>
	</div>
	<?php
	pjUtil::printNotice(__('infoAvailabilityTitle', true, false), __('infoAvailabilityDesc', true, false)); 
	?>
	<div class="pj-availability-form">
		<form name="frmAvailability"  method="post" id="frmAvailability" class="form pj-form">
			<input type="hidden" name="get_avail" value="1"/>
			<div class="pj-filter-block">
				<div class="pj-filter-from">
					<label><?php __('lblFrom')?>:</label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_from" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo $date_from; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</div>
				<div class="pj-filter-to">
					<label><?php __('lblTo')?>:</label>
					<span class="pj-form-field-custom pj-form-field-custom-after">
						<input type="text" name="date_to" class="pj-form-field pointer w80 datepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" value="<?php echo $date_to; ?>" />
						<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
					</span>
				</div>
			</div>
			<div class="pj-filter-block">
				<label><?php __('lblFilterByType')?>:</label>
				<span class="inline-block">
					<select name="car_type" id="car_type" class="pj-form-field w100">
						<option value="">-- <?php __('lblViewAll'); ?> --</option>
						<?php
						foreach ($tpl['type_arr'] as $type)
						{
							
							?><option value="<?php echo $type['id']; ?>" ><?php echo pjSanitize::clean($type['name']); ?></option><?php
							
						}
						?>
					</select>
				</span>
			</div>
			<div class="pj-filter-block">
				<label><?php __('lblCars')?>:</label>
				<span class="inline-block">
					<select id="car_id" name="car_id[]" multiple="multiple" size="5" class="w100">
						<?php
						foreach ($tpl['car_arr'] as $v)
						{
							
							?><option value="<?php echo $v['id']; ?>" ><?php echo stripslashes($v['car_name'] ." - ". $v['registration_number']); ?></option><?php
							
						}
						?>
					</select>
				</span>
			</div>
		</form>
	</div>
	<div class="pj-availability-outer">
		<div class="pj_availability_loader"></div>
		<div id="pj_availability_content" class="pj-availability-content"></div>
	</div>
	<?php
}
?>
<script type="text/javascript">

var myLabel = myLabel || {};
myLabel.view_all = "--<?php __('lblViewAll'); ?>--";
</script>