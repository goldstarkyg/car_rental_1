<?php
if (isset($tpl['arr']) && !empty($tpl['arr']))
{
	?>
	<form action="" method="post" class="form pj-form">
		<input type="hidden" name="send_sms" value="1" />
		<input type="hidden" name="id" value="<?php echo $tpl['arr']['id']; ?>" />
		<p>
			<span class="bold inline_block b5"><?php __('booking_message'); ?></span>
			<span><textarea name="message" id="confirm_message" class="pj-form-field w600 h120 required"><?php echo stripslashes(str_replace(array('\r\n', '\n'), '&#10;', $tpl['arr']['message'])); ?></textarea></span>
		</p>
		<?php 
		if (!empty($tpl['arr']['client_phone'])) 
		{ 
			?>
			<p>
				<label><b><?php __('lblCustomerPhone');?>:</b><input type="hidden" name="to" value="<?php echo pjSanitize::html($tpl['arr']['client_phone']); ?>"/> <?php echo pjSanitize::html($tpl['arr']['client_phone']); ?></label>
			</p>
			<?php
		}else{
			?>
			<label><input type="hidden" id="client_phone" name="to" value=""/></label>
			<?php
		}
		?>
	</form>
	<?php
}
?>