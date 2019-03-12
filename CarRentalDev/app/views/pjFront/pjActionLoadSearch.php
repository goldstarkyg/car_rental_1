<?php
$add_days = 2;
if($tpl['option_arr']['o_booking_periods'] == 'perday')
{
	$add_days = (int) $tpl['option_arr']['o_min_hour'] + 1;
}
$months = __('months', true);
$short_months = __('short_months', true);
ksort($months);
ksort($short_months);
$days = __('days', true);
$short_days = __('short_days', true);
$week_start = isset($tpl['option_arr']['o_week_start']) && in_array((int) $tpl['option_arr']['o_week_start'], range(0,6)) ? (int) $tpl['option_arr']['o_week_start'] : 0;
?>
<div class="container-fluid pjCrContainer">
	<div class="panel panel-default">
		<?php include_once dirname(__FILE__) . '/elements/header.php';?>
		
		<div class="panel-body pjCrBody">
			<form action="#" id="crFormSearch" method="get">
				<div id="pjCrCalendarLocale" style="display: none;" data-months="<?php echo implode("_", $months);?>" data-days="<?php echo implode("_", $short_days);?>" data-fday="<?php echo $week_start;?>"></div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340">
						<div class="form-group pjCrFormGroup">
							<div class="row">
								<label for="" class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12"><?php __('front_1_start'); ?></label>
								<?php
								$min_from = date("Y-m-d"); ;
								$min_to = isset($_SESSION[$controller->default_product][$controller->default_order]['date_from']) && !empty($_SESSION[$controller->default_product][$controller->default_order]['date_from']) ? $_SESSION[$controller->default_product][$controller->default_order]['date_from'] : date("Y-m-d");
								?>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div id="pjCrDatePickerFrom" class="input-group pjCrDatePicker pjCrDatePickerFrom" data-min="<?php echo $min_from;?>">
										<span class="input-group-addon pjCrCursorPointer">
											<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
										</span>
	
										<input type="text" id="cr_date_from" name="date_from" class="form-control" value="<?php echo isset($_SESSION[$controller->default_product][$controller->default_order]['date_from']) ? htmlspecialchars(pjUtil::formatDate($_SESSION[$controller->default_product][$controller->default_order]['date_from'], 'Y-m-d', $tpl['option_arr']['o_date_format'])) : date($tpl['option_arr']['o_date_format'], strtotime("+1 day")); ?>" readonly="readonly"/>
									</div><!-- /.input-group pjCrDatePicker pjCrDatePickerFrom -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
								<?php
								$hf = isset($_SESSION[$controller->default_product][$controller->default_order]['hour_from']) ? $_SESSION[$controller->default_product][$controller->default_order]['hour_from'] : '09';
								$mf = isset($_SESSION[$controller->default_product][$controller->default_order]['minutes_from']) ? $_SESSION[$controller->default_product][$controller->default_order]['minutes_from'] : '00';
								?>
								
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group pjCrTimePicker pjCrTimePickerFrom" data-rel="from">
										<span class="input-group-addon pjCrCursorPointer">
											<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
										</span>
										<input type="text" class="form-control" value="<?php printf("%s:%s", $hf, $mf); ?>" readonly />
									</div>
								</div>
								<input type="hidden" id="cr_hour_from" name="hour_from" value="<?php echo pjSanitize::html($hf); ?>" />
								<input type="hidden" id="cr_minutes_from" name="minutes_from" value="<?php echo pjSanitize::html($mf); ?>" />
							</div><!-- /.row -->
						</div><!-- /.form-group pjCrFormGroup -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340 -->
	
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340">
						<div class="form-group pjCrFormGroup">
							<div class="row">
								<label for="" class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12"><?php __('front_1_end'); ?></label>
								
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div id="pjCrDatePickerTo" class="input-group pjCrDatePicker pjCrDatePickerTo" data-min="<?php echo $min_to;?>">
										<span class="input-group-addon pjCrCursorPointer">
											<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
										</span>
	
										<input type="text" id="cr_date_to" name="date_to" class="form-control" value="<?php echo isset($_SESSION[$controller->default_product][$controller->default_order]['date_to']) ? htmlspecialchars(pjUtil::formatDate($_SESSION[$controller->default_product][$controller->default_order]['date_to'], 'Y-m-d', $tpl['option_arr']['o_date_format'])) : date($tpl['option_arr']['o_date_format'], strtotime("+".$add_days." day")); ?>" readonly="readonly"/>
									</div><!-- /.input-group pjCrDatePicker pjCrDatePickerTo -->
								</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
								<?php
								$ht = isset($_SESSION[$controller->default_product][$controller->default_order]['hour_to']) ? $_SESSION[$controller->default_product][$controller->default_order]['hour_to'] : '09';
								$mt = isset($_SESSION[$controller->default_product][$controller->default_order]['minutes_to']) ? $_SESSION[$controller->default_product][$controller->default_order]['minutes_to'] : '00';
								?>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
									<div class="input-group pjCrTimePicker pjCrTimePickerTo" data-rel="to">
										<span class="input-group-addon pjCrCursorPointer">
											<span class="glyphicon glyphicon-time" aria-hidden="true"></span>
										</span>
				
										<input type="text" class="form-control" value="<?php printf("%s:%s", $ht, $mt); ?>" readonly />
									</div><!-- /.input-group timePick timePickFrom -->
								</div>
								<input type="hidden" id="cr_hour_to" name="hour_to" value="<?php echo pjSanitize::html($ht); ?>" />
								<input type="hidden" id="cr_minutes_to" name="minutes_to" value="<?php echo pjSanitize::html($mt); ?>" />
							</div><!-- /.row -->
						</div><!-- /.form-group pjCrFormGroup -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340 -->
	
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340">
						<div class="form-group">
							<div class="row">
								<label for="" class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12"><?php __('front_1_pickup'); ?></label>
								
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<select name="pickup_id" id="cr_pickup_id" class="form-control">
									<?php
									foreach ($tpl['location_arr'] as $location)
									{
										?><option value="<?php echo $location['id']; ?>"<?php echo isset($_SESSION[$controller->default_product][$controller->default_order]['pickup_id']) && $_SESSION[$controller->default_product][$controller->default_order]['pickup_id'] == $location['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($location['name']); ?></option><?php
									}
									?>
									</select>
								</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
								
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<br />
	
									<p>
										<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>
										<?php __('front_1_cant_find'); ?>
										
										<a href="#" class="text-capitalize pjCrColorPrimary" data-toggle="modal" id="crBtnMap"><?php __('front_1_view_map'); ?></a>
									</p>
	
								</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
							</div><!-- /.row -->
						</div><!-- /.form-group -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340 -->
					<?php
					$isChecked = (isset($_SESSION[$controller->default_product][$controller->default_order]['pickup_id']) && isset($_SESSION[$controller->default_product][$controller->default_order]['same_location'])) || !isset($_SESSION[$controller->default_product][$controller->default_order]['pickup_id']);
					?>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340">
						<div class="form-group">
							<div class="row">
								<label for="" class="control-label col-lg-12 col-md-12 col-sm-12 col-xs-12"><?php  __('front_1_return'); ?></label>
								
								<div id="crReturnBox" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<select name="return_id" id="cr_return_id" class="form-control"<?php echo $isChecked ? 'disabled="disabled"' : NULL; ?>>
									<?php
									foreach ($tpl['location_arr'] as $location)
									{
										?><option value="<?php echo $location['id']; ?>"<?php echo isset($_SESSION[$controller->default_product][$controller->default_order]['return_id']) && $_SESSION[$controller->default_product][$controller->default_order]['return_id'] == $location['id'] ? ' selected="selected"' : NULL; ?>><?php echo stripslashes($location['name']); ?></option><?php
									}
									?>
									</select>
								</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
	
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<div class="checkbox">
										<label for="cr_same_location">
											<input  type="checkbox" name="same_location" id="cr_same_location" value="1"<?php echo $isChecked ? ' checked="checked"' : NULL; ?> /> <?php __('front_1_same'); ?>
										</label>
									</div><!-- /.checkbox -->
								</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
							</div><!-- /.row -->
						</div><!-- /.form-group -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-12 pjCrMinSize340 -->
	
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
						<button type="button"  class="btn btn-default text-uppercase pjCrBtntDefault" id="crBtnQuote"><?php __('front_btn_quote'); ?></button>
					</div><!-- /.col-lg-12 col-md-12 col-sm-12 col-xs-12 -->
				</div><!-- /.row -->

				<div class="crError text-danger text-center" style="display: none;"></div>
			</form>
		</div><!-- /.panel-body pjCrBody -->
	</div><!-- /.panel panel-default -->
</div><!-- /.container-fluid pjCrContainer -->