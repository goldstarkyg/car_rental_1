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
	include_once PJ_VIEWS_PATH . 'pjLayouts/elements/optmenu.php';
	pjUtil::printNotice(__('infoBookingsTitle', true), __('infoBookingsBody', true));
	
	if (isset($tpl['arr']))
	{
		if (is_array($tpl['arr']))
		{
			$count = count($tpl['arr']);
			if ($count > 0)
			{
				?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionUpdate" method="post" class="form pj-form">
					<input type="hidden" name="options_update" value="1" />
					<input type="hidden" name="next_action" value="pjActionBooking" />
					<table class="pj-table" cellpadding="0" cellspacing="0" style="width: 100%">
						<thead>
							<tr>
								<th><?php __('lblOption'); ?></th>
								<th><?php __('lblValue'); ?></th>
							</tr>
						</thead>
						<tbody>
	
				<?php
				for ($i = 0; $i < $count; $i++)
				{
					if ($tpl['arr'][$i]['tab_id'] != 3 || (int) $tpl['arr'][$i]['is_visible'] === 0 || $tpl['arr'][$i]['key'] == 'o_tax_type') continue;
					
					$rowClass = NULL;
					$rowStyle = NULL;
					if (in_array($tpl['arr'][$i]['key'], array('o_paypal_address')))
					{
						$rowClass = " boxPaypal";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_paypal'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_authorize_merchant_id', 'o_authorize_transkey', 'o_authorize_timezone', 'o_authorize_md5_hash')))
					{
						$rowClass = " boxAuthorize";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_authorize'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					if (in_array($tpl['arr'][$i]['key'], array('o_bank_account')))
					{
						$rowClass = " boxBankAccount";
						$rowStyle = "display: none";
						if ($tpl['option_arr']['o_allow_bank'] == 'Yes')
						{
							$rowStyle = NULL;
						}
					}
					
					?>
					<tr class="pj-table-row-odd<?php echo $rowClass; ?>" style="<?php echo $rowStyle; ?>">
						<td width="50%">
							<span class="block bold"><?php __('opt_' . $tpl['arr'][$i]['key']); ?></span>
							<span class="fs10"><?php __('opt_' . $tpl['arr'][$i]['key'].'_text'); ?></span>
						</td>
						<td>
							<?php
							switch ($tpl['arr'][$i]['type'])
							{
								case 'string':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w400" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'text':
									?><textarea name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field" style="width: 400px; height: 80px;"><?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?></textarea><?php
									break;
								case 'int':
									?>
									<?php
									
									if($tpl['arr'][$i]['key'] == 'o_security_payment'){ ?>
										<span class="pj-form-field-custom pj-form-field-custom-before">
											<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
											<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field w60 align_right" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" />
										</span>
									<?php } else { ?>
									<input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-int w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									}
									
									if($tpl['arr'][$i]['key'] == 'o_tax_payment'){ ?>
										<select name="value-int-o_tax_type" class="pj-form-field">
										<?php
										foreach (__('tax_type_arr', true) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo @$tpl['option_arr']["o_tax_type"] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
										}
										?>
										</select>
										<?php
									}
									
									if($tpl['arr'][$i]['key'] == 'o_insurance_payment'){ ?>
										<select name="value-int-o_insurance_payment_type" class="pj-form-field">
										<?php
										foreach (__('insurance_type_arr', true) as $k => $v)
										{
											?><option value="<?php echo $k; ?>"<?php echo @$tpl['option_arr']["o_insurance_payment_type"] == $k ? ' selected="selected"' : NULL; ?>><?php echo $v; ?></option><?php
										}
										?>
										</select>
										<?php
									}
									
									if($tpl['arr'][$i]['key'] == 'o_deposit_payment'){ ?>
										<select name="value-enum-o_deposit_type" class="pj-form-field">
										<?php
										$default = explode("::", $tpl['o_arr']['o_deposit_type']['value']);
										$enum = explode("|", $default[0]);
										
										$enumLabels = array();
										if (!empty($tpl['o_arr']['o_deposit_type']['label']) && strpos($tpl['o_arr']['o_deposit_type']['label'], "|") !== false)
										{
											$enumLabels = explode("|", $tpl['o_arr']['o_deposit_type']['label']);
										}
										
										foreach ($enum as $k => $el)
										{
											?><option value="<?php echo $default[0].'::'.$el; ?>"<?php echo $default[1] == $el ? ' selected="selected"' : NULL; ?>><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										}
										?>
										</select>
									<?php
									}
									if($tpl['arr'][$i]['key'] == 'o_min_hour'){
										?>&nbsp;<?php
										if ($tpl['option_arr']['o_booking_periods'] == 'both'){
											?>
											<span class="boxMinimumBoth" style="display: inline;"><?php __('lblHours'); ?></span>
											<span class="boxMinimumDay" style="display: none;"><?php __('lblDays'); ?></span>
											<?php
										}else{
											?>
											<span class="boxMinimumBoth" style="display: none;"><?php __('lblHours'); ?></span>
											<span class="boxMinimumDay" style="display: inline;"><?php __('lblDays'); ?></span>
											<?php
										}
									}
									if(in_array($tpl['arr'][$i]['key'], array('o_free_up_per_day', 'o_new_day_per_day', 'o_free_up_per_both_hourly', 'o_new_day_per_both_hourly', 'o_free_up_per_both_special', 'o_new_day_per_both_special', 'o_booking_pending'))){
										?>&nbsp;<?php __('lblHours');
									}
									break;
								case 'float':
									?><input type="text" name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field field-float w60" value="<?php echo htmlspecialchars(stripslashes($tpl['arr'][$i]['value'])); ?>" /><?php
									break;
								case 'enum':
									?><select name="value-<?php echo $tpl['arr'][$i]['type']; ?>-<?php echo $tpl['arr'][$i]['key']; ?>" class="pj-form-field">
									<?php
									$default = explode("::", $tpl['arr'][$i]['value']);
									$enum = explode("|", $default[0]);
									
									$enumLabels = array();
									if (!empty($tpl['arr'][$i]['label']) && strpos($tpl['arr'][$i]['label'], "|") !== false)
									{
										$enumLabels = explode("|", $tpl['arr'][$i]['label']);
									}
									
									foreach ($enum as $k => $el)
									{
										if ($default[1] == $el)
										{
											?><option value="<?php echo $default[0].'::'.$el; ?>" selected="selected"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										} else {
											?><option value="<?php echo $default[0].'::'.$el; ?>"><?php echo array_key_exists($k, $enumLabels) ? stripslashes($enumLabels[$k]) : stripslashes($el); ?></option><?php
										}
									}
									?>
									</select>
									<?php
									break;
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
						</tbody>
					</table>
					
					<p><input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" /></p>
				</form>
				
				<?php
			}
		}
	}
}
?>