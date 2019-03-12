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
	<div class="dashboard_header">
		<div class="item">
			<div class="stat reservations">
				<div class="info">
					<abbr><?php echo $tpl['cnt_new_reservations_today'];?></abbr>
					<?php echo (int) $tpl['cnt_new_reservations_today'] !== 1 ? strtolower(__('lblDashNewTodayPlural', true)) : strtolower(__('lblDashNewTodaySingular', true)); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat returns">
				<div class="info smaller">
					<abbr><?php echo $tpl['cnt_today_pickup'];?></abbr>
					<?php echo (int) $tpl['cnt_today_pickup'] !== 1 ? strtolower(__('lblDashPickupsToday', true)) : strtolower(__('lblDashPickupToday', true)); ?>
				</div>
				<div class="info smaller">
					<abbr><?php echo $tpl['cnt_today_return'];?></abbr>
					<?php echo (int) $tpl['cnt_today_return'] !== 1 ? strtolower(__('lblDashReturnsToday', true)) : strtolower(__('lblDashReturnToday', true)); ?>
				</div>
			</div>
		</div>
		<div class="item">
			<div class="stat cars">
				<div class="info">
					<abbr><?php echo $tpl['cnt_avail_today'];?></abbr>
					<?php echo (int) $tpl['cnt_avail_today'] !== 1 ? strtolower(__('lblDashAvailCarsToday', true)) : strtolower(__('lblDashAvailCarToday', true)); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="dashboard_box">
		<div class="dashboard_top">
			<div class="dashboard_column_top"><?php __('lblDashLatestBookings');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex"><?php __('lblDashViewAll'); ?></a>)</div>
			<div class="dashboard_column_top"><?php __('lblDashTodayPickups');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;filter=p_today"><?php __('lblDashViewAll'); ?></a>)</div>
			<div class="dashboard_column_top"><?php __('lblDashQuickLinks');?></div>
		</div>
		
		<div class="dashboard_middle">
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_latest_list">
					<?php
					if(count($tpl['latest_bookings']) > 0)
					{
						foreach($tpl['latest_bookings'] as $v)
						{
							$rental_time = '';
							$rental_days = $v['rental_days'];
							$rental_hours = $v['rental_hours'];
							if($rental_days > 0 || $rental_hours > 0){
								if($rental_days > 0){
									$rental_time .= $rental_days . ' ' . ($rental_days > 1 ? __('plural_day', true, false) : __('singular_day', true, false));
								}
								if($rental_hours > 0){
									$rental_time .= ' ' . $rental_hours . ' ' . ($rental_hours > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false));
								}
							}
							?>
							<div class="dashboard_row">
								<label><span><?php __('booking_id');?></span>: <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::clean($v['booking_id']);?></a> | <span><?php __('booking_total_price');?>:</span> <?php echo pjUtil::formatCurrencySign($v['total_price'], $tpl['option_arr']['o_currency']);?></label>
								<label><span><?php __('lblDashCustomer');?>:</span> <?php echo pjSanitize::clean($v['c_name']);?></label>
								<label><span><?php __('lblDashPickup'); ?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['from'])); ?></label>
								<label><span><?php __('lblDashReturn'); ?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['to'])); ?></label>
								<label><span><?php __('lblRentalDuration');?>:</span> <?php echo $rental_time;?></label>
								<label><span><?php __('lblDashCarAssigned');?>:</span> <?php echo pjSanitize::clean($v['car_type']);?>, <?php echo pjSanitize::clean($v['car_name']);?>,
									<?php
									if($controller->isAdmin())
									{
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionUpdate&amp;id=<?php echo $v['car_id']?>"><?php echo pjSanitize::clean($v['registration_number']);?></a>
										<?php
									}else{
										echo pjSanitize::clean($v['registration_number']);
									}
									?>
								</label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('lblDashNoBooking');?></span></label>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="dashboard_list dashboard_pickup_list">
					<?php
					if(count($tpl['today_pickups']) > 0)
					{
						foreach($tpl['today_pickups'] as $v)
						{
							$rental_time = '';
							$rental_days = $v['rental_days'];
							$rental_hours = $v['rental_hours'];
							if($rental_days > 0 || $rental_hours > 0){
								if($rental_days > 0){
									$rental_time .= $rental_days . ' ' . ($rental_days > 1 ? __('plural_day', true, false) : __('singular_day', true, false));
								}
								if($rental_hours > 0){
									$rental_time .= ' ' . $rental_hours . ' ' . ($rental_hours > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false));
								}
							}
							?>
							<div class="dashboard_row">
								<label><span><?php __('booking_id');?></span>: <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::clean($v['booking_id']);?></a> | <span><?php __('booking_total_price');?>:</span> <?php echo pjUtil::formatCurrencySign($v['total_price'], $tpl['option_arr']['o_currency']);?></label>
								<label><span><?php __('lblDashCustomer');?>:</span> <?php echo pjSanitize::clean($v['c_name']);?></label>
								<label><span class="red"><?php __('lblDashPickup'); ?>:</span> <span class="red"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['from'])); ?></span></label>
								<label><span><?php __('lblDashReturn'); ?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['to'])); ?></label>
								<label><span><?php __('lblRentalDuration');?>:</span> <?php echo $rental_time;?></label>
								<label><span><?php __('lblDashCarAssigned');?>:</span> <?php echo pjSanitize::clean($v['car_type']);?>, <?php echo pjSanitize::clean($v['car_name']);?>,
									<?php
									if($controller->isAdmin())
									{
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionUpdate&amp;id=<?php echo $v['car_id']?>"><?php echo pjSanitize::clean($v['registration_number']);?></a>
										<?php
									}else{
										echo pjSanitize::clean($v['registration_number']);
									}
									?>
								</label>
								<label></label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('lblDashNoPickupToday');?></span></label>
						</div>
						<?php
					}
					?>
				</div>
				<div class="dashboard_subtop"><label><?php __('lblDashTodayReturns');?> (<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;filter=r_today"><?php __('lblDashViewAll'); ?></a>)</label></div>
				<div class="dashboard_list dashboard_return_list">
					<?php
					if(count($tpl['today_returns']) > 0)
					{
						foreach($tpl['today_returns'] as $v)
						{
							$rental_time = '';
							$rental_days = $v['rental_days'];
							$rental_hours = $v['rental_hours'];
							if($rental_days > 0 || $rental_hours > 0){
								if($rental_days > 0){
									$rental_time .= $rental_days . ' ' . ($rental_days > 1 ? __('plural_day', true, false) : __('singular_day', true, false));
								}
								if($rental_hours > 0){
									$rental_time .= ' ' . $rental_hours . ' ' . ($rental_hours > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false));
								}
							}
							?>
							<div class="dashboard_row">
								<label><span><?php __('booking_id');?></span>: <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionUpdate&amp;id=<?php echo $v['id']?>"><?php echo pjSanitize::clean($v['booking_id']);?></a> | <span><?php __('booking_total_price');?>:</span> <?php echo pjUtil::formatCurrencySign($v['total_price'], $tpl['option_arr']['o_currency']);?></label>
								<label><span><?php __('lblDashCustomer');?>:</span> <?php echo pjSanitize::clean($v['c_name']);?></label>
								<label><span><?php __('lblDashPickup'); ?>:</span> <?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['from'])); ?></label>
								<label><span class="red"><?php __('lblDashReturn'); ?>:</span> <span class="red"><?php echo date($tpl['option_arr']['o_date_format'], strtotime($v['to'])); ?></span></label>
								<label><span><?php __('lblRentalDuration');?>:</span> <?php echo $rental_time;?></label>
								<label><span><?php __('lblDashCarAssigned');?>:</span> <?php echo pjSanitize::clean($v['car_type']);?>, <?php echo pjSanitize::clean($v['car_name']);?>,
									<?php
									if($controller->isAdmin())
									{
										?>
										<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionUpdate&amp;id=<?php echo $v['car_id']?>"><?php echo pjSanitize::clean($v['registration_number']);?></a>
										<?php
									}else{
										echo pjSanitize::clean($v['registration_number']);
									}
									?>
								</label>
							</div>
							<?php
						}
					}else{
						?>
						<div class="dashboard_row">
							<label><span><?php __('lblDashNoReturnToday');?></span></label>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="dashboard_column">
				<div class="quick_links">
					<?php
					if($controller->isAdmin())
					{
						?>
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionAvailability"><?php __('lblDashCarAvailability'); ?></a>
						<?php
					}
					?>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;filter=p_tomorrow"><?php __('lblDashTomorrowPickups'); ?></a>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminBookings&amp;action=pjActionIndex&amp;filter=r_tomorrow"><?php __('lblDashTomorrowReturns'); ?></a>
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminOptions&amp;action=pjActionPreview" target="_blank"><?php __('lblDashFrontEndPreview'); ?></a>
				</div>
			</div>
		</div>
		<div class="dashboard_bottom"></div>
	</div>
	
	<div class="clear_left t20 overflow">
		<div class="float_left black pt15"><span class="gray"><?php echo ucfirst(__('lblDashLastLogin', true)); ?>:</span>  <?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($_SESSION[$controller->defaultUser]['last_login'])); ?></div>
		<div class="float_right overflow">
		<?php
		$days = __('days', true, false);
		?>
			<div class="dashboard_date">
				<abbr><?php echo $days[date('w')]; ?></abbr>
				<?php echo pjUtil::formatDate(date("Y-m-d"), "Y-m-d", $tpl['option_arr']['o_date_format']); ?>
			</div>
			<div class="dashboard_hour"><?php echo date('H:i');?></div>
		</div>
	</div>
	<?php
}
?>