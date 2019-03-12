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
	$titles = __('error_titles', true);
	$bodies = __('error_bodies', true);
	if (isset($_GET['err']))
	{
		pjUtil::printNotice(@$titles[$_GET['err']], @$bodies[$_GET['err']]);
	}
	?>
	<?php pjUtil::printNotice(__('infoThemeTitle', true), __('infoThemeDesc', true), false, false); ?>

	<div class="theme-holder pj-loader-outer">
		<?php include PJ_VIEWS_PATH . 'pjAdminOptions/elements/theme.php'; ?>
	</div>
	<div class="clear_both"></div>
	<?php
}
?>