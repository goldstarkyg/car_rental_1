<div class="container-fluid pjCrContainer">
	<div class="panel panel-default">
		<?php include_once dirname(__FILE__) . '/elements/header.php';?>
		
		<div class="panel-body pjCrBody">
			<?php
			$rental_hours = $_SESSION[$controller->default_product][$controller->default_order]['rental_hours'];
			$hours = $rental_hours %24;
			$days = 0;
			if($hours == 0 ){
				$days = $rental_hours / 24;
			}
			else {
				$days = floor($rental_hours / 24);
			}
			
			$isMinHourRestricted = 0;
			if ((int) $tpl['option_arr']['o_min_hour'] > 0)
			{
				switch ($tpl['option_arr']['o_booking_periods'])
				{
					case 'perday':
						if ($days < (int) $tpl['option_arr']['o_min_hour'])
						{
							$isMinHourRestricted = 1;
						}
						break;
					default:
						if ($rental_hours < (int) $tpl['option_arr']['o_min_hour'])
						{
							$isMinHourRestricted = 2;
						}
						break;
				}
			}
			if ($isMinHourRestricted !== 0)
			{
				switch ($isMinHourRestricted)
				{
					case 1:
						?>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="crNotice alert alert-warning"><?php echo str_replace('{X}', $tpl['option_arr']['o_min_hour'], __('front_2_min_days', true)); ?></div>
								<input type="button" class="btn btn-default text-uppercase pjCrBtntDefault" id="crBtnWhen" value="<?php __('btnBack'); ?>" />
							</div>
						</div>
						<?php
						break;
					case 2:
						?>
						<div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
								<div class="crNotice alert alert-warning"><?php echo str_replace('{X}', $tpl['option_arr']['o_min_hour'], __('front_2_min_hours', true)); ?></div>
								<input type="button" class="btn btn-default text-uppercase pjCrBtntDefault" id="crBtnWhen" value="<?php __('btnBack'); ?>" />
							</div>
						</div>
						<?php
						break;
				}
				
			}else{
				?>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="form-inline">
							<div class="form-group">
								<label for=""><?php __('front_2_filter_by'); ?>:</label>
								<select name="crTabsSelect" id="crTabsSelect" class="form-control crTabsSelect">
									<option<?php echo !isset($_GET['type_id']) || (isset($_GET['type_id']) && $_GET['type_id'] == 'all') ? ' selected="selected"' : NULL; ?> rel="all"><?php __('front_2_all'); ?></option>
									<?php
									foreach ($tpl['type_arr'] as $k => $v)
									{
										?><option<?php echo isset($_GET['type_id']) && $_GET['type_id'] == $v['id'] ? ' selected="selected"' : NULL; ?> rel="<?php echo $v['id']; ?>"><?php echo pjSanitize::clean($v['name']); ?></option><?php
									}
									?>
								</select>
							</div><!-- /.form-group -->
						</div><!-- /.form-inline -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
	
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
						<div class="form-inline pull-right">
							<div class="form-group">
								<label for=""><?php __('front_2_sort_by'); ?>: </label>
								<select name="sort" id="crSort" class="form-control crSort">
									<option value="price" rel="total_price|asc" <?php echo isset($_GET['col_name']) &&  $_GET['col_name'] == 'total_price' && isset($_GET['direction']) &&  $_GET['direction'] == 'asc' ? 'selected="selected"' : '' ?>><?php echo __('front_2_sort_price_asc') ?>
									<option value="price" rel="total_price|desc" <?php echo isset($_GET['col_name']) &&  $_GET['col_name'] == 'total_price' && isset($_GET['direction']) &&  $_GET['direction'] == 'desc' ? 'selected="selected"' : '' ?>><?php echo __('front_2_sort_price_desc') ?>
									<option value="luggage" rel="luggages|asc" <?php echo isset($_GET['col_name']) &&  $_GET['col_name'] == 'luggages' && isset($_GET['direction']) &&  $_GET['direction'] == 'asc' ? 'selected="selected"' : '' ?>><?php echo __('front_2_sort_luggage_asc') ?>
									<option value="luggage" rel="luggages|desc" <?php echo isset($_GET['col_name']) &&  $_GET['col_name'] == 'luggages' && isset($_GET['direction']) &&  $_GET['direction'] == 'desc' ? 'selected="selected"' : '' ?>><?php echo __('front_2_sort_luggage_desc') ?>
								</select>
							</div><!-- /.form-group -->
						</div><!-- /.form-inline -->
					</div><!-- /.col-lg-6 col-md-6 col-sm-6 col-xs-6 -->
				</div><!-- /.row -->
				
				<br />
				
				<button class="btn btn-default text-capitalize pjCrBtnPannelTrigger visible-xs-block"><?php __('front_3_booking'); ?></button>
				<br class="visible-xs-inline" />
				
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 hidden-xs pjCrPanelLeft">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h1 class="panel-title"><?php __('front_3_booking'); ?></h1><!-- /.panel-title -->
							</div><!-- /.panel-heading -->
	
							<div class="panel-body">
								<p class="clearfix">
									<strong><?php __('front_3_when'); ?></strong>
									<a href="<?php echo $_SERVER['PHP_SELF']; ?>" id="crBtnWhen" class="pull-right text-capitalize pjCrColorPrimary"><?php __('front_3_change'); ?></a>
								</p>
	
								<div class="row">
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><span class="text-muted"><?php __('front_3_pickup'); ?>:</span></div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
	
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><?php echo stripslashes($tpl['pickup_location']['name']); ?><br/><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($_SESSION[$controller->default_product][$controller->default_order]['date_from'] . " " . $_SESSION[$controller->default_product][$controller->default_order]['hour_from'] . ":" . $_SESSION[$controller->default_product][$controller->default_order]['minutes_from'] . ":00")); ?></div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 -->
								</div><!-- /.row -->
	
								<div class="row">
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><span class="text-muted"><?php __('front_3_return'); ?>:</span></div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
	
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><?php echo isset($tpl['return_location']['name']) ? stripslashes($tpl['return_location']['name']) : stripslashes($tpl['pickup_location']['name']); ?><br/><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($_SESSION[$controller->default_product][$controller->default_order]['date_to'] . " " . $_SESSION[$controller->default_product][$controller->default_order]['hour_to'] . ":" . $_SESSION[$controller->default_product][$controller->default_order]['minutes_to'] . ":00")); ?></div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 -->
								</div><!-- /.row -->
	
								<div class="row">
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><span class="text-muted"><?php __('front_3_rental_duration'); ?>:</span></div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
	
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><?php echo $days > 0 ? ($days == 1 ? $days ." ". __('front_label_day',true) : $days ." ". __('front_3_days',true) ) : ''?><?php echo $days > 0 && $hours > 0 ? ' ' :'';?><?php echo $hours > 0 ?  $hours." ".__('front_3_hours',true) : ''?></div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 -->
								</div><!-- /.row -->
							</div><!-- /.panel-body -->
						</div><!-- /.panel panel-default -->
					</div><!-- /.col-lg-4 col-md-4 col-sm-12 col-xs-12 hidden-xs pjCrPanelLeft -->
					
					<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 pjCrPanelRight">
						<?php
						if (count($tpl['arr']) > 0)
						{ 
							$type_transmissions = __('type_transmissions', true);
							?>
							<ul class="list-group">
								<?php
								foreach ($tpl['arr'] as $type)
								{
									?>
									<li class="list-group-item pjCrProduct">
										<div class="row">
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
												<img src="<?php echo is_file(PJ_INSTALL_PATH . $type['thumb_path']) ? PJ_INSTALL_URL . $type['thumb_path'] : PJ_INSTALL_URL . PJ_IMG_PATH . "frontend/dummy_2.png"; ?>" alt="" class="img-responsive" />
											</div><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-12 -->
																
											<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
												<br />
												<p class="lead"><?php echo stripslashes($type['name']); ?></p>
																
												<dl>
													<dt><?php __('front_2_example'); ?>:</dt>
													<dd><?php echo stripslashes(@$type['example']['make'] . " " . @$type['example']['model']); ?></dd>
												</dl>
																
												<ul class="list-inline">
													<li class="bg-primary pjCrBgPrimary" title="<?php __('front_2_passengers'); ?>">
														<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
																
														<?php echo $type['passengers']; ?>
													</li>
																
													<li class="bg-primary pjCrBgPrimary" title="<?php __('front_2_luggage'); ?>">
														<span class="glyphicon glyphicon-briefcase" aria-hidden="true"></span>
																
														<?php echo $type['luggages']; ?>
													</li>
																
													<li class="bg-primary pjCrBgPrimary" title="<?php __('front_2_doors'); ?>">
														<span class="glyphicon glyphicon-modal-window" aria-hidden="true"></span>
																
														<?php echo $type['doors']; ?>
													</li>
																
													<li class="bg-primary pjCrBgPrimary" title="<?php __('front_2_transmission'); ?>">
														<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
																
														<?php echo $type_transmissions[$type['transmission']]{0}; ?>
													</li>
												</ul><!-- /.list-inline -->
																
												<p><?php echo stripslashes(nl2br($type['description'])); ?></p>
											</div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
																
											<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
												<br />
												
												<?php
												if ($type['cnt_available'] > 0 && $type['total_price'] > 0)
												{ 
													?>
													<div class="well well-sm text-center">
														<p><?php __('front_2_best'); ?>:</p>
														<p><strong><?php echo pjUtil::formatCurrencySign(number_format(floatval($type['total_price']), 2), $tpl['option_arr']['o_currency'], " "); ?></strong></p>
													</div><!-- /.well well-sm text-center -->
													<button type="button" value="<?php echo $type['id']; ?>" class="btn btn-default btn-block text-uppercase pjCrBtntDefault crBtnContinue"><?php __('front_btn_continue'); ?></button>
													<?php
												} else {
													?>
													<div class="well well-sm text-center">
														<p><?php echo __('front_2_not', true, false);?></p>
													</div><!-- /.well well-sm text-center -->
													<a href="#" class="pjCrBtnLink crChangeDates"><?php __('front_change_dates', false, true)?></a>
													<?php
												}
												?>
													
											</div><!-- /.col-lg-3 col-md-3 col-sm-3 col-xs-12 -->
										</div><!-- /.row -->
									</li><!-- /.list-group-item pjCrProduct -->
									<?php
								} 
								?>
							</ul>
							<?php
						}else{ 
							__('front_2_empty');
						} 
						?>
					</div><!-- /.col-lg-8 col-md-8 col-sm-12 col-xs-12 pjCrPanelRight -->
				</div><!-- /.row -->
				<?php
			} 
			?>
		</div><!-- /.panel-body pjCrBody -->
		
	</div><!-- /.panel panel-default -->
</div><!-- /.container-fluid pjCrContainer -->