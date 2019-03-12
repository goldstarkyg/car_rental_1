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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex"><?php __('lblAllTypes'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('lblAllExtras'); ?></a></li>
		</ul>
	</div>
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblDetails', false, true); ?></a></li>
			<li><a href="#tabs-2"><?php __('lblCustomRates', false, true); ?></a></li>
		</ul>
		<div id="tabs-1" class="tab1-type">
			<?php pjUtil::printNotice(__('infoUpdateTypeTitle', true), __('infoUpdateTypeBody', true)); ?>
			<div id="pj_type_loader"></div>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionUpdate" method="post" id="frmUpdate" class="form pj-form" enctype="multipart/form-data">
				<input type="hidden" name="action_update" value="1" />
				<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
				
				<?php
				$locale = isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : NULL;
				if (is_null($locale))
				{
					foreach ($tpl['lp_arr'] as $v)
					{
						if ($v['is_default'] == 1)
						{
							$locale = $v['id'];
							break;
						}
					}
				}
				if (is_null($locale))
				{
					$locale = @$tpl['lp_arr'][0]['id'];
				}
				?>
				
				<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
				<div class="multilang b10"></div>
				<?php endif;?>
				<div class="clear_both">
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title"><?php __('type_name'); ?></label>
							<span class="inline_block">
								<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w200 <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"  value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['name'])); ?>"/>
								<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif;?>
							</span>
						</p>
						<?php
					}
					?>
					<?php
					foreach ($tpl['lp_arr'] as $v)
					{
						?>
						<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
							<label class="title"><?php __('type_description'); ?></label>
							<span class="inline_block">
								<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w400 h100 "><?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['description'])); ?></textarea>
								<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
								<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
								<?php endif;?>
							</span>
						</p>
						<?php
					}
					?>
					
					<p><label class="title"><?php echo __('type_image') ; ?></label><input type="file" name="image" id="image" class="pj-form-field " /></p>
					<?php 
					if (is_file(PJ_INSTALL_PATH.$tpl['arr']['thumb_path']))
					{ 
						?>
						<div class="pj-image-outer">
							<label class="title">&nbsp;</label>
							<div class="pj-image-inner">
								<img src="<?php echo PJ_INSTALL_URL.$tpl['arr']['thumb_path']; ?>" alt="" />
								<a class="pj-image-delete" href="#" data-id="<?php echo $tpl['arr']['id']; ?>"><?php __('btnDelete')?></a>
							</div>
						</div>
						<br/>
						<?php 
					} 
					?>
					<p>
						<label class="title"><?php echo __('lblPricePerDay') ; ?></label>
					    <span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="price_per_day" id="price_per_day" class="pj-form-field w50 align_right" value="<?php echo $tpl['arr']['price_per_day'] ?>"/>
						</span>
						&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('lblPricePerDayTip', true)); ?>"></a>
					</p>
					<p>
						<label class="title"><?php echo __('lblPricePerHour') ; ?></label>
					    <span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="price_per_hour" id="price_per_hour" class="pj-form-field w50 align_right" value="<?php echo $tpl['arr']['price_per_hour'] ?>"/>
						</span>
						&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('lblPricePerHourTip', true)); ?>"></a>
					</p>
					<p>
				   		<label class="title"><?php echo __('type_default_distance') ; ?></label>
				   		<span class="inline_block">
				   			<input type="text" name="default_distance" id="default_distance" class="pj-form-field w70 digits required" value="<?php echo $tpl['arr']['default_distance'] ?>"/>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?>&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('type_default_distance_tip', true)); ?>"></a>
				   		</span>
				   	</p>
					<p>
						<label class="title"><?php echo __('type_extra_price') ; ?></label>
					    <span class="pj-form-field-custom pj-form-field-custom-before">
							<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
							<input type="text" name="extra_price" id="extra_price" class="pj-form-field w50 align_right" value="<?php echo $tpl['arr']['extra_price'] ?>" />
							<span style="line-height:30px;">&nbsp;/<?php echo $tpl['option_arr']['o_unit'] ?></span> 
						</span>
						&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('type_extra_price_tip', true)); ?>"></a>
					</p>
					<p><label class="title"><?php echo __('type_passengers') ; ?></label><input type="text" name="passengers" id="passengers" class="pj-form-field w70 digits required" value="<?php echo (int) $tpl['arr']['passengers'] ?>"/></p>
					<p><label class="title"><?php echo __('type_luggages') ; ?></label><input type="text" name="luggages" id="luggages" class="pj-form-field w70 digits required" value="<?php echo (int) $tpl['arr']['luggages'] ?>"/></p>
					<p><label class="title"><?php echo __('type_doors') ; ?></label><input type="text" name="doors" id="doors" class="pj-form-field w70 digits required" value="<?php echo (int) $tpl['arr']['doors'] ?>"/></p>
					<p>
						<label class="title"><?php __('type_transmission'); ?></label>
							<select name="transmission" class="pj-form-field required" >
							<option value=""><?php __('cr_choose'); ?></option>
							<?php
							foreach (__('type_transmissions', true) as $k => $v)
							{
								?><option value="<?php echo $k; ?>" <?php echo $k == $tpl['arr']['transmission'] ? 'selected="selected"' : '' ?>><?php echo $v; ?></option><?php
							}
							?>
							</select>
					</p>
				    					
					<p><label class="title"><?php echo __('type_extras'); ?></label>
					<span class="block" style="margin-left: 130px; margin-top: 3px">
					
          <?php 
				if(count($tpl['extra_arr']) == 0 ) 
				{ 
					$message = __('type_empty_extra', true);
					$message = str_replace("{STAG}", '<a href="'.PJ_INSTALL_URL.'index.php?controller=pjAdminExtras&action=pjActionCreate">', $message);
					$message = str_replace("{ETAG}", '</a>', $message);
					echo $message;
				}else { 
					?>
        
						<select name="extra_id[]" id="extra_id" class="pj-form-field w300" size="5" multiple="multiple">
							<?php
								foreach ($tpl['extra_arr'] as $extra)
								{
									?>
									<option value="<?php echo $extra['id']; ?>" <?php echo in_array($extra['id'],$tpl['type_extra_arr'])? ' selected="selected"' : NULL; ?>><?php echo $extra['name'] ?></option>
									<?php
								}
							?>
						</select>
						&nbsp;&nbsp;
						<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblExtrasTip'); ?>"></a>
					<?php } ?>
					</span>
					
					<p>
						<label class="title">&nbsp;</label>
						<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
						<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminTypes&action=pjActionIndex';" />
					</p>
					</div>
			</form>
		</div><!-- tabs-1 -->
		<div id="tabs-2">
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
				<input type="hidden" name="type_id" value="<?php echo $tpl['arr']['id'];?>" />
				
				<table class="pj-table" id="tblPrices" cellpadding="0" cellspacing="0" style="width: 100%">
					<thead>
						<tr>
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
								<td  class="td_padding">
									<input type="text" name="date_from[<?php echo $price['id']; ?>]" data-index="<?php echo $price['id']; ?>" value="<?php echo pjUtil::formatDate($price['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer required datepick" style="width: 95px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
								</td>
								<td  class="td_padding">
									<input type="text" name="date_to[<?php echo $price['id']; ?>]" data-index="<?php echo $price['id']; ?>" value="<?php echo pjUtil::formatDate($price['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format']); ?>" class="pj-form-field pointer required datepick" style="width: 95px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
								</td>
								<td  class="td_padding_no_border_right">
									<label class="block float_left r3 t5"><?php __('lblFrom');?>:</label>
									<input type="text" name="from[<?php echo $price['id']; ?>]" class="pj-form-field w50<?php echo $tpl['option_arr']['o_booking_periods'] == 'both' && $price['price_per'] == 'hour' ? ' hour-from-spinner' : ' from-spinner';?>" value="<?php echo (int) $price['from']; ?>" />
								</td>
								<td  class="td_padding_no_border_right">
									<label class="block float_left r3 t5"><?php __('lblTo');?>:</label>
									<input type="text" name="to[<?php echo $price['id']; ?>]" class="pj-form-field w50<?php echo $tpl['option_arr']['o_booking_periods'] == 'both' && $price['price_per'] == 'hour' ? ' hour-to-spinner' : ' to-spinner';?>" value="<?php echo (int) $price['to']; ?>" />
								</td>
								<td  class="td_padding">
									<?php
									if($tpl['option_arr']['o_booking_periods'] == 'both'){ 
										?>
										<select name="price_per[<?php echo $price['id']; ?>]" class="pj-form-field pPeriod" >
											<option value="hour"<?php echo $price['price_per'] != 'hour' ? NULL : ' selected="selected"'; ?>><?php __('items_hour_plural'); ?></option>
											<option value="day"<?php echo $price['price_per'] != 'day' ? NULL : ' selected="selected"'; ?>><?php __('items_day_plural'); ?></option>
										</select>
										<?php
									} 
									?>
								</td>
								
								<td  class="td_padding_no_border_right" style="padding-top: 9px;">
									<span class="pj-form-field-custom pj-form-field-custom-before">
										<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
										<input type="text" name="price[<?php echo $price['id']; ?>]" class="pj-form-field align_right w44 " value="<?php echo number_format((float) $price['price'], 2, '.', ''); ?>" />
									</span>
									
								</td>
								<td  class="td_padding" style="padding-top: 18px;">
									<?php
									if($tpl['option_arr']['o_booking_periods'] == 'both'){ 
										?>
										<span class="pHour" style="display: <?php echo $price['price_per'] == 'hour' ? NULL : 'none'; ?>"><?php echo $items_price_per['hour']; ?></span>
										<span class="pDay" style="display: <?php echo  $price['price_per'] == 'day' ? NULL : 'none'; ?>"><?php echo $items_price_per['day']; ?></span>
										<?php
									}elseif($tpl['option_arr']['o_booking_periods'] == 'perday'){
										?>
										<span class="pDay"><?php echo $items_price_per['day']; ?></span>
										<?php
									}elseif($tpl['option_arr']['o_booking_periods'] == 'perhour'){
										?>
										<span class="pHour"><?php echo $items_price_per['hour']; ?></span>
										<?php
									} 
									?>
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
			
			<div id="dialogDeletePrice" title="<?php __('lblDeleteConfirmation')?>" style="display: none"><?php __('lblSure'); ?></div>
			<div id="dialogDeleteImage" title="<?php __('lblDeleteConfirmation')?>" style="display: none"><?php __('lblDeleteImageConfirm'); ?></div>
			<?php $index = 'x_' . rand(1, 999999); ?>
			<table id="tblPricesClone" style="display: none">
				<tbody>
					<tr>
						<td  class="td_padding">
							<input type="text" name="date_from[{INDEX}]" data-index="{INDEX}" class="pj-form-field pointer required datepick" style="width: 95px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						</td>
						<td  class="td_padding">
							<input type="text" name="date_to[{INDEX}]" data-index="{INDEX}" class="pj-form-field pointer required datepick" style="width: 95px; padding: 7px 4px;" readonly="readonly" rel="<?php echo $week_start; ?>" rev="<?php echo $jqDateFormat; ?>" />
						</td>
						
						<td  class="td_padding_no_border_right">
							<label class="block float_left r3 t5"><?php __('lblFrom');?>:</label>
							<input type="text" name="from[{INDEX}]" class="pj-form-field w50<?php echo $tpl['option_arr']['o_booking_periods'] == 'both' ? ' hour-from-spin' : ' from-spin';?> ui-spinner-input " />
						</td>
						<td class="td_padding_no_border_right">
							<label class="block float_left r3 t5"><?php __('lblTo');?>:</label>
							<input type="text" name="to[{INDEX}]" class="pj-form-field w50<?php echo $tpl['option_arr']['o_booking_periods'] == 'both' ? ' hour-to-spin' : ' to-spin';?> spin ui-spinner-input " />
						</td>
						<td class="td_padding">
							<?php
							if($tpl['option_arr']['o_booking_periods'] == 'both'){ 
								?>
								<select name="price_per[{INDEX}]" class="pj-form-field pPeriod" >
									<option value="hour"><?php __('items_hour_plural'); ?></option>
									<option value="day"><?php __('items_day_plural'); ?></option>
								</select>
								<?php
							} 
							?>
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
							<?php
							if($tpl['option_arr']['o_booking_periods'] == 'both'){ 
								?>
								<span class="pHour" ><?php echo $items_price_per['hour']; ?></span>
								<span class="pDay" style="display: none"><?php echo $items_price_per['day']; ?></span>
								<?php
							}elseif($tpl['option_arr']['o_booking_periods'] == 'perday'){
								?>
								<span class="pDay"><?php echo $items_price_per['day']; ?></span>
								<?php
							}elseif($tpl['option_arr']['o_booking_periods'] == 'perhour'){
								?>
								<span class="pHour"><?php echo $items_price_per['hour']; ?></span>
								<?php
							} 
							?>
						</td>
						<td><a class="pj-table-icon-delete btnRemovePrice" title="<?php __('lblDelete'); ?>" href="#"></a></td>
					</tr>
				</tbody>
			</table>
		</div><!-- tabs-2 -->
	</div>
	<div style="overflow: hidden; height: 0px ! important;" class="bxRateErrors"></div>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	
	(function ($) {
		$(function() {
			$(".multilang").multilang({
				langs: <?php echo $tpl['locale_str']; ?>,
				flagPath: "<?php echo PJ_FRAMEWORK_LIBS_PATH; ?>pj/img/flags/",
				select: function (event, ui) {
					
				}
			});
			$(".multilang").find("a[data-index='<?php echo $locale; ?>']").trigger("click");
		});
	})(jQuery_1_8_2);
	
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