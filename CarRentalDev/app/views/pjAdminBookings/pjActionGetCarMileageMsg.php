<?php if(count($tpl['arr']) > 0) {?>
	<?php echo stripslashes($tpl['arr']['make'] . " " . $tpl['arr']['model'] . " - " . $tpl['arr']['registration_number']); ?> <?php __('car_set_current_mileage') ; ?> <?php echo $_GET['mileage'] ?>. OK?
<?php } ?>