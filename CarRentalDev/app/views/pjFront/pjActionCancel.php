<div style="margin: 0 auto; width: 450px">
<?php
$cancel_err = __('cancel_err', true);


if (isset($tpl['status']))
{
	?><p><?php echo $cancel_err[$tpl['status']]; ?></p><?php
}else{
	if (isset($_GET['err']))
	{
		?><p><?php echo $cancel_err[200]; ?></p><?php
	}
	if (isset($tpl['arr']))
	{
		$titles = __('_titles',true);
		
		?>
		<table cellspacing="2" cellpadding="5" style="width: 100%">
			<thead>
				<tr>
					<th colspan="2" style="text-transform: uppercase; text-align: left"><?php __('front_cancel_heading'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php __('front_cancel_from'); ?></td>
					<td><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($tpl['arr']['from'])); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_to'); ?></td>
					<td><?php echo date($tpl['option_arr']['o_datetime_format'], strtotime($tpl['arr']['to'])); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_pickup'); ?></td>
					<td><?php echo stripslashes($tpl['arr']['pickup_location']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_return'); ?></td>
					<td><?php echo stripslashes($tpl['arr']['return_location']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_type'); ?></td>
					<td><?php echo stripslashes($tpl['arr']['type']); ?></td>
				</tr>
				<?php
				foreach ($tpl['arr']['extra_arr'] as $k => $v)
				{
					?><tr><td>Extra <?php echo $k + 1; ?></td><td><?php
					$cell = array();
					$cell[] = pjUtil::formatCurrencySign(number_format($v['price'], 2), $tpl['option_arr']['o_currency']);
					$cell[] = stripslashes($v['name']);
					echo join(" / ", $cell);
					?></td></tr><?php
				}
				?>
				<tr>
					<td colspan="2" style="font-weight: bold"><?php __('front_cancel_personal'); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_title'); ?></td>
					<td><?php echo $titles[$tpl['arr']['c_title']]; ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_name'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_name']); ?></td>
				</tr>
				
				<tr>
					<td><?php __('front_cancel_phone'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_phone']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_email'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_email']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_company'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_company']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_address'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_address']); ?></td>
				</tr>
				
				<tr>
					<td><?php __('front_cancel_city'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_city']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_state'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_state']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_zip'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['c_zip']); ?></td>
				</tr>
				<tr>
					<td><?php __('front_cancel_country'); ?></td>
					<td><?php echo pjSanitize::html($tpl['arr']['country_title']); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<form action="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjFront&amp;action=pjActionCancel" method="post">
							<input type="hidden" name="booking_cancel" value="1" />
							<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
							<input type="hidden" name="hash" value="<?php echo $_GET['hash']; ?>" />
							<input type="submit" value="<?php echo __('front_cancel_confirm'); ?>" />
						</form>
					</td>
				</tr>
			</tfoot>
		</table>
		<?php
	}
}
?>
</div>