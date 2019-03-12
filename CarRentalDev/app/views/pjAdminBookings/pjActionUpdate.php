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
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate" method="post" id="frmUpdate" class="form pj-form">
		<input type="hidden" name="booking_update" value="1" />
		<input type="hidden" name="tab_id" value="<?php echo isset($_GET['tab_id']) && !empty($_GET['tab_id']) ? $_GET['tab_id'] : 'tabs-1'; ?>" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
				
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php __('lblBookingDetails', false, true); ?></a></li>
				<li><a href="#tabs-2"><?php __('lblClientDetails', false, true); ?></a></li>
				<li><a href="#tabs-3"><?php __('lblBookingCollect', false, true); ?></a></li>
				<li><a href="#tabs-4"><?php __('lblBookingReturn', false, true); ?></a></li>
				<li><a href="#tabs-5"><?php __('lblBookingPayments', false, true); ?></a></li>
			</ul>
			<div id="tabs-1" class="tab1-booking">
				<?php pjUtil::printNotice(__('infoUpdateBookingDetailsTitle', true), __('infoUpdateBookingDetailsDesc', true)); ?>
				<div id="pj_price_loader"></div>
				<div class="float_left">
					<fieldset class="fieldset white w350">
						<legend><?php __('lblBookingDetails'); ?></legend>
						<?php
							$created = strtotime($tpl['arr']['created']);
							$from_ts = strtotime($tpl['arr']['from']);
							$to_ts = strtotime($tpl['arr']['to']);
							$date_from = date($tpl['option_arr']['o_date_format'], $from_ts)." ".date('H:i',$from_ts);
							$date_to = date($tpl['option_arr']['o_date_format'], $to_ts)." ".date('H:i',$to_ts);
							$created_datetime = date($tpl['option_arr']['o_date_format'], $created)." ".date('H:i',$created);
						?>
						<p>
							<label class="title110"><?php __('booking_id'); ?></label>
							<span class="inline_block">
								<label class="content"><?php echo $tpl['arr']['booking_id'];?></label>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_ip'); ?></label>
							<span class="inline_block">
								<label class="content"><?php echo $tpl['arr']['ip'];?></label>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_created_on'); ?></label>
							<span class="inline_block">
								<label class="content"><?php echo $created_datetime;?></label>
							</span>
						</p>
						
						<p>
							<label class="title110"><b><?php __('lblBookingStatus'); ?></b></label>
							<span class="inline_block">
								<select name="status" id="status" class="pj-form-field w220 required">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach (__('booking_statuses', true) as $k => $v)
									{
										?><option value="<?php echo $k; ?>" <?php echo $tpl['arr']['status'] == $k ? 'selected="selected"' : '' ?>><?php echo stripslashes($v); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_from'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-after">
								<input type="text" name="date_from" id="date_from" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" value="<?php echo $date_from; ?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" data-msg-remote="<?php __('lblPickupWorkingTime');?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
							</span>
						</p>
						<p>
							<label class="title110"><?php __('booking_to'); ?></label>
							<span class="pj-form-field-custom pj-form-field-custom-after">
								<input type="text" name="date_to" id="date_to" class="pj-form-field pointer w120 required datetimepick" readonly="readonly" value="<?php echo $date_to ; ?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" data-msg-remote="<?php __('lblReturnWorkingTime');?>"/>
								<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
								<input type="hidden" name="dates" id="dates" value="1" />
								<input type="hidden" name="isUpdate" id="isUpdate" value="0" />
							</span>
						</p>
						<?php
						$rental_time = '';
						$rental_days = $tpl['arr']['rental_days'];
						$hours = $tpl['arr']['rental_hours'];
						$daily_mileage_limit = 0;
						$price_for_extra_mileage = 0;
						if($rental_days > 0 || $tpl['arr']['rental_hours'] > 0){
							if($tpl['arr']['rental_days'] > 0){
								$rental_time .= $rental_days . ' ' . ($rental_days > 1 ? __('plural_day', true, false) : __('singular_day', true, false));
							}
							if($hours > 0){
								$rental_time .= ' ' . $hours . ' ' . ($hours > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false));
							}
						}
						?>
						
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
											if($tpl['arr']['type_id'] == $v['id'])
											{
												?><option value="<?php echo $v['id']; ?>" selected="selected"><?php echo stripslashes($v['name']); ?></option><?php
												$daily_mileage_limit = $v['default_distance'];
												$price_for_extra_mileage = $v['extra_price'];
											}else{
												?><option value="<?php echo $v['id']; ?>"><?php echo stripslashes($v['name']); ?></option><?php
											}
										}
									}
									?>
								</select>
							</span>
						</p>
						<p><label class="title110"><?php __('booking_car'); ?></label>
							<span id="boxCars">
								<select name="car_id" id="car_id" class="pj-form-field  w200 required">
									<option value="">-- <?php __('lblChoose'); ?> --</option>
									<?php
									foreach ($tpl['car_arr'] as $v)
									{
										?><option value="<?php echo $v['car_id']; ?>"<?php echo $tpl['arr']['car_id'] == $v['car_id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['make'] . " " . $v['model'] . " - " . $v['registration_number']); ?></option><?php
									}
									?>
								</select>
							</span>
						</p>
					
						<div style="clear: both"></div>
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
											?><option value="<?php echo $v['id']; ?>" <?php echo $tpl['arr']['pickup_id'] == $v['id'] ? 'selected="selected"' : ''; ?>><?php echo stripslashes($v['name']); ?></option><?php
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
											?><option value="<?php echo $v['id']; ?>" <?php echo $tpl['arr']['return_id'] == $v['id'] ? 'selected="selected"' : ''; ?>><?php echo stripslashes($v['name']); ?></option><?php
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
						<div id="dialogUpdateCar" title="Update Car" style="display: none"></div>
					</fieldset>
					<fieldset class="fieldset white w350 float_left">
						<legend><?php __('booking_extras'); ?></legend>
						<div class="extras">
							<div>
								<label id="lblNoExtra"><?php __('lblNoExtra');?></label>
								<table class="pj-table" id="boxExtras" cellpadding="0" cellspacing="0" >
									<tbody>
										<?php
										$number_of_extra = 0;
										$per_extra = __('per_extras', true, false);
										if(count($tpl['be_arr']) > 0)
										{
											foreach ($tpl['be_arr'] as $key => $extra_id)
											{
												mt_srand();
												$index = 'x_' . mt_rand();
												?>
												<tr>
													<td style="padding:7px 3px; vertical-align: top;">
														<select name="extra_id[<?php echo $index; ?>]" class="pj-form-field pj-extra-item b3" style="width: 220px;">
															<option value="" data-price="">-- <?php __('lblChoose'); ?> --</option>
															<?php
															if (isset($tpl['extra_arr']))
															{
																foreach ($tpl['extra_arr'] as $v)
																{
																	?><option value="<?php echo $v['extra_id']; ?>" <?php echo $v['extra_id'] == $extra_id ? ' selected="selected"' : NULL; ?> data-price="<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . $per_extra[$v['per']]; ?>"><?php echo stripslashes($v['name']); ?></option><?php
																}
															}
															?>
														</select>
														<div class="pj-extra-price"><?php echo isset($tpl['extra_price_arr'][$extra_id]) ? $tpl['extra_price_arr'][$extra_id] : null;?></div>
													</td>
													<td style="padding:7px 3px; width: 60px;vertical-align: top;">
														<select name="extra_cnt[<?php echo $index; ?>]" class="pj-form-field pj-extra-qty">
															<?php
															for($i=1; $i<=10;$i++)
															{
																?><option value="<?php echo $i; ?>" <?php echo $tpl['be_quantity_arr'][$extra_id]  == $i ? ' selected="selected"' : NULL; ?>><?php echo $i; ?></option><?php
															}
															?>
														</select>
													</td>
													<td style="padding:7px 3px;  width: 30px;">
													<a class="pj-table-icon-delete opExtraDel" data-id="1" href="#" title="Delete"></a>
													</td>
												</tr>
												<?php
												foreach ($tpl['extra_arr'] as $m)
												{
													if($m['extra_id'] == $extra_id ) {
													?><input type="hidden" name="e_price[<?php echo $index; ?>]" value="<?php echo $m['price'] ?>" ><?php
													}
												}
												$number_of_extra++;
											}
										}
										?>
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
						<?php
						$rental_fee_detail = array();
						$sum = (float)$tpl['arr']['car_rental_fee'] + (float)$tpl['arr']['extra_price'];
						?>
						<p>
							<label class="title bold"><?php __('lblRentalDuration'); ?></label>
							<label id="cr_rental_time" class="content"><?php echo $rental_time;?></label>
							<input type="hidden" id="rental_days" name="rental_days" value="<?php echo $tpl['arr']['rental_days']; ?>"/>
							<input type="hidden" id="rental_hours" name="rental_hours" value="<?php echo $tpl['arr']['rental_hours']; ?>"/>
						</p>
						<p style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perday')) ? 'block' : 'none';?>">
							<label class="title bold"><?php __('lblPricePerDay'); ?></label>
							<label id="cr_price_per_day" class="content"><?php echo $rental_fee_detail[] = pjUtil::formatCurrencySign($tpl['arr']['price_per_day'], $tpl['option_arr']['o_currency']);  ?></label>
							<label id="cr_price_per_day_detail"><?php echo $tpl['arr']['price_per_day_detail']; ?></label>
							<input type="hidden" id="price_per_day" name="price_per_day" value="<?php echo $tpl['arr']['price_per_day']; ?>"/>
							<input type="hidden" id="price_per_day_detail" name="price_per_day_detail" value="<?php echo !empty($tpl['arr']['price_per_day_detail']) ? $tpl['arr']['price_per_day_detail'] : null; ?>"/>
						</p>
						<p style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perhour')) ? 'block' : 'none';?>">
							<label class="title bold"><?php __('lblPricePerHour'); ?></label>
							<label id="cr_price_per_hour" class="content"><?php echo $rental_fee_detail[] = pjUtil::formatCurrencySign($tpl['arr']['price_per_hour'], $tpl['option_arr']['o_currency'])?></label>
							<label id="cr_price_per_hour_detail"><?php echo $tpl['arr']['price_per_hour_detail']; ?></label>
							<input type="hidden" id="price_per_hour" name="price_per_hour" value="<?php echo $tpl['arr']['price_per_hour']; ?>"/>
							<input type="hidden" id="price_per_hour_detail" name="price_per_hour_detail" value="<?php echo !empty($tpl['arr']['price_per_hour_detail']) ? $tpl['arr']['price_per_hour_detail'] : null; ?>"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('lblCarRentalFee'); ?></label>
							<label id="cr_rental_fee" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['car_rental_fee'], $tpl['option_arr']['o_currency'])?></label>
							<label id="cr_rental_fee_detail"><?php echo join(' + ', $rental_fee_detail);?></label>
							<input type="hidden" id="car_rental_fee" name="car_rental_fee" value="<?php echo $tpl['arr']['car_rental_fee']; ?>"/>
						</p>
						<p>
							<label class="title bold"><?php __('booking_extra_price'); ?></label>
							<label id="cr_extra_price" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['extra_price'], $tpl['option_arr']['o_currency'])?></label>
							<input type="hidden" id="extra_price" name="extra_price" value="<?php echo $tpl['arr']['extra_price']; ?>"/>
						</p>
						<p>
							<label class="title bold"><?php __('booking_insurance'); ?></label>
							<label id="cr_insurance" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['insurance'], $tpl['option_arr']['o_currency'])?></label>
							<label id="cr_insurance_detail">
								<?php
								$insurance_types = __('insurance_type_arr', true, false);
								switch ($tpl['option_arr']['o_insurance_payment_type']) {
									case 'perday':
										echo pjUtil::formatCurrencySign($tpl['option_arr']['o_insurance_payment'], $tpl['option_arr']['o_currency']) . ' ' . strtolower($insurance_types['perday']);
									;
									break;
									case 'percent':
										echo $tpl['option_arr']['o_insurance_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($sum, $tpl['option_arr']['o_currency']);
									;
									break;
									case 'perbooking':
										echo pjUtil::formatCurrencySign($tpl['option_arr']['o_insurance_payment'], $tpl['option_arr']['o_currency']) . ' ' . strtolower($insurance_types['perbooking']);
									;
									break;
								}
								$sum += (float) $tpl['arr']['insurance'];
								?>
							</label>
							<input type="hidden" id="insurance" name="insurance" value="<?php echo $tpl['arr']['insurance']; ?>"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_subtotal'); ?></label>
							<label id="cr_sub_total" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['sub_total'], $tpl['option_arr']['o_currency'])?></label>
							<input type="hidden" id="sub_total" name="sub_total" value="<?php echo $tpl['arr']['sub_total']; ?>"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_tax'); ?></label>
							<label id="cr_tax" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['tax'], $tpl['option_arr']['o_currency'])?></label>
							<label id="cr_tax_detail">
								<?php
								if($tpl['option_arr']['o_tax_type'] == '1')
								{
									echo $tpl['option_arr']['o_tax_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($tpl['arr']['sub_total'], $tpl['option_arr']['o_currency']);
								}
								?>
							</label>
							<input type="hidden" id="tax" name="tax" value="<?php echo $tpl['arr']['tax']; ?>"/>
						</p>
						<p>
							<label class="title bold red"><?php __('booking_total_price'); ?></label>
							<label id="cr_total_price" class="content bold"><?php echo pjUtil::formatCurrencySign($tpl['arr']['total_price'], $tpl['option_arr']['o_currency'])?></label>
							<input type="hidden" id="total_price" name="total_price" value="<?php echo $tpl['arr']['total_price']; ?>"/>
						</p>
						<p class="cr-line"></p>
						<p>
							<label class="title bold"><?php __('booking_required_deposit'); ?></label>
							<label id="cr_required_deposit" class="content"><?php echo pjUtil::formatCurrencySign($tpl['arr']['required_deposit'], $tpl['option_arr']['o_currency'])?></label>
							<label id="cr_required_deposit_detail">
								<?php
								switch ($tpl['option_arr']['o_deposit_type'])
								{
									case 'percent':
										echo $tpl['option_arr']['o_deposit_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($tpl['arr']['total_price'], $tpl['option_arr']['o_currency']);
										break;
									case 'amount':
										break;
								}
								?>
							</label>
							<input type="hidden" id="required_deposit" name="required_deposit" value="<?php echo $tpl['arr']['required_deposit']; ?>"/>
						</p>
					</fieldset>
					
					<input type="button" value="<?php __('booking_email_reminder', false, true); ?>" class="pj-button reminder-email" data-id="<?php echo $tpl['arr']['id']; ?>" />
					<input type="button" value="<?php __('booking_sms_reminder', false, true); ?>" class="pj-button reminder-sms" data-id="<?php echo $tpl['arr']['id']; ?>" />
					<div id="dialogReminderEmail" title="<?php __('booking_email_reminder', false, true); ?>" style="display: none"></div>
					<div id="dialogReminderSms" title="<?php __('booking_sms_reminder', false, true); ?>" style="display: none"></div>
				</div>
			</div><!-- tabs-1 -->
			<div id="tabs-2">
				<?php pjUtil::printNotice(__('infoUpdateClientDetailsTitle', true), __('infoUpdateClientDetailsDesc', true)); ?>
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
										?><option value="<?php echo $k; ?>" <?php echo $tpl['arr']['c_title'] == $k ? 'selected="selected"' : '' ?>><?php echo stripslashes($v); ?></option><?php
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
						<input type="text" name="c_name" id="c_name" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_name'])); ?>" class="pj-form-field  w200<?php echo $tpl['option_arr']['o_bf_include_name'] == 3 ? ' required' : NULL; ?>" />
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
							<input type="text" name="c_email" id="c_email" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_email'])); ?>" class="pj-form-field email w200<?php echo $tpl['option_arr']['o_bf_include_email'] == 3 ? ' required' : NULL; ?>" />
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
							<input type="text" name="c_phone" id="c_phone" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_phone'])); ?>" class="pj-form-field w200<?php echo $tpl['option_arr']['o_bf_include_phone'] == 3 ? ' required' : NULL; ?>" />
						</span>
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_company'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_company'); ?></label>
						<input type="text" name="c_company" id="c_company" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_company'])); ?>" class="pj-form-field  w200<?php echo $tpl['option_arr']['o_bf_include_company'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					
					if (in_array($tpl['option_arr']['o_bf_include_address'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_address'); ?></label>
						<input type="text" name="c_address" id="c_address" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_address'])); ?>" class="pj-form-field  w400<?php echo $tpl['option_arr']['o_bf_include_address'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					
					
					if (in_array($tpl['option_arr']['o_bf_include_city'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_city'); ?></label>
						<input type="text" name="c_city" id="c_city" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_city'])); ?>" class="pj-form-field  w200<?php echo $tpl['option_arr']['o_bf_include_city'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
			
					if (in_array($tpl['option_arr']['o_bf_include_state'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_state'); ?></label>
						<input type="text" name="c_state" id="c_state" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_state'])); ?>" class="pj-form-field  w200<?php echo $tpl['option_arr']['o_bf_include_state'] == 3 ? ' required' : NULL; ?>" />
					</p>
					<?php
					}
					if (in_array($tpl['option_arr']['o_bf_include_zip'], array(2, 3)))
					{
						?>
					<p>
						<label class="title"><?php __('booking_zip'); ?></label>
						<input type="text" name="c_zip" id="c_zip" value="<?php echo htmlspecialchars(stripslashes($tpl['arr']['c_zip'])); ?>" class="pj-form-field  w200<?php echo $tpl['option_arr']['o_bf_include_zip'] == 3 ? ' required' : NULL; ?>" />
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
								?><option value="<?php echo $v['id']; ?>" <?php echo $tpl['arr']['c_country'] == $v['id'] ? 'selected="selected"' : '' ;?>><?php echo $v['country_title']; ?></option><?php
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
							?><option value="<?php echo $k; ?>"<?php echo $k != $tpl['arr']['cc_type'] ? NULL : ' selected="selected"'; ?>><?php echo pjSanitize::html($v); ?></option><?php
						}
						?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('booking_cc_number'); ?></label>
						<input type="text" name="cc_num" id="cc_num" class="pj-form-field w200" value="<?php echo pjSanitize::html($tpl['arr']['cc_num']); ?>" autocomplete="off" />
					</p>
					<p>
						<label class="title"><?php __('booking_cc_exp'); ?></label>
						<input type="text" name="cc_exp" id="cc_exp" class="pj-form-field w80" value="<?php echo pjSanitize::html($tpl['arr']['cc_exp']); ?>" autocomplete="off" />
					</p>
					<p>
						<label class="title"><?php __('booking_cc_code'); ?></label>
						<input type="text" name="cc_code" id="cc_code" class="pj-form-field w80" value="<?php echo pjSanitize::html($tpl['arr']['cc_code']); ?>" autocomplete="off" />
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input type="button" id="btnSave4" value="<?php __('btnSave'); ?>" class="pj-button cr-button-validate-save" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
					</p>
				</fieldset>
			</div><!-- tabs-2 -->
			<div id="tabs-3">
				<?php pjUtil::printNotice(__('infoUpdateCollectTitle', true), __('infoUpdateCollectDesc', true)); ?>
				<fieldset class="overflow">
					<legend><?php __('lblPickup'); ?></legend>
					<?php
					$pickup_date = strtotime($tpl['arr']['from']);
					if(!empty($tpl['arr']['pickup_date']))
					{
						$pickup_date = strtotime($tpl['arr']['pickup_date']);
					}
					$pickup_date = date($tpl['option_arr']['o_date_format'], $pickup_date)." ".date('H:i',$pickup_date);
					?>
					<p>
						<label class="title"><?php __('booking_pickup_date'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="pickup_date" id="pickup_date" class="pj-form-field pointer w120 datetimepick" readonly="readonly" value="<?php echo $pickup_date;?>" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</p>
					<p>
						<label class="title"><?php __('booking_car'); ?></label>
						<select name="collect_car_id" id="collect_car_id" class="pj-form-field  w200 required">
							<option value="">-- <?php __('lblChoose'); ?> --</option>
							<?php
							foreach ($tpl['car_arr'] as $v)
							{
								?><option value="<?php echo $v['car_id']; ?>"<?php echo $tpl['arr']['car_id'] == $v['car_id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($v['make'] . " " . $v['model'] . " - " . $v['registration_number']); ?></option><?php
							}
							?>
						</select>
					</p>
					<p>
						<label class="title"><?php __('car_current_mileage') ; ?></label>
						<span class="inline_block">
							<label id="collect_current_mileage" class="content"><?php echo $tpl['booking_car_arr']['mileage'];?>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?></label>
						</span>
					</p>
					<p>
						<label class="title"><?php echo __('booking_pickup_mileage') ; ?></label>
						<input type="text" name="pickup_mileage" id="pickup_mileage" class="pj-form-field w100  digits" value="<?php echo $tpl['arr']['pickup_mileage']; ?>"/>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?>&nbsp;&nbsp;(<a href="#" id="cr_set_as_current" rev="<?php echo $tpl['booking_car_arr']['mileage'];?>"><?php __('booking_set_as_current'); ?></a>)
					</p>
					<p>
						<label class="title">&nbsp;</label>
						<input id="btnSave5" type="button" value="<?php __('btnSave'); ?>" class="pj-button" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
					</p>
				</fieldset><!-- Pick-up -->
			</div><!-- tabs-3 -->
			
			<div id="tabs-4">
				<?php pjUtil::printNotice(__('infoUpdateReturnTitle', true), __('infoUpdateReturnDesc', true)); ?>
				<fieldset class="overflow">
					<legend><?php __('lblReturn'); ?></legend>
					<?php
					$extra_hours_usage  = 0;
					
					$dropoff_date = strtotime($tpl['arr']['to']);
					$dropoff_date = date($tpl['option_arr']['o_date_format'], $dropoff_date)." ".date('H:i',$dropoff_date);
					
					$actual_dropoff_date ='';
					if(!empty($tpl['arr']['actual_dropoff_datetime']))
					{
						$actual_dropoff_date = strtotime($tpl['arr']['actual_dropoff_datetime']);
						$actual_dropoff_date = date($tpl['option_arr']['o_date_format'], $actual_dropoff_date)." ".date('H:i',$actual_dropoff_date);
						
						$seconds = strtotime($tpl['arr']['actual_dropoff_datetime']) - strtotime($tpl['arr']['to']);
						if($seconds > 0)
						{
							$extra_hours_usage = ceil($seconds / 3600);
						}
					}
					?>
					<p>
						<label class="title"><?php __('booking_return_deadline'); ?></label>
						<span class="inline_block">
							<label class="content"><?php echo $dropoff_date;?></label>
						</span>
					</p>
					<p>
						<label class="title"><?php __('booking_return_datetime'); ?></label>
						<span class="pj-form-field-custom pj-form-field-custom-after">
							<input type="text" name="actual_dropoff_datetime" id="actual_dropoff_datetime" value="<?php echo $actual_dropoff_date; ?>" class="pj-form-field pointer w120 datetimepick" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
							<span class="pj-form-field-after"><abbr class="pj-form-field-icon-date"></abbr></span>
						</span>
					</p>
					<p>
						<label class="title"><?php __('booking_extra_hours_usage'); ?></label>
						<span class="inline_block">
							<label id="cr_extra_hours_usage" class="content"><?php echo $extra_hours_usage > 0 ? $extra_hours_usage . ' ' . ($extra_hours_usage > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false)) : __('booking_no', false, true) ;?></label>
						</span>
					</p>
					<p>
						<label class="title"><?php echo __('booking_dropoff_mileage') ; ?></label>
						<span class="inline_block">
							<input type="text" name="dropoff_mileage" id="dropoff_mileage" class="pj-form-field w100  digits" value="<?php echo !empty($tpl['arr']['dropoff_mileage']) ? $tpl['arr']['dropoff_mileage'] : null; ?>"/>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?>
						</span>
					</p>
					<?php
					$actual_mileage = 0;
					$extra_mileage_charge = 0;
					$_em_charge = 0;
					if(!empty($tpl['arr']['dropoff_mileage']) && !empty($tpl['arr']['pickup_mileage']))
					{
						$actual_mileage = $tpl['arr']['dropoff_mileage'] - $tpl['arr']['pickup_mileage'];
					}
					if($actual_mileage > 0)
					{
						$_em_charge = $actual_mileage - ($rental_days * $daily_mileage_limit);
						if($_em_charge > 0)
						{
							$extra_mileage_charge = $_em_charge * $price_for_extra_mileage;
						}
					}
					if($extra_mileage_charge > 0)
					{
						?><input type="hidden" name="extra_mileage_charge" id="extra_mileage_charge" value="<?php echo number_format($extra_mileage_charge,  2, '.', ''); ?>"/><?php
						$extra_mileage_charge = $_em_charge . $tpl['option_arr']['o_unit'] . ' x ' . pjUtil::formatCurrencySign(number_format($price_for_extra_mileage,  2, '.', ''), $tpl['option_arr']['o_currency']) . ' = ' . pjUtil::formatCurrencySign(number_format($extra_mileage_charge,  2, '.', ''), $tpl['option_arr']['o_currency']);
					}else{
						$extra_mileage_charge = __('booking_no', true, false);
						?><input type="hidden" name="extra_mileage_charge" id="extra_mileage_charge"/><?php
					}
					?>
					<p>
						<label class="title"><?php __('booking_extra_mileage_charge'); ?></label>
						<span class="inline_block">
							<label id="cr_extra_mileage_charge" class="content"><?php echo $extra_mileage_charge;?></label>
						</span>
					</p>
					
					<p>
						<label class="title">&nbsp;</label>
						<input  id="btnSave6" type="button" value="<?php __('btnSave'); ?>" class="pj-button" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminBookings&action=pjActionIndex';" />
					</p>
				</fieldset><!-- Drop-off -->
			</div><!-- tabs-4 -->
			
			<div id="tabs-5">
				<?php pjUtil::printNotice(__('infoUpdatePaymentTitle', true), __('infoUpdatePaymentDesc', true)); ?>
				<fieldset class="overflow b10">
					<legend><?php __('lblBasicInfo'); ?></legend>
					<p>
						<label class="title"><?php __('booking_total_price'); ?></label>
						<span class="inline_block">
							<label class="content cr-total-quote float_left"><?php echo pjUtil::formatCurrencySign($tpl['arr']['total_price'], $tpl['option_arr']['o_currency']);?></label>
							&nbsp;&nbsp;
							<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblTotalPriceTip'); ?>"></a>
						</span>
					</p>
					<p>
						<label class="title"><?php __('booking_payments_made'); ?></label>
						<span class="inline_block">
							<label id="pj_collected" class="content float_left">
								<?php echo pjUtil::formatCurrencySign($tpl['collected'], $tpl['option_arr']['o_currency']); ?>
							</label>
							&nbsp;&nbsp;
							<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblPaymentMadeTip'); ?>"></a>
						</span>
					</p>
					<p>
						<label class="title"><?php __('booking_payment_due'); ?></label>
						<span class="inline_block">
							<label id="pj_due_payment"  class="content cr-due-payment  float_left">
								<?php
								$amount_due = $tpl['arr']['total_price'] - $tpl['collected'];
								
								if($amount_due < 0)
								{
									$amount_due = 0;
								}
								echo pjUtil::formatCurrencySign(number_format($amount_due, 2, '.', ''), $tpl['option_arr']['o_currency']);
								?>
							</label>
							&nbsp;&nbsp;
							<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblPaymentDueTip'); ?>"></a>
						</span>
					</p>
				</fieldset><!-- Info -->
				
				<table class="pj-table" id="tblPayment" cellpadding="0" cellspacing="0" style="width: 100%">
					<thead>
						<tr>
							<th><?php __('booking_payment_method'); ?></th>
							<th><?php __('booking_payment_type'); ?></th>
							<th style="width: 150px;"><?php __('booking_payment_amount'); ?></th>
							<th style="width: 100px;"><?php __('booking_payment_status'); ?></th>
							<th style="width: 30px;">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if (isset($tpl['payment_arr']) && count($tpl['payment_arr']) > 0)
						{
							foreach ($tpl['payment_arr'] as $payment)
							{
								?>
								<tr>
									<td>
										<select name="payment_method[<?php echo $payment['id']; ?>]" class="pj-form-field">
											<option value="">---<?php __('lblChoose');?>---</option>
											<?php
											foreach (__('payment_methods',true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo $payment['payment_method'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</td>
									<td>
										<select id="payment_type_<?php echo $payment['id']; ?>" name="payment_type[<?php echo $payment['id']; ?>]" data-index="<?php echo $payment['id']; ?>" class="pj-form-field pj-payment-type" >
											<option value="">---<?php __('lblChoose');?>---</option>
											<?php
											foreach (__('payment_types',true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo $payment['payment_type'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</td>
									<td >
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
											<input type="text" id="amount_<?php echo $payment['id']; ?>" name="amount[<?php echo $payment['id']; ?>]" class="pj-form-field align_right w60 number pj-payment-amount" data-index="<?php echo $payment['id']; ?>" value="<?php echo number_format((float) $payment['amount'], 2, '.', ''); ?>" />
										</span>
									</td>
									<td>
										<select id="payment_status_<?php echo $payment['id']; ?>" name="payment_status[<?php echo $payment['id']; ?>]" class="pj-form-field pj-payment-status" >
											<?php
											foreach (__('payment_statuses',true) as $k => $v)
											{
												?><option value="<?php echo $k; ?>"<?php echo $payment['status'] == $k ? 'selected="selected"' : null;?>><?php echo $v; ?></option><?php
											}
											?>
										</select>
									</td>
									<td><a class="pj-table-icon-delete btnDeletePayment" title="<?php __('lblDelete'); ?>" href="#" data-id="<?php echo $payment['id']; ?>"></a></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr class="notFound">
								<td colspan="5"><?php __('lblNoRecordsFound');?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<p>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" id="btnSavePayment" />
					<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button" id="btnAddPayment" />
				</p>
				
				<div id="dialogDeletePayment" title="<?php __('lblDeleteConfirmation')?>" style="display: none"><?php __('lblDelPaymentConfirm'); ?></div>
			</div><!-- tabs-5 -->
		</div>
		
	</form>
	
	<table id="tblPaymentsClone" style="display: none">
		<tbody>
			<tr>
				<td>
					<select name="payment_method[{INDEX}]" class="pj-form-field" >
						<option value="">---<?php __('lblChoose');?>---</option>
						<?php
						foreach (__('payment_methods',true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</td>
				<td>
					<select id="payment_type_{INDEX}" name="payment_type[{INDEX}]" class="pj-form-field {PTCLASS}" data-index="{INDEX}">
						<option value="">---<?php __('lblChoose');?>---</option>
						<?php
						foreach (__('payment_types',true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</td>
				<td >
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" id="amount_{INDEX}" name="amount[{INDEX}]" class="pj-form-field align_right w60 number {ACLASS}" data-index="{INDEX}" />
					</span>
				</td>
				<td>
					<select id="status_{INDEX}" name="status[{INDEX}]" class="pj-form-field {SCLASS}" >
						<?php
						foreach (__('payment_statuses',true) as $k => $v)
						{
							?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
						}
						?>
					</select>
				</td>
				<td><a class="pj-table-icon-delete btnRemovePayment" title="<?php __('lblDelete'); ?>" href="#"></a></td>
			</tr>
		</tbody>
	</table>
				
	<div id="dialogCancel" title="<?php __('lblCancelBooking');?>" style="display: none"><?php __('lblCancelBookingConfirm');?></div>
	
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.dateRangeValidation = "<?php __('lblBookingDateRangeValidation'); ?>";
	myLabel.numDaysValidation = "<?php __('lblBookingNumDaysValidation'); ?>";
	myLabel.phone_not_available = "<?php __('lblPhoneNotAvailable'); ?>";
	myLabel.numberOfExtras = <?php echo $number_of_extra;?>;
	myLabel.security_deposit = <?php echo $tpl['option_arr']['o_security_payment'];?>;
	myLabel.currency = "<?php echo $tpl['option_arr']['o_currency'];?>";
	myLabel.mileage_unit = "<?php echo $tpl['option_arr']['o_unit'];?>";
	</script>
	<?php
	if (isset($_GET['tab_id']) && !empty($_GET['tab_id']))
	{
		$tab_id = explode("-", $_GET['tab_id']);
		$tab_id = (int) $tab_id[1] - 1;
		$tab_id = $tab_id < 0 ? 0 : $tab_id;
		?>
		<script type="text/javascript">
		(function ($) {
			$(function () {
				$("#tabs").tabs("option", "selected", <?php echo $tab_id; ?>);
			});
		})(jQuery_1_8_2);
		</script>
		<?php
	}
}
?>