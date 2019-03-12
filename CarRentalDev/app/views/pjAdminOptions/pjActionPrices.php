<?php
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
$jqDateFormat = pjUtil::jqDateFormat($tpl['option_arr']['o_date_format']);
	
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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex"><?php __('lblAllTypes'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('lblAllExtras'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPrices"><?php __('menuPrices'); ?></a></li>
		</ul>
	</div>
	
	<?php
	pjUtil::printNotice(__('infoPricesTitle', true), __('infoPricesBody', true));
	if (isset($tpl['price_arr']) && !empty($tpl['price_arr']))
	{
		foreach ($tpl['price_arr'] as $range)
		{
			$from = strtotime($range['date_from']);
			$to = strtotime($range['date_to']);
			
			foreach ($tpl['price_arr'] as $tmp)
			{
				if ($range['id'] == $tmp['id'])
				{
					continue;
				}
				if (strtotime($tmp['date_from']) <= $to && strtotime($tmp['date_to']) >= $from)
				{
					$err[] = array($range, $tmp);
				}
			}
		}
	}
	
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPrices" method="post" class="form pj-form" id="frmPrice">
		<input type="hidden" name="options_update" value="1" />
		
		<table class="pj-table" id="tblPrices" cellpadding="0" cellspacing="0" style="width: 100%">
			<thead>
				<tr>
					<th><?php __('price_type'); ?></th>
					<th><?php __('price_from'); ?></th>
					<th><?php __('price_to'); ?></th>
					<th style="width: 210px;" colspan="3"><?php __('items_length'); ?></th>
					<th style="width: 130px;" colspan="2"><?php __('price_price'); ?></th>
					<th style="width: 5%"></th>
				</tr>
			</thead>
			<tbody>
			<?php
			if (isset($tpl['price_arr']) && count($tpl['price_arr']) > 0)
			{
				$i = 1;
				$items_price_per = __('items_price_per', true);
				foreach ($tpl['price_arr'] as $price)
				{
					?>
					<tr>
						<td class="td_padding">
							<select name="type_id[<?php echo $price['id']; ?>]" class="pj-form-field  " style="width: 130px;">
							<?php 
							foreach ($tpl['type_arr'] as $type)
							{
								?><option value="<?php echo $type['id']; ?>" <?php echo $price['type_id'] == $type['id'] ? 'selected="selected"' : ''; ?>><?php echo stripslashes($type['name']); ?></option><?php
							}
							?>
							</select>
						</td>
						<td  class="td_padding">
							<input type="text" name="date_from[<?php echo $price['id']; ?>]" value="<?php echo pjUtil::formatDate($price['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer  datepick" style="width: 75px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						</td>
						<td  class="td_padding">
							<input type="text" name="date_to[<?php echo $price['id']; ?>]" value="<?php echo pjUtil::formatDate($price['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer  datepick" style="width: 75px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						</td>
						<td  class="td_padding_no_border_right">
							<input type="text" name="from[<?php echo $price['id']; ?>]" class="pj-form-field w50 spinner" value="<?php echo (int) $price['from']; ?>" />
						</td>
						<td  class="td_padding_no_border_right">
							<input type="text" name="to[<?php echo $price['id']; ?>]" class="pj-form-field w50 spinner" value="<?php echo (int) $price['to']; ?>" />
						</td>
						<td  class="td_padding"><select name="price_per[<?php echo $price['id']; ?>]" class="pj-form-field pPeriod" >
								<option value="hour"<?php echo $price['price_per'] != 'hour' ? NULL : ' selected="selected"'; ?>><?php __('items_hour_plural'); ?></option>
								<option value="day"<?php echo $price['price_per'] != 'day' ? NULL : ' selected="selected"'; ?>><?php __('items_day_plural'); ?></option>
							</select>
						</td>
						
						<td  class="td_padding_no_border_right" style="padding-top: 9px;">
							<span class="pj-form-field-custom pj-form-field-custom-before">
								<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
								<input type="text" name="price[<?php echo $price['id']; ?>]" class="pj-form-field align_right w44 " value="<?php echo number_format((float) $price['price'], 2, '.', ''); ?>" />
							</span>
							
						</td>
						<td  class="td_padding" style="padding-top: 18px;">
							<span class="pHour" style="display: <?php echo $price['price_per'] == 'hour' ? NULL : 'none'; ?>"><?php echo $items_price_per['hour']; ?></span>
							<span class="pDay" style="display: <?php echo  $price['price_per'] == 'day' ? NULL : 'none'; ?>"><?php echo $items_price_per['day']; ?></span>
						</td>
						
						<td><a class="pj-table-icon-delete btnDeletePrice" title="<?php __('lblDelete'); ?>" href="#" data-id="<?php echo $price['id']; ?>"></a></td>
					</tr>
					
					<?php
					 /*<?php echo $v['id']; ?>&amp;listing_id=<?php echo $tpl['arr']['id']; ?>*/
				}
			} else {
				?>
				<tr class="notFound">
					<td colspan="9"><?php __('price_empty'); ?></td>
				</tr>
				<?php
			}
			?>
			
				
			</tbody>
		</table>
	<br />
	<p>
		<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
		<input type="button" value="<?php __('btnAdd'); ?>" class="pj-button" id="btnAddPrice" />
		<span class="bxStatus bxStatusStart" style="display: none"><?php echo __('price_status_start'); ?></span>
		<span class="bxStatus bxStatusEnd" style="display: none"><?php echo __('price_status_end'); ?></span>
	</p>
	

					
	</form>
	
	<div id="dialogDeletePrice" title="Delete confirmation" style="display: none"><?php __('lblSure'); ?></div>
	<?php $index = 'x_' . rand(1, 999999); ?>
	<table id="tblPricesClone" style="display: none">
		<tbody>
			<tr>
				<td  class="td_padding">
					<select name="type_id[{INDEX}]" class="pj-form-field  w130" style="width: 130px;">
					<?php 
					foreach ($tpl['type_arr'] as $type)
					{
						?><option value="<?php echo $type['id']; ?>"><?php echo stripslashes($type['name']); ?></option><?php
					}
					?>
					</select>
				</td>
				
				<td  class="td_padding">
					<input type="text" name="date_from[{INDEX}]" class="pj-form-field pointer  datepick" style="width: 75px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				</td>
				<td  class="td_padding">
					<input type="text" name="date_to[{INDEX}]" class="pj-form-field pointer  datepick" style="width: 75px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
				</td>
				
				<td  class="td_padding_no_border_right">
					<input type="text" name="from[{INDEX}]" class="pj-form-field w50 spin ui-spinner-input " />
				</td>
				<td class="td_padding_no_border_right">
					<input type="text" name="to[{INDEX}]" class="pj-form-field w50 spin ui-spinner-input " />
				</td>
				<td class="td_padding_no_border_right">
					<select name="price_per[{INDEX}]" class="pj-form-field pPeriod" >
						<option value="hour"><?php __('items_hour_plural'); ?></option>
						<option value="day"><?php __('items_day_plural'); ?></option>
					</select>
				</td>
				
				<td  class="td_padding_no_border_right" style="padding-top: 9px;">
					<span class="pj-form-field-custom pj-form-field-custom-before">
						<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
						<input type="text" name="price[{INDEX}]" class="pj-form-field align_right w44 number" />
					</span>
					
				</td>
				<td  class="td_padding" style="padding-top: 18px;">
					<?php
					$items_price_per = __('items_price_per', true);
					?>
					<span class="pHour" ><?php echo $items_price_per['hour']; ?></span>
					<span class="pDay" style="display: none"><?php echo $items_price_per['day']; ?></span>
				</td>
				<td><a class="pj-table-icon-delete btnRemovePrice" title="<?php __('lblDelete'); ?>" href="#"></a></td>
			</tr>
		</tbody>
	</table>

<?php
}
?>