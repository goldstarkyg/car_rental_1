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
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionIndex"><?php __('lblAllCars'); ?></a></li>
			<li class="ui-state-default ui-corner-top"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionAvailability"><?php __('lblAvailability'); ?></a></li>
		</ul>
	</div>
	<?php pjUtil::printNotice(__('infoUpdateCarTitle', true), __('infoUpdateCarBody', true)); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionUpdate" method="post" id="frmUpdate" class="form pj-form" enctype="multipart/form-data">
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
					<label class="title"><?php __('car_make'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][make]" class="pj-form-field w200 <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"   value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['make'])); ?>" />
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
					<label class="title"><?php __('car_model'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][model]" class="pj-form-field w200 <?php echo (int) $v['is_default'] === 0 ? NULL : ' required'; ?>"    value="<?php echo htmlspecialchars(stripslashes(@$tpl['arr']['i18n'][$v['id']]['model'])); ?>"/>
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			
			<p><label class="title"><?php echo __('car_reg') ; ?></label><input type="text" name="registration_number" id="registration_number" class="pj-form-field w200  required" value="<?php echo $tpl['arr']['registration_number'] ?>"/></p>
			<p><label class="title"><?php echo __('car_current_mileage') ; ?></label><input type="text" name="mileage" id="mileage" class="pj-form-field w100  digits" value="<?php echo $tpl['arr']['mileage'] ?>"/>&nbsp;<?php echo $tpl['option_arr']['o_unit'] ?></p>
			<p>
				<label class="title"><?php __('car_location'); ?></label>
					<select name="location_id" id="location_id" class="pj-form-field required w200" >
					<option value=""><?php __('cr_choose'); ?></option>
					<?php
					foreach ($tpl['location_arr'] as $k => $v)
					{
						?><option value="<?php echo $v['id']; ?>" <?php echo $v['id'] == $tpl['arr']['location_id'] ? 'selected="selected"' : '' ?>><?php echo $v['name']; ?></option><?php
					}
					?>
					</select>
					<?php if(count($tpl['location_arr']) == 0 ) { ?>
						<?php __('car_empty_location') ?>&nbsp;<a href="<?php echo PJ_INSTALL_URL ?>index.php?controller=pjAdminLocations&action=pjActionCreate">here</a>
					<?php } ?>
			</p>
			
			<p><label class="title"><?php echo __('car_type'); ?></label>
				<span class="block" style="margin-left: 135px; margin-top: 3px">
				<?php 
				$i = 1;
				$is_open = false;
				foreach ($tpl['type_arr'] as $type)
				{
					$is_open = true;
					?><span class="float_left block w200"><input type="checkbox" name="type_id[]" id="type_<?php echo $type['id']; ?>" value="<?php echo $type['id']; ?>" <?php echo in_array($type['id'],$tpl['car_type_arr'])  ? 'checked="checked"' : '' ?> /> <label for="type_<?php echo $type['id']; ?>"><?php echo pjSanitize::clean($type['name']); ?></label></span><?php
					if ($i % 3 === 0)
					{
						$is_open = false;
						?><span class="clear_left block"></span><?php
					}
					$i++;
				}
				if ($is_open) {
					?><span class="clear_left block"></span><?php
				}
				?>
				</span>
				<?php if(count($tpl['type_arr']) == 0 ) { ?>
					<?php __('car_empty_type') ?>&nbsp;<a href="<?php echo PJ_INSTALL_URL ?>index.php?controller=pjAdminTypes&action=pjActionCreate">here</a>
				<?php } ?>
			</p>
			
			<p>
				<label class="title">&nbsp;</label>
				<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
				<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminCars&action=pjActionIndex';" />
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
	
	var myLabel = myLabel || {};
	myLabel.car_same_reg = "<?php __('car_same_reg'); ?>";
	</script>
	<?php
}
?>