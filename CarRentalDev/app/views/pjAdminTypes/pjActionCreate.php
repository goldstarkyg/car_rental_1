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
	?>
	<div class="ui-tabs ui-widget ui-widget-content ui-corner-all b10">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex"><?php __('lblAllTypes'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('lblAllExtras'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoAddTypeTitle', true), __('infoAddTypeBody', true)); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionCreate" method="post" id="frmCreate" class="pj-form form" enctype="multipart/form-data">
	<input type="hidden" name="action_create" value="1" />
	
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
						<input type="text" name="i18n[<?php echo $v['id']; ?>][name]" class="pj-form-field w200 <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"  />
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
						<textarea name="i18n[<?php echo $v['id']; ?>][description]" class="pj-form-field w400 h100 "></textarea>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php echo __('lblPricePerDay') ; ?></label>
			    <span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price_per_day" id="price_per_day" class="pj-form-field w50 align_right"/>
				</span>
				&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('lblPricePerDayTip', true)); ?>"></a>
			</p>
			<p>
				<label class="title"><?php echo __('lblPricePerHour') ; ?></label>
			    <span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price_per_hour" id="price_per_hour" class="pj-form-field w50 align_right"/>
				</span>
				&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('lblPricePerHourTip', true)); ?>"></a>
			</p>
			<p>
		   		<label class="title"><?php echo __('type_default_distance') ; ?></label>
		   		<span class="inline_block">
		   			<input type="text" name="default_distance" id="default_distance" class="pj-form-field w70 digits required"/>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?>&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('type_default_distance_tip', true)); ?>"></a>
		   		</span>
		   	</p>
			<p>
				<label class="title"><?php echo __('type_extra_price') ; ?></label>
			    <span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="extra_price" id="extra_price" class="pj-form-field w50 align_right"/>
					<span style="line-height:30px;">&nbsp;/<?php echo $tpl['option_arr']['o_unit'] ?></span> 
				</span>
				&nbsp;&nbsp;<a class="pj-form-langbar-tip pj-selector-langbar-tip" href="#" title="<?php echo nl2br(__('type_extra_price_tip', true)); ?>"></a>
			</p>
			<p><label class="title"><?php echo __('type_image') ; ?></label><input type="file" name="image" id="image" class="pj-form-field " /></p>
			<p><label class="title"><?php echo __('type_passengers') ; ?></label><input type="text" name="passengers" id="passengers" class="pj-form-field w70 digits required" /></p>
			<p><label class="title"><?php echo __('type_luggages') ; ?></label><input type="text" name="luggages" id="luggages" class="pj-form-field w70 digits required" /></p>
			<p><label class="title"><?php echo __('type_doors') ; ?></label><input type="text" name="doors" id="doors" class="pj-form-field w70 digits required" /></p>
			<p>
				<label class="title"><?php __('type_transmission'); ?></label>
					<select name="transmission" class="pj-form-field required" >
					<option value=""><?php __('cr_choose'); ?></option>
					<?php
					foreach (__('type_transmissions', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v; ?></option><?php
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
								<option value="<?php echo $extra['id']; ?>" ><?php echo $extra['name'] ?></option>
								<?php
							}
						?>
					</select>
					&nbsp;&nbsp;
					<a href="#" class="pj-form-langbar-tip listing-tip" title="<?php __('lblExtrasTip'); ?>"></a>
				<?php } ?>
					
				</span>
		    </p>
		   
			
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminTypes&action=pjActionIndex';" />
			</p>
		</div>
	</form>
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
}
?>