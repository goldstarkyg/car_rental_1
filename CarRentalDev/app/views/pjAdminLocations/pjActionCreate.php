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
	<?php pjUtil::printNotice(__('infoAddLocationTitle', true), __('infoAddLocationBody', true)); ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionCreate" method="post" id="frmCreate" class="pj-form form">
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
					<label class="title"><?php __('location_name'); ?></label>
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
			
			<p>
				<label class="title"><?php __('location_email'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-email"></abbr></span>
					<input type="text" name="email" id="email" class="pj-form-field email w200" />
				</span>
			</p>
			<p>
				<label class="title"><?php __('location_email_notify'); ?></label>
				<span class="inline_block t5">
					<input type="checkbox" name="notify_email" value="T" class="block float_left r10"/>
					<a href="#" class="pj-form-langbar-tip listing-tip checkbox-tip" title="<?php echo pjSanitize::clean(__('location_email_notify_tip', true)); ?>"></a>
				</span>
			</p>
			<p>
				<label class="title"><?php __('location_phone'); ?></label>
				<span class="pj-form-field-custom pj-form-field-custom-before">
					<span class="pj-form-field-before"><abbr class="pj-form-field-icon-phone"></abbr></span>
					<input type="text" name="phone" id="phone" class="pj-form-field w200" />
				</span>
			</p>
	
			
			<p>
				<label class="title"><?php __('location_country'); ?></label>
				<span class="inline_block">
					<select id="country_id" name="country_id" class="pj-form-field w300 required">
						<option value="">-- <?php __('lblChoose'); ?>--</option>
						<?php
						foreach ($tpl['country_arr'] as $k => $v)
						{
							?><option value="<?php echo $v['id']; ?>"><?php echo $v['country_title']; ?></option><?php
						}
						?>
					</select>
				</span>
			</p>
			
			<?php
			foreach ($tpl['lp_arr'] as $v)
			{
				?>
				<p class="pj-multilang-wrap" data-index="<?php echo $v['id']; ?>" style="display: <?php echo (int) $v['is_default'] === 0 ? 'none' : NULL; ?>">
					<label class="title"><?php __('location_state'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][state]" class="pj-form-field w200"  />
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
					<label class="title"><?php __('location_city'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][city]" class="pj-form-field w200"  />
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
					<label class="title"><?php __('location_address_1'); ?></label>
					<span class="inline_block">
						<input type="text" name="i18n[<?php echo $v['id']; ?>][address_1]" class="pj-form-field w200"  />
						<?php if ((int) $tpl['option_arr']['o_multi_lang'] === 1 && count($tpl['lp_arr']) > 1) : ?>
						<span class="pj-multilang-input"><img src="<?php echo PJ_INSTALL_URL . PJ_FRAMEWORK_LIBS_PATH . 'pj/img/flags/' . $v['file']; ?>" alt="" /></span>
						<?php endif;?>
					</span>
				</p>
				<?php
			}
			?>
			<p>
				<label class="title"><?php __('location_zip'); ?></label>
				<input type="text" name="zip" id="zip"  class="pj-form-field w100 " />
			</p>
			<p>
				<label class="title">&nbsp;</label>
				<span><?php __('lblGMapNote'); ?></span>
			</p>
			
			<div class="left-content">
				<p>
					<label class="title">&nbsp;</label>
					<span class="">
						<input type="button" value="<?php __('btnGoogleMapsApi'); ?>" class="pj-button btnGoogleMapsApi" />
						<span style="color: red; display: none"></span>
					</span>
				</p>	
				<p>
					<label class="title"><?php __('lblLatitude'); ?></label>
					<input type="text" name="lat" id="lat" value="" class="pj-form-field w200 number" />
				</p>
				<p>
					<label class="title"><?php __('lblLongitude'); ?></label>
					<input type="text" name="lng" id="lng" value="" class="pj-form-field w200 number" />
				</p>
				<p>
					<label class="title">&nbsp;</label>
					<input type="submit" value="<?php __('btnSave'); ?>" class="pj-button" />
					<input type="button" value="<?php __('btnCancel'); ?>" class="pj-button" onclick="window.location.href='<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjAdminLocations&action=pjActionIndex';" />
				</p>
			</div>
			<div id="map_canvas" class="map-canvas"></div>
			
		</div>
	</form>
	<script type="text/javascript">
	var myLabel = myLabel || {};
	myLabel.address_not_found = "<?php __('lblAddressNotFound'); ?>";
	
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