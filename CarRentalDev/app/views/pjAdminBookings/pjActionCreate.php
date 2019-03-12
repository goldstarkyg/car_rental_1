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
	$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
	$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionCreate" method="post" id="frmCreate" class="form pj-form">
		<input type="hidden" name="action_create" value="1" />
		
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails', false, true); ?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails', false, true); ?></a></li>
			</ul>
			<div id="tabs-1" class="tab1-booking">
				<?php pjUtil::printNotice(__('infoAddBookingTitle', true), __('infoAddBookingBody', true)); ?>
				<div id="pj_price_loader"></div>
				<div class="float_left">
					<fieldset class="fieldset white w350">
						<legend><?php __('lblBookingDetails'); ?></legend>
						<p>
							<label class="title110"><b><?php __('lblBookingStatus'); ?></b></label>
							<span class="inline_block">
								<select name="status" id="status" class="pj-form-field w220 required">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach (__('booking_statuses', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_from'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-after">
								<input type="text" name="date_from" id="date_from" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" data-msg-remote="<?php __('lblPickupWorkingTime');?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_to'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-after">
								<input type="text" name="date_to" id="date_to" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" data-msg-remote="<?php __('lblReturnWorkingTime');?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								<input type="hidden" name="dates" id="dates" value="1" />
								<input type="hidden" name="isUpdate" id="isUpdate" value="0" />
							</span>
						</p>
						<p style="overflow: visible">
							<label class="title110"><?php __('booking_type'); ?></label>
							<span class="inline_block">
								<select name="type_id" id="type_id" class="pj-form-field w200 required">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									if (isset($tpl['type_arr']) && count($tpl['type_arr']) > 0)
									{
										foreach ($tpl['type_arr'] as $v)
										{
											?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
										}
									}
									?>
								</select>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_car'); ?></label>
							<span id="boxCars">
								<select name="car_id" id="car_id" class="pj-form-field  w200 required">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach ($tpl['car_arr'] as $v)
									{
										?><option value="<?php echo $v['car_id']; ?>"><?php echo stripslashes($v['make'] . " " . $v['model'] . " - " . $v['registration_number']); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_pickup'); ?></label>
							<span class="inline_block">
								<select name="pickup_id" id="pickup_id" class="pj-form-field w200 required">
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
						</p>
						<p>
							<label class="title110"><?php __('booking_return'); ?></label>
							<span class="inline_block">
								<select name="return_id" id="return_id" class="pj-form-field w200 required">
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
						</p>
						
						<p>
							<label class="title110">&nbsp;</label>
							<input type="button" id="btnSave" value="<?php __('btnSave'); ?>" class="pj-button cr-button-validate-save" />
							<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
						</p>
					</fieldset>
					
					<fieldset class="fieldset white w350 float_left">
						<legend><?php __('booking_extras'); ?></legend>
						<div class="extras">
							<div>
								<label id="lblNoExtra"><?php __('lblNoExtra');?></label>
								<table class="pj-table" id="boxExtras" cellpadding="0" cellspacing="0" >
									<tbody>
										
									</tbody>
								</table>
								<div class="overflow addExtra" id="addExtra">
									<input id="opExtraAdd" class="pj-button float_left r5" type="button" value="<?php __('btnAdd'); ?>">
									<input type="button" id="btnSave2" value="<?php __('btnSave'); ?>" class="pj-button cr-button-validate-save" />
								</div>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="float_right overflow">
					<fieldset class="fieldset white w330">
						<legend><?php __('lblQuote'); ?></legend>
						<p>
							<label class="title bold"><?php __('lblRentalDuration'); ?></label>
							<label id="cr_rental_time" class="content"></label>
							<input type="hidden" id="rental_days" name="rental_days"/>
							<input type="hidden" id="rental_hours" name="rental_hours"/>
						</p>
						<p style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perday')) ? 'block' : 'none';?>">
							<label class="title bold"><?php __('lblPricePerDay'); ?></label>
							<label id="cr_price_per_day" class="content"></label>
							<label id="cr_price_per_day_detail"></label>
							<input type="hidden" id="price_per_day" name="price_per_day"/>
							<input type="hidden" id="price_per_day_detail" name="price_per_day_detail"/>
						</p>
						<p style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perhour')) ? 'block' : 'none';?>">
							<label class="title bold"><?php __('lblPricePerHour'); ?></label>
							<label id="cr_price_per_hour" class="content"></label>
							<label id="cr_price_per_hour_detail"></label>
							<input type="hidden" id="price_per_hour" name="price_per_hour"/>
							<input type="hidden" id="price_per_hour_detail" name="price_per_hour_detail" />
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('lblCarRentalFee'); ?></label>
							<label id="cr_rental_fee" class="content"></label>
							<label id="cr_rental_fee_detail"></label>
							<input type="hidden" id="car_rental_fee" name="car_rental_fee"/>
						</p>
						<p>
							<label class="title bold"><?php __('booking_extra_price'); ?></label>
							<label id="cr_extra_price" class="content"></label>
							<input type="hidden" id="extra_price" name="extra_price" />
						</p>
						<p>
							<label class="title bold"><?php __('booking_insurance'); ?></label>
							<label id="cr_insurance" class="content"></label>
							<label id="cr_insurance_detail"></label>
							<input type="hidden" id="insurance" name="insurance"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_subtotal'); ?></label>
							<label id="cr_sub_total" class="content"></label>
							<input type="hidden" id="sub_total" name="sub_total" />
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_tax'); ?></label>
							<label id="cr_tax" class="content"></label>
							<label id="cr_tax_detail"></label>
							<input type="hidden" id="tax" name="tax"/>
						</p>
						<p>
							<label class="title bold red"><?php __('booking_total_price'); ?></label>
							<label id="cr_total_price" class="content bold"></label>
							<input type="hidden" id="total_price" name="total_price"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_required_deposit'); ?></label>
							<label id="cr_required_deposit" class="content"></label>
							<label id="cr_required_deposit_detail"></label>
							<input type="hidden" id="required_deposit" name="required_deposit"/>
						</p>
					</fieldset>
				</div>
			</div>
			<div id="tabs-2">
				<?php pjUtil::printNotice(__('infoAddClientDetailsTitle', true), __('infoAddClientDetailsDesc', true)); ?>
				<fieldset class="overflow">
					<legend><?php __('lblClientDetails'); ?></legend>
					<?php
					if (in_array($tpl['option_arr']['o_bf_include_title'], array(2, 3)))
					{
						?>
						<p>
							<label class="title"><?php __('booking_title'); ?></label>
							<span class="inline_block">
								<select name="c_title" id="c_title" class="pj-form-field w220 <?php echo $tpl['option_arr']['o_bf_include_title'] == 3 ? ' required' : NULL; ?>">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach (__('_titles', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>"><?php echo stripslashes($v); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
					<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_name'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_name'); ?></label>
						<input type="text" name="c_name" id="c_name" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					
					
					if (in_array($tpl['option_arr']['o_bf_include_email'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_email'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
							<input type="text" name="c_email" id="c_email" class="pj-form-field email w250<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_phone'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_phone'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
							<input type="text" name="c_phone" id="c_phone" class="pj-form-field w250<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_company'); ?></label>
						<input type="text" name="c_company" id="c_company" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_address'); ?></label>
						<input type="text" name="c_address" id="c_address" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
								
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_city'); ?></label>
						<input type="text" name="c_city" id="c_city" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
			
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_state'); ?></label>
						<input type="text" name="c_state" id="c_state" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_zip'); ?></label>
						<input type="text" name="c_zip" id="c_zip" class="pj-form-field  w250<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_country'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_country'); ?></label>
						<select id="c_country" name="c_country" class="pj-form-field w300 <?php echo $tpl['option_arr']['o_bf_include_country'] == 3 ? ' required' : NULL; ?>">
							<option value="">---</option>
							<?php
							foreach ($tpl['country_arr'] as $k => $v)
							{
								?><option value="<?php echo $v['id']; ?>"><?php echo $v['country_title']; ?></option><?php
							}
							?>
						</select>
					</p>
					<?php
					}
					?>
					<p>
						<label class="title"><?php __('booking_cc_type'); ?></label>
						<select name="cc_type" id="cc_type" class="pj-form-field">
						<option value="">-- <?php __('lblChoose'); ?> --</option>
						<?php
						foreach (__('_cc_types', true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo pjSanitize::html($v); ?></option><?php
						}
						?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('booking_cc_number'); ?></label>
						<input type="text" name="cc_num" id="cc_num" class="pj-form-field w200" autocomplete="off" />
					</p>
					<p>
						<label class="title"><?php __('booking_cc_exp'); ?></label>
						<input type="text" name="cc_exp" id="cc_exp" class="pj-form-field w80" autocomplete="off" />
					</p>
					<p>
						<label class="title"><?php __('booking_cc_code'); ?></label>
						<input type="text" name="cc_code" id="cc_code" class="pj-form-field w80" autocomplete="off" />
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="button" id="btnSave4" value="<?php __('btnSave'); ?>" class="pj-button cr-button-validate-save" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
					</p>
				</fieldset>
			</div><!-- tab2 -->
		</div>
				
	</form>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.dateRangeValidation = "<?php __('lblBookingDateRangeValidation'); ?>";
	myLabel.numDaysValidation = "<?php __('lblBookingNumDaysValidation'); ?>";
	</script>
	<?php
}
?>