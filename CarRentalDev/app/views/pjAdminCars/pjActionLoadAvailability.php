<?php
$col_width = 230;
$num_cars = count($tpl['car_arr']); 
?>
<div class="pj-avail-legend">
	<div><abbr class="confirmed"></abbr><label><?php __('lblLegendConfirmed');?></label></div>
	<div><abbr class="pending"></abbr><label><?php __('lblLegendPending');?></label></div>
	<div><abbr class="pending-over"></abbr><label><?php __('lblLegendPendingOver');?></label></div>
</div>
<?php
if(count($tpl['car_arr']) > 0)
{ 
	?>
	<div class="pj-date-column">
		<table cellpadding="0" cellspacing="0" border="0" class="display">
			<thead>
				<tr class="title-head-row">
					<th><?php __('lblDate');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$run_date = $tpl['min_date'];
				$days = __('days', true, false);
				while($run_date <= $tpl['max_date'])
				{
					?>
					<tr class="title-row" lang="<?php echo date('Ymd', strtotime($run_date)) ?>">
						<td>
							<?php echo pjUtil::formatDate($run_date, "Y-m-d", $tpl['option_arr']['o_date_format']); ?>
							<br/>
							<?php echo $days[date('w', strtotime($run_date))];?>
						</td>
					</tr>
					<?php
					$run_date = date('Y-m-d', strtotime($run_date) + 86400);
				} 
				?>
			</tbody>
		</table>
	</div>
	<div class="pj-car-column">
	<div class="wrapper1">
	    <div class="div1-compare" style="width: <?php echo $col_width * $num_cars; ?>px;"></div>
		</div>
		<div class="wrapper2">
    		<div class="div2-compare" style="width: <?php echo $col_width * $num_cars; ?>px;">
    			<table cellpadding="0" cellspacing="0" border="0" class="display" id="compare_table" width="<?php echo $col_width * $num_cars; ?>px">
    				<thead>
						<tr class="content-head-row">
							<?php
						$j = 1;
						foreach($tpl['car_arr'] as $car)
						{
							?>
							<th class="<?php echo $j == 1 ? 'first-col' : null;?>" width="<?php echo $col_width;?>px">
								<?php echo pjSanitize::clean($car['car_name']);?> - <a href="<?php echo $_SERVER['PHP_SELF']; ?>?controller=pjAdminCars&amp;action=pjActionUpdate&id=<?php echo $car['id'];?>"><?php echo pjSanitize::clean($car['registration_number']);?></a>
								<br/>
								<?php echo pjSanitize::clean($car['type']);?>
							</th>
							<?php
							$j++;
						} 
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					$run_date = $tpl['min_date'];
					while($run_date <= $tpl['max_date'])
					{
						?>
						<tr id="content_row_<?php echo date('Ymd', strtotime($run_date)) ?>" class="">
							<?php
							$j = 1;
							foreach($tpl['car_arr'] as $car)
							{
								?>
								<td class="<?php echo $j == 1 ? 'first-col' : null;?>" >
									<?php
									$avail_arr = $tpl['avail_arr'][$car['id']][$run_date];
									if(empty($avail_arr))
									{
										?><br/></br><?php 
									}else{
										echo join(" ", $avail_arr);
									}
									?>
								</td>
								<?php
								$j++;
							} 
							?>
						</tr>
						<?php
						$run_date = date('Y-m-d', strtotime($run_date) + 86400);
					} 
					?>
					</tbody>
    			</table>
    		</div>
    	</div>
	</div>
	<?php
}else{
	?>
	
	<?php
} 
?>