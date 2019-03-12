<?php
$extra_per = __('extra_per',true);
?>
<div class="container-fluid pjCrContainer">
	<div class="panel panel-default">
		<?php include_once dirname(__FILE__) . '/elements/header.php';?>
		
		<div class="panel-body pjCrBody">
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
							<?php
							$rental_hours = $_SESSION[$controller->default_product][$controller->default_order]['rental_hours'];
							$hours = $rental_hours %24;
							if($tpl['option_arr']['o_booking_periods'] == 'perday') 
							{
								$days = $_SESSION[$controller->default_product][$controller->default_order]['rental_days'];
								?>
								<div class="row">
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><span class="text-muted"><?php __('front_3_rental'); ?>:</span></div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
	
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><?php echo $days == 1 ? $days ." ". __('front_label_day',true) : $days ." ". __('front_3_days',true); ?><?php echo $days > 0 && $hours > 0 ? ' ' :'';?><?php echo $hours > 0 ?  $hours." ".__('front_3_hours',true) : ''?></div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 -->
								</div><!-- /.row -->
								<?php
							}else{
								$days = 0;
								if($hours == 0 )
								{
									$days = $rental_hours / 24;
								}else {
									$days = floor($rental_hours / 24);
								}
								?>
								<div class="row">
									<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12"><span class="text-muted"><?php __('front_3_rental_duration'); ?>:</span></div><!-- /.col-lg-5 col-md-5 col-sm-5 col-xs-12 -->
	
									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12"><?php echo $rental_duration = $days > 0 ? ($days == 1 ? $days ." ". __('front_label_day',true) : $days ." ". __('front_3_days',true) ) : ''?><?php echo $days > 0 && $hours > 0 ? ' ' :'';?><?php echo $hours > 0 ?  $hours." ".__('front_3_hours',true) : ''?></div><!-- /.col-lg-7 col-md-7 col-sm-7 col-xs-12 -->
								</div><!-- /.row -->
								<?php
							} 
							?>

							<br />

							<p class="clearfix">
								<strong><?php __('front_3_choise'); ?></strong>

								<a href="<?php echo $_SERVER['PHP_SELF']; ?>" id="crBtnChoise" class="pull-right text-capitalize pjCrColorPrimary"><?php __('front_3_change'); ?></a>
							</p>

							<div class="row">
								<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
									<img src="<?php echo is_file(@$tpl['type_arr']['thumb_path']) ? PJ_INSTALL_URL . $tpl['type_arr']['thumb_path'] : PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/dummy_1.png'; ?>" alt="" class="img-responsive"/>
								</div><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-4 -->

								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
									<p><strong><?php echo stripslashes(@$tpl['type_arr']['name']); ?></strong></p>
									<p>(<?php __('front_3_example'); ?>: <?php echo stripslashes(@$tpl['type_arr']['example']['make'] . " " . @$tpl['type_arr']['example']['model']); ?>)</p>
								</div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-8 -->
								<?php
								if (isset($tpl['term_arr']) && count($tpl['term_arr']) === 1)
								{
									if(!empty($tpl['term_arr'][0]['content']))
									{
										?>
										<button type="button" id="crBtnConditions" class="btn btn-link pjCrBtnTerms" data-toggle="modal"><?php __('front_3_conditions'); ?></button>
										<?php
									} 
									?>
	
									<div id="pjCrTermsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-body">
													<div id="pjCrTermsConditions"><?php echo stripslashes(nl2br($tpl['term_arr'][0]['content']));?></div>
												</div><!-- /.modal-body -->
	
												<div class="modal-footer">
													<button type="button" class="btn btn-default pjCrBtntDefault" data-dismiss="modal"><?php __('front_1_close');?></button>
												</div><!-- /.modal-footer -->
											</div><!-- /.modal-content -->
										</div><!-- /.modal-dialog -->
									</div><!-- /.modal fade -->
									<?php 
								}
								?>
							</div><!-- /.row -->
						</div><!-- /.panel-body -->
					</div><!-- /.panel panel-default -->
				</div><!-- /.col-lg-4 col-md-4 col-sm-12 col-xs-12 pjCrPanelLeft -->

				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 pjCrPanelRight">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="panel-title"><?php __('front_3_extras'); ?></h1><!-- /.panel-title -->
						</div><!-- /.panel-heading -->

						<div class="panel-body">
							<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="crFormExtras">
								<ul class="list-group">
									<?php
									$extraCost = 0;
									foreach ($tpl['arr'] as $extra)
									{
										?>
										<li class="list-group-item pjCrProductExtra">
											<div class="row">
												<div class="col-lg-4 col-md-4 col-sm-3 col-xs-12">
													<h4><?php echo stripslashes($extra['name']); ?></h4>
												</div><!-- /.col-lg-4 col-md-4 col-sm-3 col-xs-12 -->
												<?php
												if($extra['extra_type'] == 'multi')
												{ 
													?>								
													<div class="col-lg-2 col-md-2 col-sm-2 col-xs-5">
														<span>x</span>
			
														<div class="pjCrMultiplierDropdown">
															<select  name="extra_quantity[<?php echo $extra['extra_id']; ?>]" id="extra_quantity_<?php echo $extra['extra_id']; ?>" class="form-control crSelectSmall" rel="<?php echo $extra['extra_id']; ?>" rev="<?php echo (isset($_SESSION[$controller->default_product][$controller->default_order]) && isset($_SESSION[$controller->default_product][$controller->default_order]['extras']) && array_key_exists($extra['extra_id'], $_SESSION[$controller->default_product][$controller->default_order]['extras'])) ? 1: 0 ?>">
																<?php for ($i=1; $i<= 10;$i++){ ?>
																	<?php
																		$selected = '';
																		if (isset($_SESSION[$controller->default_product]) && isset($_SESSION[$controller->default_product][$controller->default_order]) && isset($_SESSION[$controller->default_product][$controller->default_order]['extras']) && array_key_exists($extra['extra_id'], $_SESSION[$controller->default_product][$controller->default_order]['extras'])  )
																		{
																			if(isset($_SESSION[$controller->default_product][$controller->default_order]['extras'][$extra['extra_id']]['extra_quantity']) && $_SESSION[$controller->default_product][$controller->default_order]['extras'][$extra['extra_id']]['extra_quantity'] == $i){
																				$selected = 'selected="selected"';
																			}
																			
																		}
																	?>
																	<option value="<?php echo $i ?>" <?php echo $selected ?> ><?php echo $i ;?></option>
																<?php } ?>
															</select>
																
														</div><!-- /.pjCrMultiplierDropdown -->
													</div><!-- /.col-lg-2 col-md-2 col-sm-2 col-xs-5 -->
													<?php
												}else{
													?><div class="col-lg-2 col-md-2 col-sm-2 col-xs-5">&nbsp;</div><?php
												} 
												?>								
												<div class="col-lg-3 col-md-4 col-sm-5 col-xs-7">
													<h5 class="text-right">
														<strong><?php echo pjUtil::formatCurrencySign(number_format(floatval($extra['price']), 2), $tpl['option_arr']['o_currency'], " "); ?></strong>
													</h5>
													<span class="pull-right"><?php echo strtolower(@$extra_per[$extra['per']]); ?></span>
												</div><!-- /.col-lg-4 col-md-4 col-sm-5 col-xs-7 -->
																				
												<div class="col-lg-3 col-md-2 col-sm-2 col-xs-12">
													<?php
													if (isset($_SESSION[$controller->default_product][$controller->default_order]) && isset($_SESSION[$controller->default_product][$controller->default_order]['extras']) && array_key_exists($extra['extra_id'], $_SESSION[$controller->default_product][$controller->default_order]['extras']))
													{ 
														?>
														<button class="btn btn-default text-capitalize pull-right pjCrBtntDefault pjCrBtnAdd crBtnRemove" value="<?php echo $extra['extra_id']; ?>">
															<span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span>
															<?php __('front_btn_remove'); ?>
														</button>
														<?php
														switch ($extra['per'])
														{
															case 'day':
																$extraCost += $extra['price'] * $_SESSION[$controller->default_product][$controller->default_order]['rental_days']* $_SESSION[$controller->default_product][$controller->default_order]['extras'][$extra['extra_id']]['extra_quantity'];
																break;
															case 'booking':
																$extraCost += $extra['price'] * $_SESSION[$controller->default_product][$controller->default_order]['extras'][$extra['extra_id']]['extra_quantity'];;
																break;
														}
													}else{
														?>
														<button class="btn btn-default text-capitalize pull-right pjCrBtntDefault pjCrBtnAdd crBtnAdd" value="<?php echo $extra['extra_id']; ?>">
															<span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span>
															<?php __('front_btn_add'); ?>
														</button>
														<?php
													} 
													?>
												</div><!-- /.col-lg-2 col-md-2 col-sm-2 col-xs-12 -->
											</div><!-- /.row -->
										</li><!-- /.list-group-item pjCrProductExtra -->
										<?php
									}
									?>
								</ul><!-- /.list-group -->
							</form>
							<?php
							$rental_fee_detail = array();
							?>
							<div class="table-responsive">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table">
									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong><?php __('front_3_rental_duration'); ?></strong>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
											<?php
											if($tpl['cart']['day_added'] == 1)
											{
												$days++;
												echo $rental_duration = $days > 0 ? ($days == 1 ? $days ." ". __('front_label_day',true) : $days ." ". __('front_3_days',true) ) : '';
											}else{
												echo $rental_duration = $days > 0 ? ($days == 1 ? $days ." ". __('front_label_day',true) : $days ." ". __('front_3_days',true) ) : ''?><?php echo $days > 0 && $hours > 0 ? ' ' :'';?><?php echo $hours > 0 ?  $hours." ".__('front_3_hours',true) : '';
											}
											?>
										</td>
									</tr>
									<tr style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perday')) ? 'table-row' : 'none';?>">
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5"><strong><?php __('front_3_price_per_day'); ?></strong><br/><?php echo $tpl['cart']['price_per_day_detail']; ?></td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
											<?php echo $rental_fee_detail[] = pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['price_per_day']), 2), $tpl['option_arr']['o_currency'], " ");?>
										</td>
									</tr>
									<tr style="display:<?php echo in_array($tpl['option_arr']['o_booking_periods'], array('both', 'perhour')) ? 'table-row' : 'none';?>">
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5"><strong><?php __('front_3_price_per_hour'); ?></strong><br/><?php echo $tpl['cart']['price_per_hour_detail']; ?></td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
											<?php echo $rental_fee_detail[] = pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['price_per_hour']), 2), $tpl['option_arr']['o_currency'], " ");?>
										</td>
									</tr>
									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong><?php __('front_3_price'); ?></strong><br/><?php echo join(" + ", $rental_fee_detail);?>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right"><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['car_rental_fee']), 2), $tpl['option_arr']['o_currency'], " ");?></td>
									</tr>

									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong><?php __('front_3_extra_price'); ?></strong>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right"><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['extra_price']), 2), $tpl['option_arr']['o_currency'], " ");?></td>
									</tr>
									<?php
									if ($tpl['option_arr']['o_insurance_payment'] > 0)
									{
										$insurance_types = __('insurance_type_arr', true, false);
										?>
										<tr>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5"><strong><?php __('front_3_insurance'); ?></strong><br/>
												<?php
												switch ($tpl['option_arr']['o_insurance_payment_type']) {
													case 'perday':
														echo pjUtil::formatCurrencySign($tpl['option_arr']['o_insurance_payment'], $tpl['option_arr']['o_currency']) . ' ' . strtolower($insurance_types['perday']);
													;
													break;
													case 'percent':
														echo $tpl['option_arr']['o_insurance_payment'] . '% ' . __('front_label_of', true, false) . ' ' . pjUtil::formatCurrencySign($tpl['cart']['price'], $tpl['option_arr']['o_currency']);
													;
													break;
													case 'perbooking':
														echo pjUtil::formatCurrencySign($tpl['option_arr']['o_insurance_payment'], $tpl['option_arr']['o_currency']) . ' ' . strtolower($insurance_types['perbooking']);
													;
													break;
												}
												?>
											</td>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
												<?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['insurance']), 2), $tpl['option_arr']['o_currency'], " ");?>
											</td>
										</tr>
										<?php
									} 
									?>
									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong><?php __('front_3_sub_total'); ?></strong>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right"><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['sub_total']), 2), $tpl['option_arr']['o_currency'], " ");?></td>
									</tr>
									<?php
									if ($tpl['option_arr']['o_tax_payment'] > 0)
									{
										?>
										<tr>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5"><strong><?php __('front_3_tax'); ?></strong><br/>
												<?php
												if($tpl['option_arr']['o_tax_type'] == '1')
												{
													echo $tpl['option_arr']['o_tax_payment'] . '% ' . __('front_label_of', true, false) . ' ' . pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['sub_total']), 2), $tpl['option_arr']['o_currency'], " ");
												}
												?>
											</td>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
												<?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['tax']), 2), $tpl['option_arr']['o_currency'], " ");?>
											</td>
										</tr>
										<?php
									}
									?>
									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong class="text-danger pjCrColorPrimary"><?php __('front_3_total_price'); ?></strong>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right"><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['total_price']), 2), $tpl['option_arr']['o_currency'], " ");?></td>
									</tr>
									<?php
									if ($tpl['option_arr']['o_security_payment'] > 0)
									{
										?>
										<tr>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
												<strong><?php __('front_3_security_deposit'); ?></strong>
											</td>
											<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right">
												<?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['option_arr']['o_security_payment']), 2), $tpl['option_arr']['o_currency'], " ");?>
											</td>
										</tr>
										<?php
									} 
									?>
									<tr>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
											<strong><?php __('front_3_required_deposit'); ?></strong>
											<br />
											<?php
											switch ($tpl['option_arr']['o_deposit_type'])
											{
												case 'percent':
													echo $tpl['option_arr']['o_deposit_payment'] . '% ' . __('front_label_of', true, false) . ' ' . pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['total_price']), 2), $tpl['option_arr']['o_currency']);
													break;
												case 'amount':
													break;
											}
											?>
										</td>
										<td class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-right"><?php echo pjUtil::formatCurrencySign(number_format(floatval($tpl['cart']['required_deposit']), 2), $tpl['option_arr']['o_currency'], " ");?></td>
									</tr>
								</table><!-- /.table -->
							</div><!-- /.table-responsive -->
							<input type="button" value="<?php __('front_btn_continue'); ?>" id="crBtnCheckout" class="btn btn-default text-uppercase pjCrBtntDefault crBtnCheckout" />
						</div><!-- /.panel-body -->
					</div><!-- /.panel panel-default -->
				</div><!-- /.col-lg-8 col-md-8 col-sm-12 col-xs-12 pjCrPanelRight -->
			</div><!-- /.row -->
		</div><!-- /.panel-body pjCrBody -->
		
	</div><!-- /.panel panel-default -->
</div><!-- /.container-fluid pjCrContainer -->