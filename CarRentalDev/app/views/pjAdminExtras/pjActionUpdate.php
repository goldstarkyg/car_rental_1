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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex"><?php __('lblAllTypes'); ?></a></li>
			<li class="ui-state-default ui-corner-top ui-tabs-active ui-state-active"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionIndex"><?php __('lblAllExtras'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoUpdateExtraTitle', true), __('infoUpdateExtraBody', true)); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminExtras&amp;action=pjActionUpdate" method="post" id="frmUpdate" class="form pj-form">
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
					<label class="title"><?php __('extra_title'); ?></label>
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
			
			<p>
				<label class="title"><?php __('extra_price'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-text"><?php echo pjUtil::formatCurrencySign(NULL, $tpl['option_arr']['o_currency'], ""); ?></abbr></span>
					<input type="text" name="price" id="price" class="pj-form-field w70 align_right" value="<?php echo  $tpl['arr']['price']  ?>"/> 
				</span>
				<span style="clear:both; " class="pj-form-field-custom pj-form-field-custom-before">
					<select name="per" class="pj-form-field" >
					<?php
					foreach (__('extra_per', true) as $k => $v)
					{
						?><option value="<?php echo $k; ?>" <?php echo  $tpl['arr']['per'] == $k ? "selected='selected'" : "";?>><?php echo $v; ?></option><?php
					}
					?>
					</select>
				</span>	
			</p>
			<p>
				<label class="title"><?php __('extra_type'); ?></label>
				<span class="inline_block t5">
					<span class="block float_left r20">
						<input type="radio" name="type" id="type_single" value="single"<?php echo $tpl['arr']['type'] == 'single' ? ' checked="checked"' : NULL;?> class="block float_left r5"/>
						<label for="type_single" class="block float_left"><?php __('extra_single');?></label>
					</span>
					<span class="block float_left">
						<input type="radio" name="type" id="type_multi" value="multi"<?php echo $tpl['arr']['type'] == 'multi' ? ' checked="checked"' : NULL;?> class="block float_left r5"/>
						<label for="type_multi" class="block float_left"><?php __('extra_multi');?></label>
					</span>
				</span>
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminExtras&action=pjActionIndex';" />
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