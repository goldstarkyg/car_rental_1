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
		
	pjUtil::printNotice(__('infoReservationsTitle', true), __('infoReservationsBody', true));

	$filter = __('filter', true);
	$bs = __('booking_statuses', true);
	?>
	<div class="b10">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" class="float_left pj-form r10">
			<input type="hidden" name="controller" value="pjAdminBookings" />
			<input type="hidden" name="action" value="pjActionCreate" />
			<input type="submit" class="pj-button pj-add-reservation" value="<?php __('btnAddReservation'); ?>" />
		</form>
		<form action="" method="get" class="float_left pj-form frm-filter">
			<input type="text" name="q" class="pj-form-field pj-form-field-search pj-search-reservation" placeholder="<?php __('btnSearch'); ?>" />
			<button type="button" class="pj-button pj-button-detailed"><span class="pj-button-detailed-arrow"></span></button>
		</form>
		
		<div class="pjCrFilter float_right t5">
			<a href="#" class="pj-button btn-filter btn-status" data-column="filter" data-value="all"><?php echo $filter['all']; ?></a>
			<label><?php __('lblPickup')?>:</label>
			<a href="#" class="pj-button btn-filter btn-status<?php echo isset($_GET['filter']) ? ($_GET['filter'] == 'p_today' ? ' pj-button-active' : null) : null;?>" data-column="filter" data-value="p_today"><?php __('booking_today'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status<?php echo isset($_GET['filter']) ? ($_GET['filter'] == 'p_tomorrow' ? ' pj-button-active' : null) : null;?>" data-column="filter" data-value="p_tomorrow"><?php __('booking_tomorrow'); ?></a>
			<label><?php __('lblReturn')?>:</label>
			<a href="#" class="pj-button btn-filter btn-status<?php echo isset($_GET['filter']) ? ($_GET['filter'] == 'r_today' ? ' pj-button-active' : null) : null;?>" data-column="filter" data-value="r_today"><?php __('booking_today'); ?></a>
			<a href="#" class="pj-button btn-filter btn-status<?php echo isset($_GET['filter']) ? ($_GET['filter'] == 'r_tomorrow' ? ' pj-button-active' : null) : null;?>" data-column="filter" data-value="r_tomorrow"><?php __('booking_tomorrow'); ?></a>
			<input type="hidden" id="filter" value="all">
		</div>
		
		
		<br class="clear_both" />
	</div>
	
	<div class="pj-form-filter-advanced" style="display: none">
		<span class="pj-menu-list-arrow"></span>
		<form action="" method="get" class="form pj-form pj-form-search frm-filter-advanced">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr style="height: 55px;">
					<td width="50%" align="left">
						<label class="title" style="width:130px;"><?php __('lblBookingStatus'); ?></label>
						<select name="status" id="status" class="pj-form-field w215">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($bs as $k => $v)
							{
								?><option value="<?php echo $k; ?>"<?php echo isset($_GET['status']) == $k ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v); ?></option><?php
							}
							?>
						</select>
					</td>
					<td align="left" style="padding-left: 15px;">
						&nbsp;
					</td>
				</tr>
				<tr style="height: 55px;">
					<td width="50%" align="left">
						<label class="title" style="width:130px;"><?php __('car_type'); ?></label>
						<select name="type_id" id="type_id" class="pj-form-field w215">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($tpl['type_arr'] as $v)
							{
								?><option value="<?php echo $v['id']; ?>"<?php echo isset($_GET['type_id']) && (int) $_GET['type_id'] == $v['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['name']); ?></option><?php
							}
							?>
						</select>
					</td>
					<td align="left" style="padding-left: 15px;">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
									<label class="title" style="width:125px;"><?php __('booking_id'); ?></label>
								</td>
								<td>
									<span class="inline_block">
										<input type="text" name="booking_id" id="booking_id" class="pj-form-field float_left w200"/>
									</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr style="height: 55px;">
					<td width="50%" align="left">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<label class="title" style="width:90px;"><?php __('lblPickupDate'); ?></label>
									</td>
									<td>
										<label class="float_left block r5 t11"><?php __('lblFrom'); ?> </label><input type="text" name="pickup_from" id="pickup_from" class="pj-form-field float_left pointer w80 required datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									</td>
									<td>
										<label class="float_left block r5 t11"><?php __('lblTo'); ?> </label><input type="text" name="pickup_to" id="pickup_to" class="pj-form-field w80 float_left datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									</td>
								</tr>
							</table>
					</td>
					<td align="left" style="padding-left: 15px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<label class="title" style="width:90px;"><?php __('lblReturnDate'); ?></label>
									</td>
									<td>
										<label class="float_left block r5 t11"><?php __('lblFrom'); ?> </label><input type="text" name="return_from" id="return_from" class="pj-form-field float_left pointer w80 required datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									</td>
									<td>
										<label class="float_left block r5 t11"><?php __('lblTo'); ?> </label><input type="text" name="return_to" id="return_to" class="pj-form-field w80 float_left datepicker" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
									</td>
								</tr>
							</table>
					</td>
				</tr>
				<tr style="height: 55px;">
					<td width="50%" align="left">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<label class="title" style="width:125px;"><?php __('booking_pickup'); ?></label>
									</td>
									<td>
										<span class="inline_block">
											<select name="pickup_id" id="pickup_id" class="pj-form-field w215 ">
												<option value="">-- <?php __('lblChoose'); ?> --</option>
												<?php
												if (isset($tpl['pickup_arr']) && count($tpl['pickup_arr']) > 0)
												{
													foreach ($tpl['pickup_arr'] as $v)
													{
														?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
													}
												}
												?>
											</select>
										</span>
									</td>
								</tr>
							</table>
					</td>
					
					<td align="left" style="padding-left: 15px;">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td>
										<label class="title" style="width:125px;"><?php __('booking_return'); ?></label>
									</td>
									<td>
										<span class="inline_block">
											<select name="return_id" id="return_id" class="pj-form-field w215 ">
												<option value="">-- <?php __('lblChoose'); ?> --</option>
												<?php
												if (isset($tpl['return_arr']) && count($tpl['return_arr']) > 0)
												{
													foreach ($tpl['return_arr'] as $v)
													{
														?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
													}
												}
												?>
											</select>
										</span>
									</td>
								</tr>
							</table>
					</td>
				</tr>
				<tr style="height: 55px;">
					<td colspan="2">
							<label class="title" style="width:130px;">&nbsp;</label>
							<input type="submit" value="<?php __('btnSearch'); ?>" class="pj-button" />
							<input type="reset" value="<?php __('btnCancel'); ?>" class="pj-button" />
					</td>
				</tr>
			</table>
			<br class="clear_both" />
		</form>
	</div>

	<div id="grid"></div>
	<script type="text/javascript">
		var pjGrid = pjGrid || {};
		pjGrid.jsDateFormat = "<?php echo pjUtil::jsDateFormat($tpl['option_arr']['o_date_format']); ?>";
		pjGrid.isEditor = <?php echo $controller->isEditor() ? 'true' : 'false'; ?>;
		pjGrid.queryString = "";
		<?php
		if (isset($_GET['car_id']) && (int) $_GET['car_id'] > 0)
		{
			?>pjGrid.queryString += "&car_id=<?php echo (int) $_GET['car_id']; ?>";<?php
		}
		if (isset($_GET['filter']))
		{
			?>pjGrid.queryString += "&filter=<?php echo $_GET['filter']; ?>";<?php
		}
		$statuses = __('booking_statuses', true);
		?>
		var myLabel = myLabel || {};
		myLabel.pick_drop = "<?php __('booking_pickup_dropoff'); ?>";
		myLabel.booking_from = "<?php __('booking_from'); ?>";
		myLabel.booking_to = "<?php __('booking_to'); ?>";
		myLabel.booking_type = "<?php __('booking_type'); ?>";
		myLabel.booking_car = "<?php __('booking_car'); ?>";
		myLabel.booking_total = "<?php __('booking_total'); ?>";
		myLabel.booking_client = "<?php __('booking_client'); ?>";
		
		myLabel.delete_selected = "<?php __('cr_delete_selected'); ?>";
		myLabel.delete_confirmation = "<?php __('cr_delete_confirmation'); ?>";
		myLabel.status = "<?php __('lblStatus'); ?>";
		myLabel.pending = "<?php echo $statuses['pending']; ?>";
		myLabel.confirmed = "<?php echo $statuses['confirmed']; ?>";
		myLabel.cancelled = "<?php echo $statuses['cancelled']; ?>";
		myLabel.collected = "<?php echo $statuses['collected']; ?>";
		myLabel.completed = "<?php echo $statuses['completed']; ?>";
	</script>
	<?php
}
?>