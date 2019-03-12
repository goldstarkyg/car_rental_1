<div class="container-fluid pjCrContainer">
	<div class="panel panel-default">
		<?php include_once dirname(__FILE__) . '/elements/header.php';?>
		
		<div class="panel-body text-center pjCrBody">
			<?php
			if($tpl['booking_arr']['total_price'] > 0)
			{ 
				?>
				<p><?php __('front_final_message_1');?></p>
				<?php
				if($tpl['booking_arr']['payment_method'] != 'paypal' && $tpl['booking_arr']['payment_method'] != 'authorize')
				{ 
					?>
					<p><?php __('front_final_message_2');?> <?php echo $tpl['booking_arr']['booking_id'];?></p>
					<?php
				}elseif($tpl['booking_arr']['payment_method'] == 'paypal'){
					?><p><?php __('front_msg_1');?></p><?php
					if (pjObject::getPlugin('pjPaypal') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
				}elseif($tpl['booking_arr']['payment_method'] == 'authorize'){
					?><p><?php __('front_msg_2');?></p><?php
					if (pjObject::getPlugin('pjAuthorize') !== NULL)
					{
						$controller->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionForm', 'params' => $tpl['params']));
					}
				}
				?>
				<p><?php __('front_final_message_3');?></p>
				<?php
				if($tpl['booking_arr']['payment_method'] != 'paypal' && $tpl['booking_arr']['payment_method'] != 'authorize')
				{ 
					?>
					<button id="crBtnStartOver" class="btn btn-default text-capitalize pjCrBtntDefault crBtnStartOver" type="button"><?php __('front_button_start_new');?></button>
					<?php
				}
			} 
			?>
		</div><!-- /.panel-body text-center pjCrBody -->
		
	</div><!-- /.panel panel-default -->
</div><!-- /.container-fluid pjCrContainer -->