<?php if(count($tpl['extra_arr']) > 0) { ?>
<?php
mt_srand();
$index = 'x_' . mt_rand();
$per_extra = __('per_extras', true, false);
?>
<tr>
	<td style="padding:7px 3px;vertical-align: top;">
		<select name="extra_id[<?php echo $index; ?>]" class="pj-form-field pj-extra-item b3" style="width: 220px;">
			<option value="" data-price="">-- <?php __('lblChoose'); ?> --</option>
			<?php
			if (isset($tpl['extra_arr']))
			{
				foreach ($tpl['extra_arr'] as $v)
				{
					?><option value="<?php echo $v['extra_id']; ?>" data-price="<?php echo pjUtil::formatCurrencySign($v['price'], $tpl['option_arr']['o_currency']) . ' ' . $per_extra[$v['per']]; ?>"><?php echo stripslashes($v['name']); ?></option><?php
				}
			}
			?>
		</select>
		<div class="pj-extra-price"></div>
	</td>
	<td style="padding:7px 3px; width: 60px;vertical-align: top;">
		<select name="extra_cnt[<?php echo $index; ?>]" class="pj-form-field pj-extra-qty">
			<?php
			for($i=1; $i<=10;$i++)
			{
				?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php
			}
			?>
		</select>
	</td>
	<td style="padding:7px 3px; width: 30px;">
		<a class="pj-table-icon-delete opExtraDel" data-id="1" href="#" title="Delete"></a>
	</td>
	
</tr>
<?php
foreach ($tpl['extra_arr'] as $v)
{
	?><input type="hidden" name="e_price[<?php echo $index; ?>]" value="<?php echo $v['price'] ?>" ><?php
}

?>
<?php }?>
