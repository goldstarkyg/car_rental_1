<?php
if (pjObject::getPlugin('pjOneAdmin') !== NULL && $controller->isAdmin())
{
	$controller->requestAction(array('controller' => 'pjOneAdmin', 'action' => 'pjActionMenu'));
}
?>

<div class="leftmenu-top"></div>
<div class="leftmenu-middle">
	<ul class="menu">
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionIndex' ? 'menu-focus' : NULL; ?>"><span class="menu-dashboard">&nbsp;</span><?php __('menuDashboard'); ?></a></li>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminBookings' ? 'menu-focus' : NULL; ?>"><span class="menu-reservations">&nbsp;</span><?php __('menuReservations'); ?></a></li>
		<?php
		if ($controller->isAdmin())
		{
			?>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminTypes&amp;action=pjActionIndex" class="<?php echo in_array($_GET['controller'], array('pjAdminTypes','pjAdminExtras')) || ($_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionPrices') ? 'menu-focus' : NULL; ?>"><span class="menu-rates">&nbsp;</span><?php __('menuRates'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminCars' && in_array($_GET['action'], array('pjActionIndex'))) || in_array($_GET['controller'], array('pjAdminCars')) ? 'menu-focus' : NULL; ?>"><span class="menu-cars">&nbsp;</span><?php __('menuCars'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminLocations&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminLocations' && in_array($_GET['action'], array('pjActionIndex'))) || in_array($_GET['controller'], array('pjAdminLocations', 'pjAdminTime')) ? 'menu-focus' : NULL; ?>"><span class="menu-locations">&nbsp;</span><?php __('menuLocations'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminUsers&amp;action=pjActionIndex" class="<?php echo $_GET['controller'] == 'pjAdminUsers' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuUsers'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionIndex" class="<?php echo ($_GET['controller'] == 'pjAdminOptions' && in_array($_GET['action'], array('pjActionIndex','pjActionRentalSettings','pjActionConfirmation','pjActionBooking','pjActionBookingForm'))) || in_array($_GET['controller'], array('pjAdminLocales', 'pjBackup', 'pjLocale', 'pjCountry', 'pjSms')  ) ? 'menu-focus' : NULL; ?>"><span class="menu-options">&nbsp;</span><?php __('menuSettings'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionPreview' ? 'menu-focus' : NULL; ?>"><span class="menu-preview">&nbsp;</span><?php __('menuPreview'); ?></a></li>
			<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionInstall" class="<?php echo $_GET['controller'] == 'pjAdminOptions' && $_GET['action'] == 'pjActionInstall' ? 'menu-focus' : NULL; ?>"><span class="menu-install">&nbsp;</span><?php __('menuInstall'); ?></a></li>
			<?php
		}
		if ($controller->isEditor())
		{
			?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionProfile" class="<?php echo $_GET['controller'] == 'pjAdmin' && $_GET['action'] == 'pjActionProfile' ? 'menu-focus' : NULL; ?>"><span class="menu-users">&nbsp;</span><?php __('menuProfile'); ?></a></li><?php
		}
		?>
		<li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdmin&amp;action=pjActionLogout"><span class="menu-logout">&nbsp;</span><?php __('menuLogout'); ?></a></li>
	</ul>
</div>
<div class="leftmenu-bottom"></div>