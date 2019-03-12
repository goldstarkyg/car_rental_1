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
	$install_view = __('install_view', true);
	$yesno_arr = __('_yesno', true);
	$layouts = __('layouts', true);
	?>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php __('lblInstall'); ?></a></li>
		</ul>
		<div id="tabs-1" class="pj-form form">
		
			<?php pjUtil::printNotice(NULL, __('lblInstallText', true), false, false); ?>
			<p>
				<label class="float_left w200 pt5"><?php __('lblIntegrationMethod');?></label>
				<select id="integration_method" name="integration_method" class="pj-form-field">
					<?php
					$integration_methods = __('integration_methods', true); 
					foreach($integration_methods as $k => $v)
					{
						?><option value="<?php echo $k; ?>"><?php echo $v;?></option><?php
					}
					?>
				</select>
				<select id="install_language" name="install_language" class="pj-form-field">
					<?php
					foreach($tpl['install_locale_arr'] as $v)
					{
						?>
						<option value="<?php echo $v['id']?>" <?php echo $v['is_default'] == 1 ? 'selected="selected"' : null;?>><?php echo $v['title']?></option>
						<?php
					} 
					?>
				</select>
			</p>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstall_step_1'); ?></p>
		<textarea class="textarea textarea-install w700 h100 overflow">
&lt;meta http-equiv="X-UA-Compatible" content="IE=edge" /&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL.PJ_FRAMEWORK_LIBS_PATH . 'pj/css/'; ?>pj.bootstrap.min.css" type="text/css" rel="stylesheet" /&gt;
&lt;link href="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoadCss" type="text/css" rel="stylesheet" /&gt;
</textarea></p>
			<p style="margin: 0 0 10px; font-weight: bold"><?php __('lblInstall_step_2'); ?></p>
			<textarea id="cr_install_text" class="textarea textarea-install w700 h80 overflow"></textarea>
			<input type="hidden" id="install_clone_text" value='&lt;script type="text/javascript" src="<?php echo PJ_INSTALL_URL; ?>index.php?controller=pjFront&action=pjActionLoad{LANG}"&gt;&lt;/script&gt;' />
		</div><!-- tabs-1 -->
	</div>
	<?php
}
?>