<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjFront extends pjAppController
{
	
	public $layout = 'pjFront';
	public $default_product = 'pjCarRental';
	public $default_captcha = 'pjCarRental_Captcha';
	public $default_order = 'pjCarRental_Order';
	public $default_language = 'CarRental_Language';
	public $default_locale = 'pjCarRental_Locale';
	public $defaultMethod = 'pjCarRental_Integration_Method';
	public $defaultTheme = 'front_theme_id';
	public $defaultStep = 'pjCarRental_Step';
	
	public function __construct()
	{
		if(isset($_GET['action']))
		{
			if($_GET['action'] != 'pjActionCancel')
			{
				$this->setLayout('pjActionFront');
				ob_start();
			}else{
				$this->setLayout('pjActionCancel');
			}
		}else{
			$_GET['action'] = 'pjActionCancel';
		}
		self::allowCORS();
	}

	public function afterFilter()
	{
		$term_arr = pjMultiLangModel::factory()
			->select('t1.content')
			->where('model', 'pjOption')
			->where('field', 'o_terms')
			->where('locale', $this->getLocaleId())
			->findAll()
			->getData();
		$this->set('term_arr',$term_arr) ;
	}
	
	public function beforeFilter()
	{
		$OptionModel = pjOptionModel::factory();
		$this->option_arr = $OptionModel->getPairs($this->getForeignId());
		$this->set('option_arr', $this->option_arr);
		$this->setTime();

		if(isset($_GET['theme']))
		{
			$this->setTheme($_GET['theme']);
		}
		
		if (!isset($_SESSION[$this->defaultLocale]))
		{
			$locale_arr = pjLocaleModel::factory()->where('is_default', 1)->limit(1)->findAll()->getData();
			if (count($locale_arr) === 1)
			{
				$this->setLocaleId($locale_arr[0]['id']);
			}
		}
		if(isset($_GET['pjLang']) && (int) $_GET['pjLang'] > 0)
		{
			$_SESSION[$this->defaultLocale] = (int) $_GET['pjLang'];
		}
		
		if (!in_array($_GET['action'], array('pjActionLoadCss')))
		{
			if(isset($_GET['pjLang']) && (int) $_GET['pjLang'] > 0 && $_GET['action'] == 'pjActionLoad')
			{
				$this->loadSetFields(true);
			}else{
				$this->loadSetFields();
			}
		}
		
		$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
			->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
			->where('t2.file IS NOT NULL')
			->orderBy('t1.sort ASC')->findAll()->getData();
		
		$this->set('locale_arr', $locale_arr);
	}
	
	public function beforeRender()
	{
	
	}
	
	public function pjActionLoadCss()
	{
		$dm = new pjDependencyManager(PJ_THIRD_PARTY_PATH);
		$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
		
		$theme = isset($_GET['theme']) ? $_GET['theme'] : $this->option_arr['o_theme'];
		if((int) $theme > 0)
		{
			$theme = 'theme' . $theme;
		}
		$arr = array(
			array('file' => 'style.css', 'path' =>  PJ_CSS_PATH),
			array('file' => 'bootstrap-datetimepicker.min.css', 'path' => $dm->getPath('pj_bootstrap_datetimepicker')),
			array('file' => "$theme.css", 'path' => PJ_CSS_PATH . "themes/")
		);
		header("Content-type: text/css");
		foreach ($arr as $item)
		{
			echo str_replace(
				array("pjWrapper"),
				array("pjWrapperCarRental_" . $theme),
				@file_get_contents($item['path'] . $item['file'])) . "\n";
		}
		exit;
	}
	
	public function pjActionLoad()
	{
		ob_start();
		header("Content-type: text/javascript");
	}
	
	public function pjActionCaptcha()
	{
		$this->setAjax(true);
		$Captcha = new pjCaptcha('app/web/obj/Anorexia.ttf', $this->defaultCaptcha, 6);
		$Captcha->setImage('app/web/img/button.png')->init(isset($_GET['rand']) ? $_GET['rand'] : null);
	}
	
	public function pjActionCheckCaptcha()
	{
		$this->setAjax(true);
		if (!isset($_GET['captcha']) || empty($_GET['captcha']) || strtoupper($_GET['captcha']) != $_SESSION[$this->defaultCaptcha]){
			echo 'false';
		}else{
			echo 'true';
		}
		exit;
	}
	
	public function pjActionLoadFinal()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if(isset($_GET['booking_id']))
			{
				$booking_arr = pjBookingModel::factory()->select('t1.*')
								  		  			->find($_GET['booking_id'])->getData();
			
				$car_arr = pjCarModel::factory()->select('t3.content as car_type')
												  ->join('pjCarType', "t1.id = t2.car_id", 'left')
												  ->join('pjMultiLang', "t3.model='pjType' AND t3.foreign_id=t2.type_id AND t3.field='name' AND t3.locale='".$booking_arr['locale_id']."'", 'left')
												  ->find($booking_arr['car_id'])->getData();
												 
				switch ($booking_arr['payment_method'])
				{
					case 'paypal':
						$this->set('params', array(
							'name' => 'crPaypal',
							'id' => 'crPaypal',
							'business' => $this->option_arr['o_paypal_address'],
							'item_name' => $car_arr['car_type'],
							'custom' => $booking_arr['id'],
							'amount' => $booking_arr['required_deposit'],
							'currency_code' => $this->option_arr['o_currency'],
							'return' => $this->option_arr['o_thankyou_page'],
							'notify_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmPaypal',
							'target' => '_self'
						));
						break;
					case 'authorize':
						$this->set('params', array(
							'name' => 'crAuthorize',
							'id' => 'crAuthorize',
							'target' => '_self',
							'timezone' => $this->option_arr['o_authorize_timezone'],
							'transkey' => $this->option_arr['o_authorize_transkey'],
							'x_login' => $this->option_arr['o_authorize_merchant_id'],
							'x_description' => $car_arr['car_type'],
							'x_amount' => $booking_arr['required_deposit'],
							'x_invoice_num' => $booking_arr['id'],
							'x_receipt_link_url' => $this->option_arr['o_thankyou_page'],
							'x_relay_url' => PJ_INSTALL_URL . 'index.php?controller=pjFront&action=pjActionConfirmAuthorize'
						));
						break;
				}
				
				$this->set('booking_arr', $booking_arr);
				
				if($_GET['controller'] == 'pjWebsite')
				{
					$this->setTemplate('pjWebsite', 'pjActionLoadFinal');
				}
			}
		}
	}
	
	public function getCarTypes($get)
	{
		$pjTypeModel = pjTypeModel::factory ();
		
		if (isset ( $get ['type_id'] ) && ! empty ( $get ['type_id'] ) && ( int ) $get ['type_id'] > 0) {
			$pjTypeModel->where ( 't1.id', $get ['type_id'] );
		}
		if (isset ( $get ['transmission'] ) && ! empty ( $get ['transmission'] )) {
			$pjTypeModel->where ( 't1.transmission', $get ['transmission'] );
		}
		$col_name = 'total_price';
		$direction = 'asc';
		if (isset ( $get ['col_name'] ) && isset ( $get ['direction'] )) {
			$col_name = $get ['col_name'];
			$direction = in_array ( strtoupper ( $get ['direction'] ), array (
					'ASC',
					'DESC' 
			) ) ? $get ['direction'] : 'ASC';
		}
		
		$col_name = $col_name == 't1.name' ? 't2.content' : $col_name;
		
		$current_datetime = date ( 'Y-m-d H:i:s', time () - ($this->option_arr ['o_booking_pending'] * 3600) );
		$_from = $_SESSION [$this->default_product] [$this->default_order] ['date_from'] . " " . $_SESSION [$this->default_product] [$this->default_order] ['hour_from'] . ":" . $_SESSION [$this->default_product] [$this->default_order] ['minutes_from'];
		$_to = $_SESSION [$this->default_product] [$this->default_order] ['date_to'] . " " . $_SESSION [$this->default_product] [$this->default_order] ['hour_to'] . ":" . $_SESSION [$this->default_product] [$this->default_order] ['minutes_to'];
		
		$pjTypeModel->select ( "t1.*, t2.content  AS `name`, t3.content  AS `description`
			 											, " . sprintf ( "(SELECT COUNT(*) FROM `%1\$s` WHERE `type_id` = `t1`.`id` AND `car_id` NOT IN (SELECT `car_id` FROM `%2\$s` WHERE (`status` = 'confirmed' OR `status` = 'collected' OR (`status` = 'pending' AND `created` >= '$current_datetime'))
					 													AND ( ((`from` BETWEEN '%3\$s' AND '%4\$s') OR (`to` BETWEEN '%3\$s' AND '%4\$s'))
					 													OR (`from` < '%3\$s' AND `to` > '%4\$s') OR (`from` > '%3\$s' AND `to` < '%4\$s') )
					 											) LIMIT 1 ) AS `cnt_available` ", pjCarTypeModel::factory ()->getTable (), pjBookingModel::factory ()->getTable (), $_from, $_to ) . "
				" )->join ( 'pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='" . $this->getLocaleId () . "'", 'left' )->join ( 'pjMultiLang', "t3.model='pjType' AND t3.foreign_id=t1.id AND t3.field='description' AND t3.locale='" . $this->getLocaleId () . "'", 'left' )->where ( 't1.status', 'T' );
		
		// PRICE SORT
		if ($col_name == 'total_price') {
			$arr = $pjTypeModel->findAll ()->getData ();
		} else {
			$arr = $pjTypeModel->orderBy ( $col_name . " " . $direction )->findAll ()->getData ();
		}
		
		$pjCarTypeModel = pjCarTypeModel::factory ();
		foreach ( $arr as $k => $v ) {
			$arr [$k] ['example'] = array ();
			$example = $pjCarTypeModel->reset ()->select ( 't1.*, t2.content as make , t3.content as model' )->join ( 'pjMultiLang', "t2.model='pjCar' AND t2.foreign_id=t1.car_id AND t2.field='make' AND t2.locale='" . $this->getLocaleId () . "'", 'left' )->join ( 'pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='model' AND t3.locale='" . $this->getLocaleId () . "'", 'left' )->where ( "(car_id NOT IN(SELECT TB.`car_id` FROM `" . pjBookingModel::factory ()->getTable () . "` AS TB WHERE (TB.`status` = 'confirmed' OR TB.`status` = 'collected' OR (TB.`status` = 'pending' AND `created` >= '$current_datetime')) AND ( ((`from` BETWEEN '$_from' AND '$_to') OR (`to` BETWEEN '$_from' AND '$_to')) OR (`from` < '$_from' AND `to` > '$_to') OR (`from` > '$_from' AND `to` < '$_to') ) ))" )->where ( 'type_id', $v ['id'] )->findAll ()->getData ();
			if (count ( $example ) > 0)
				$arr [$k] ['example'] = $example [0];
		}
		
		// PRICES
		$date_from = $_SESSION [$this->default_product] [$this->default_order] ['date_from'];
		$date_to = $_SESSION [$this->default_product] [$this->default_order] ['date_to'];
		
		$datetime_from = $date_from . " " . $_SESSION [$this->default_product] [$this->default_order] ['hour_from'] . ":" . $_SESSION [$this->default_product] [$this->default_order] ['minutes_from'];
		$datetime_to = $date_to . " " . $_SESSION [$this->default_product] [$this->default_order] ['hour_to'] . ":" . $_SESSION [$this->default_product] [$this->default_order] ['minutes_to'];
		
		foreach ( $arr as $k => $type_arr ) {
			$amount = 0;
			$price = 0;
			$arr [$k] ['total_price'] = 0;
			$price_arr = pjAppController::getPrices( $datetime_from, $datetime_to, $type_arr, $this->option_arr);
			$amount = $price_arr ['price'];
			if ($amount == 0) {
				$price_arr = pjAppController::getDefaultPrices( $datetime_from, $datetime_to, $type_arr, $this->option_arr);
				$amount = $price_arr ['price'];
			}
			$arr [$k] ['total_price'] = $amount;
		}
		
		// PRICE SORT
		$temp_arr = array ();
		$not_avail_arr = array ();
		$value = array ();
		if ($col_name == 'total_price') {
			foreach ( $arr as $k => $v ) {
				if ($v ['total_price'] > 0 && $v ['cnt_available']) {
					$temp_arr [$k] = $v ['total_price'];
				} else {
					$not_avail_arr [$k] = 0;
				}
			}
			
			if ($direction == 'asc') {
				asort ( $temp_arr );
			} else if ($direction == 'desc') {
				arsort ( $temp_arr );
			}
			foreach ( $temp_arr as $id => $val ) {
				$value [$id] = $arr [$id];
			}
			foreach ( $not_avail_arr as $id => $val ) {
				$value [$id] = $arr [$id];
			}
			$arr = $value;
		}
		return $arr;
	}
	
	public function pjActionLoadSearch()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('location_arr', pjSanitize::clean($location_arr));
			
			if (!isset($_SESSION[$this->default_product][$this->default_order]))
			{
				$to_string = "+2 days";
				switch ($this->option_arr['o_booking_periods'])
				{
					case 'perday':
						if ($this->option_arr['o_min_hour'] + 1 > 2)
						{
							$to_string = sprintf("+%u days", $this->option_arr['o_min_hour'] + 1);
						}
						break;
					default:
						if ($this->option_arr['o_min_hour'] > 24 * 2)
						{
							$to_string = sprintf("+%u hours", $this->option_arr['o_min_hour']);
						}
						break;
				}
				$_SESSION[$this->default_product][$this->default_order] = array();
				$_SESSION[$this->default_product][$this->default_order]['hour_from'] = "09";
				$_SESSION[$this->default_product][$this->default_order]['hour_to'] = "09";
				$_SESSION[$this->default_product][$this->default_order]['minutes_from'] = "00";
				$_SESSION[$this->default_product][$this->default_order]['minutes_to'] = "00";
				$_SESSION[$this->default_product][$this->default_order]['date_from'] = date('Y-m-d', strtotime("+1 day"));
				$_SESSION[$this->default_product][$this->default_order]['date_to'] = date('Y-m-d', strtotime($to_string));
				$_SESSION[$this->default_product][$this->default_order]['rental_days'] = 1;
			}
			
			if(isset($_GET['index']) && $_GET['index'] == 0){
				unset($_SESSION[$this->default_product][$this->default_order]['1_passed']);
				unset($_SESSION[$this->default_product][$this->default_order]['2_passed']);
				unset($_SESSION[$this->default_product][$this->default_order]['3_passed']);
			}
			if($_GET['controller'] == 'pjWebsite')
			{
				$this->setTemplate('pjWebsite', 'pjActionLoadSearch');
			}
		}
	}
	public function pjActionApplySearch()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_POST['date_from']))
			{
				$date_from = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$date_to = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
			
				$seconds = abs(strtotime($date_to . " " . $_POST['hour_to'].":".$_POST['minutes_to']) - strtotime($date_from. " " . $_POST['hour_from'].":".$_POST['minutes_from']));
				$rental_days = floor($seconds / 86400);
				$rental_hours = ceil($seconds / 3600);
				$extra_hours = intval($rental_hours - ($rental_days * 24));
				unset($_POST['date_from']);
				unset($_POST['date_to']);
				$_SESSION[$this->default_product][$this->default_order] = array_merge($_POST, compact('date_from', 'date_to', 'rental_days','rental_hours'));
			}
			$_SESSION[$this->default_product][$this->default_order]['1_passed'] = true;
			unset($_SESSION[$this->default_product][$this->default_order]['2_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['3_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['4_passed']);
			$_SESSION[$this->defaultStep] = 2;
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Data has been applied.'));
		}
		exit;
	}
	public function pjActionSetCarType()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			
			$_SESSION[$this->default_product][$this->default_order]['1_passed'] = true;
			$_SESSION[$this->default_product][$this->default_order]['2_passed'] = true;
			unset($_SESSION[$this->default_product][$this->default_order]['3_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['4_passed']);
			
			if($_SESSION[$this->default_product][$this->default_order]['type_id'] != $_GET['type_id'])
			{
				unset($_SESSION[$this->default_product][$this->default_order]['extras']);
			}
			
			$_SESSION[$this->default_product][$this->default_order]['type_id'] = $_GET['type_id'];
			$_SESSION[$this->defaultStep] = 3;
		}
	}
	public function pjActionLoadCars()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if (isset($_POST['date_from']))
			{
				$date_from = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
				$date_to = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				
		    	$seconds = abs(strtotime($date_to . " " . $_POST['hour_to'].":".$_POST['minutes_to']) - strtotime($date_from. " " . $_POST['hour_from'].":".$_POST['minutes_from']));
				$rental_days = floor($seconds / 86400);
				$rental_hours = ceil($seconds / 3600);
				$extra_hours = intval($rental_hours - ($rental_days * 24));
				unset($_POST['date_from']);
				unset($_POST['date_to']);					
				$_SESSION[$this->default_product][$this->default_order] = array_merge($_POST, compact('date_from', 'date_to', 'rental_days','rental_hours'));
			}
			$_SESSION[$this->default_product][$this->default_order]['1_passed'] = true;
			unset($_SESSION[$this->default_product][$this->default_order]['2_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['3_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['4_passed']);
			
			$arr = $this->getCarTypes($_GET);
			
			$this->set('arr', $arr);
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pickup_location = $pjMultiLangModel->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['pickup_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
			$this->set('pickup_location',$pickup_location[0]) ;
			
			if (!isset($_SESSION[$this->default_product][$this->default_order]['same_location']))
			{
				$return_location_arr = $pjMultiLangModel->reset()->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['return_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
													
				$return_location = $return_location_arr[0] ;
			}else{
				$return_location = $pickup_location[0];
			}
			
			$this->set('return_location',$return_location) ;
			
			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('type_arr',$type_arr) ;
			
			if($_GET['controller'] == 'pjWebsite')
			{
				$this->setTemplate('pjWebsite', 'pjActionLoadCars');
			}
		}
	}
	
	public function pjActionLoadExtras()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = array();
			
			$_SESSION[$this->default_product][$this->default_order]['1_passed'] = true;
			$_SESSION[$this->default_product][$this->default_order]['2_passed'] = true;
			unset($_SESSION[$this->default_product][$this->default_order]['3_passed']);
			unset($_SESSION[$this->default_product][$this->default_order]['4_passed']);
			
			if(isset($_SESSION[$this->default_product][$this->default_order]['type_id']) && $_SESSION[$this->default_product][$this->default_order]['type_id'] != $_GET['type_id'])
			{
				unset($_SESSION[$this->default_product][$this->default_order]['extras']);
			}
			
			$_SESSION[$this->default_product][$this->default_order]['type_id'] = $_GET['type_id'];
			
			$pjTypeModel = pjTypeModel::factory();
			$arr = pjTypeExtraModel::factory()->select('t1.*, t2.content AS name , t3.price , t3.per, t3.type AS extra_type')
									->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
									->join('pjExtra', 't3.id = t1.extra_id')
								    ->where('t1.type_id', $_GET['type_id'])
								    ->where('t3.status', 'T')
								    ->orderBy('t1.extra_id ASC')->findAll()->getData();
			
			$this->set('arr',$arr) ;
			
			
			$type_arr = $pjTypeModel->select("t1.*, t2.content as name")
									->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
									->find($_GET['type_id'])->getData();
									
			$type_arr['example'] = array();
			$current_datetime = date('Y-m-d H:i:s', time() - ($this->option_arr['o_booking_pending'] * 3600));
			$_from = $_SESSION[$this->default_product][$this->default_order]['date_from'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_from'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_from'];
			$_to = $_SESSION[$this->default_product][$this->default_order]['date_to'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_to'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_to'];
			$example = pjCarTypeModel::factory()->reset()->select('t1.*, t2.content as make , t3.content as model')
												 ->join('pjMultiLang', "t2.model='pjCar' AND t2.foreign_id=t1.car_id AND t2.field='make' AND t2.locale='".$this->getLocaleId()."'", 'left')
												 ->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='model' AND t3.locale='".$this->getLocaleId()."'", 'left')
												 ->where("(car_id NOT IN(SELECT TB.`car_id` FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE (TB.`status` = 'confirmed' OR TB.`status`='collected' OR (TB.`status` = 'pending' AND `created` >= '$current_datetime')) AND ( ((`from` BETWEEN '$_from' AND '$_to') OR (`to` BETWEEN '$_from' AND '$_to')) OR (`from` < '$_from' AND `to` > '$_to') OR (`from` > '$_from' AND `to` < '$_to') ) ))")
												 ->where('type_id',$_GET['type_id'])->findAll()->getData();
			if(count($example) > 0)
			$type_arr['example'] = $example[0];
			
			
			$this->set('type_arr',$type_arr) ;
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pickup_location = $pjMultiLangModel->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['pickup_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
			$this->set('pickup_location',$pickup_location[0]) ;
			
			if (!isset($_SESSION[$this->default_product][$this->default_order]['same_location']))
			{
				$return_location_arr = $pjMultiLangModel->reset()->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['return_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
													
				$return_location = $return_location_arr[0] ;
			}else{
				$return_location = $pickup_location[0];
			}
			
			$this->set('return_location',$return_location) ;
			
			$type_arr = $pjTypeModel->reset()->find($_GET['type_id'])->getData();
			
			$date_from = $_SESSION[$this->default_product][$this->default_order]['date_from'];
			$date_to = $_SESSION[$this->default_product][$this->default_order]['date_to'];
			
			$datetime_from = $date_from." ".$_SESSION[$this->default_product][$this->default_order]['hour_from'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_from'];
			$datetime_to = $date_to." ".$_SESSION[$this->default_product][$this->default_order]['hour_to'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_to'];
			
			$cart = pjAppController::getCartTotal($this->default_product, $this->default_order, $this->option_arr);
		
			$this->set('cart', $cart);
			
			$term_arr = pjMultiLangModel::factory()
				->select('t1.content')
				->where('model', 'pjOption')
				->where('field', 'o_terms')
				->where('locale', $this->getLocaleId())
				->findAll()
				->getData();
			$this->set('term_arr',$term_arr) ;
			
			if($_GET['controller'] == 'pjWebsite')
			{
				$this->setTemplate('pjWebsite', 'pjActionLoadExtras');
			}
		}
	}
	
	public function pjActionAddExtra()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$code = 100;
			if (!isset($_SESSION[$this->default_product][$this->default_order]))
			{
				$_SESSION[$this->default_product][$this->default_order] = array();
			}
			if (!isset($_SESSION[$this->default_product][$this->default_order]['extras']))
			{
				$_SESSION[$this->default_product][$this->default_order]['extras'] = array();
			}
			
			if (!array_key_exists($_GET['extra_id'], $_SESSION[$this->default_product][$this->default_order]['extras']))
			{
				$arr = pjExtraModel::factory()->find($_GET['extra_id'])->getData();
				if (count($arr) > 0)
				{
					$_SESSION[$this->default_product][$this->default_order]['extras'][$_GET['extra_id']] = $arr;
					$_SESSION[$this->default_product][$this->default_order]['extras'][$_GET['extra_id']]['extra_quantity'] = isset($_REQUEST['extra_quantity'][$_GET['extra_id']]) ? $_REQUEST['extra_quantity'][$_GET['extra_id']] : 1;
					$code = 200;
				}
			}
			else{
				if($_REQUEST['extra_quantity'][$_GET['extra_id']] != $_SESSION[$this->default_product][$this->default_order]['extras'][$_GET['extra_id']]['extra_quantity'] ){
					$_SESSION[$this->default_product][$this->default_order]['extras'][$_GET['extra_id']]['extra_quantity'] = isset($_REQUEST['extra_quantity'][$_GET['extra_id']]) ? $_REQUEST['extra_quantity'][$_GET['extra_id']] : 1;
					
					$code = 200;
				}
			}
			
			
			header("Content-type: application/json; charset=utf-8");
			echo '{"code":'.$code.'}';
			exit;
		}
	}
	
	public function pjActionRemoveExtra()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$code = 100;
			if (isset($_SESSION[$this->default_product][$this->default_order]) && is_array($_SESSION[$this->default_product][$this->default_order]) &&
				isset($_SESSION[$this->default_product][$this->default_order]['extras']) && is_array($_SESSION[$this->default_product][$this->default_order]['extras']) &&
				array_key_exists($_GET['extra_id'], $_SESSION[$this->default_product][$this->default_order]['extras']))
			{
				unset($_SESSION[$this->default_product][$this->default_order]['extras'][$_GET['extra_id']]);
				$code = 200;
			}
			
			header("Content-type: application/json; charset=utf-8");
			echo '{"code":'.$code.'}';
			exit;
		}
	}
	
	public function pjActionLoadCheckout(){
		
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$arr = array();
			
			$_SESSION[$this->default_product][$this->default_order]['1_passed'] = true;
			$_SESSION[$this->default_product][$this->default_order]['2_passed'] = true;
			$_SESSION[$this->default_product][$this->default_order]['3_passed'] = true;
			unset($_SESSION[$this->default_product][$this->default_order]['4_passed']);
			
			$pjMultiLangModel = pjMultiLangModel::factory();
			$pickup_location = $pjMultiLangModel->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['pickup_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
			$this->set('pickup_location',$pickup_location[0]) ;
			
			if (!isset($_SESSION[$this->default_product][$this->default_order]['same_location']))
			{
				$return_location_arr = $pjMultiLangModel->reset()->select('t1.content AS name ')
													  ->where('model', 'pjLocation')
													  ->where('field', 'name')
													  ->where('foreign_id', @$_SESSION[$this->default_product][$this->default_order]['return_id'])
													  ->where('locale', $this->getLocaleId())
													  ->findAll()->getData();
													
				$return_location = $return_location_arr[0] ;
			}
			else{
				$return_location = $pickup_location[0];
			}
			
			$this->set('return_location',$return_location) ;
			
			$type_id = @$_SESSION[$this->default_product][$this->default_order]['type_id'];
			$type_arr = pjTypeModel::factory()->select("t1.*, t2.content as name")
									->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
									->find($type_id)->getData();
									
			$type_arr['example'] = array();
			$current_datetime = date('Y-m-d H:i:s', time() - ($this->option_arr['o_booking_pending'] * 3600));
			$_from = $_SESSION[$this->default_product][$this->default_order]['date_from'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_from'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_from'];
			$_to = $_SESSION[$this->default_product][$this->default_order]['date_to'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_to'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_to'];
			$example = pjCarTypeModel::factory()->reset()->select('t1.*, t2.content as make , t3.content as model')
												 ->join('pjMultiLang', "t2.model='pjCar' AND t2.foreign_id=t1.car_id AND t2.field='make' AND t2.locale='".$this->getLocaleId()."'", 'left')
												 ->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='model' AND t3.locale='".$this->getLocaleId()."'", 'left')
												 ->where("(car_id NOT IN(SELECT TB.`car_id` FROM `".pjBookingModel::factory()->getTable()."` AS TB WHERE (TB.`status` = 'confirmed' OR TB.`status` = 'collected' OR (TB.`status` = 'pending' AND `created` >= '$current_datetime')) AND ( ((`from` BETWEEN '$_from' AND '$_to') OR (`to` BETWEEN '$_from' AND '$_to')) OR (`from` < '$_from' AND `to` > '$_to') OR (`from` > '$_from' AND `to` < '$_to') ) ))")
												 ->where('type_id',$type_id)->findAll()->getData();
			if(count($example) > 0)
			{
				$type_arr['example'] = $example[0];
				$_SESSION[$this->default_product][$this->default_order]['car_id'] = $example[0]['car_id'];
			}
			
			
			$this->set('type_arr',$type_arr) ;
			
			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
			
			$this->set('country_arr', $country_arr);
			
			$extra_arr = pjTypeExtraModel::factory()->select('t1.*, t2.content AS name , t3.price , t3.per ')
									->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
									->join('pjExtra', 't3.id = t1.extra_id')
								    ->where('t1.type_id', $type_id)
								    ->where('t3.status', 'T')
								    ->orderBy('t1.extra_id ASC')->findAll()->getData();
			$this->set('extra_arr',$extra_arr) ;
			
			$type_arr = pjTypeModel::factory()->find($type_id)->getData();
			
			$date_from = $_SESSION[$this->default_product][$this->default_order]['date_from'];
			$date_to = $_SESSION[$this->default_product][$this->default_order]['date_to'];
			
			$datetime_from = $date_from." ".$_SESSION[$this->default_product][$this->default_order]['hour_from'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_from'];
			$datetime_to = $date_to." ".$_SESSION[$this->default_product][$this->default_order]['hour_to'] . ":" . $_SESSION[$this->default_product][$this->default_order]['minutes_to'];
			
			$cart = pjAppController::getCartTotal($this->default_product, $this->default_order, $this->option_arr);
			
			$this->set('cart', $cart);
			
			if($_GET['controller'] == 'pjWebsite')
			{
				$this->setTemplate('pjWebsite', 'pjActionLoadCheckout');
			}
		}
	}
	
	public function pjActionBookingSave()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$_SESSION[$this->default_product][$this->default_order]['4_passed'] = true;
			
			if ((int) $this->option_arr['o_bf_include_captcha'] === 3 && (!isset($_POST['captcha']) ||
					!pjCaptcha::validate($_POST['captcha'], $_SESSION[$this->defaultCaptcha]) ))
			{
				$json = array('code' => 100, 'text' => '');
				pjAppController::jsonResponse($json);
			}
			$_SESSION[$this->defaultCaptcha] = NULL;
			unset($_SESSION[$this->defaultCaptcha]);
			
			$opts = pjAppController::getCartTotal($this->default_product, $this->default_order, $this->option_arr);
			$data = array();
			if ($this->option_arr['o_payment_disable'] == 'Yes')
			{
				$data['status'] = $this->option_arr['o_booking_status'];
			} else {
				$data['status'] = $this->option_arr['o_booking_status']; 
			}
			
			$data['rental_days']   = $opts['rental_days'];
			$data['rental_hours']   = $opts['rental_hours'];
			$data['price_per_hour']   = $opts['price_per_hour'];
			$data['price_per_day']   = $opts['price_per_day'];
			$data['price_per_hour']   = $opts['price_per_hour'];
			$data['price_per_day_detail']   = $opts['price_per_day_detail'];
			$data['price_per_hour_detail']   = $opts['price_per_hour_detail'];
			$data['car_rental_fee']   = $opts['car_rental_fee'];
			$data['extra_price']   = $opts['extra_price'];
			$data['insurance']   = $opts['insurance'];
			$data['sub_total']   = $opts['sub_total'];
			$data['tax']   = $opts['tax'];
			$data['total_price']   = $opts['total_price'];
			$data['required_deposit']   = $opts['required_deposit'];
			$data['security_deposit']   = $opts['security_deposit'];
			
			$data['from'] = $_SESSION[$this->default_product][$this->default_order]['date_from'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_from']. ":" .$_SESSION[$this->default_product][$this->default_order]['minutes_from'].":00";
			$data['to'] = $_SESSION[$this->default_product][$this->default_order]['date_to'] . " " . $_SESSION[$this->default_product][$this->default_order]['hour_to']. ":" .$_SESSION[$this->default_product][$this->default_order]['minutes_to'].":00";
			$data['uuid'] = time();
			$data['booking_id'] = $this->getBookingID();
			$data['ip'] = pjUtil::getClientIp();
			$data['locale_id'] = $this->getLocaleId();
			
			if (isset($_SESSION[$this->default_product][$this->default_order]['same_location']))
			{
				$data['return_id'] = $_SESSION[$this->default_product][$this->default_order]['pickup_id'];
			}
			
			$payment = 'none';
			if (isset($_POST['payment_method']))
			{
				$payment = $_POST['payment_method'];
			}
			
			
			$pjBookingModel = pjBookingModel::factory();
			$pjCarTypeModel = pjCarTypeModel::factory();
			
			$current_datetime = date('Y-m-d H:i:s', time() - ($this->option_arr['o_booking_pending'] * 3600));
			
			if (isset($_SESSION[$this->default_product][$this->default_order]['car_id']))
			{
				$data['car_id'] = $_SESSION[$this->default_product][$this->default_order]['car_id'];
			}
			$booking_id = $pjBookingModel
							->setAttributes(array_merge($_POST, $_SESSION[$this->default_product][$this->default_order],$data))
							->insert()
							->getInsertId();
			
			if ($booking_id !== false && (int) $booking_id > 0)
			{
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				
				if (isset($_SESSION[$this->default_product][$this->default_order]) && isset($_SESSION[$this->default_product][$this->default_order]['extras']))
				{
					$be = array();
					$be['booking_id'] = $booking_id;
					
					foreach ($_SESSION[$this->default_product][$this->default_order]['extras'] as $extra_id => $be_arr)
					{
						if(is_numeric($extra_id)){
							$be['extra_id'] = $extra_id;
							$be['price'] = $be_arr['price'];
							$be['quantity'] = $be_arr['extra_quantity'];
							$pjBookingExtraModel->setAttributes($be)->insert();
						}
					}
				}
				
				$booking_arr = $pjBookingModel->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
											  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
											  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
											  ->find($booking_id)->getData();
				
				if (count($booking_arr) > 0)
				{
					$extra_arr = $pjBookingExtraModel->select("t1.*, t2.content as name, t3.price")
													 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
													 ->join('pjExtra', "t3.id = t1.extra_id")
													 ->where('t1.booking_id',$booking_arr['id'])
													 ->findAll()->getData();
					$booking_arr['extra_arr'] = $extra_arr;
				}
				
				$pdata = array();
				$pdata['booking_id'] = $booking_arr['id'];
				$pdata['payment_method'] = $payment;
				$pdata['payment_type'] = 'online';
				$pdata['amount'] = $booking_arr['required_deposit'];
				$pdata['status'] = 'notpaid';
				pjBookingPaymentModel::factory()->setAttributes($pdata)->insert();
				
				pjFront::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'confirm');
				
				$_SESSION[$this->default_product][$this->default_order] = array();
				unset($_SESSION[$this->default_product][$this->default_order]);
				$json = array('code' => 200, 'text' => '', 'booking_id' => $booking_id, 'payment' => $payment);
			} else {
				$json = array('code' => 100, 'text' => '');
			}
			pjAppController::jsonResponse($json);
		}
	}
	
	public function pjActionGetLocations()
	{
		$this->isAjax = true;
	
		if ($this->isXHR())
		{
			$location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name, t3.content AS city, t4.content AS state, t5.content AS address_1')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t3.model='pjLocation' AND t3.foreign_id=t1.id AND t3.field='city' AND t3.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t4.model='pjLocation' AND t4.foreign_id=t1.id AND t4.field='state' AND t4.locale='".$this->getLocaleId()."'", 'left')
					->join('pjMultiLang', "t5.model='pjLocation' AND t5.foreign_id=t1.id AND t5.field='address_1' AND t5.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				
			pjAppController::jsonResponse($location_arr);
		}
	}
	
	public function pjActionGetTerms()
	{
		$this->setAjax(true);
		if ($this->isXHR())
		{
			$term_arr = pjMultiLangModel::factory()->select('t1.content  ')
												  ->where('model', 'pjOption')
												  ->where('field', 'o_terms')
												  ->where('locale', $this->getLocaleId())
												  ->findAll()->getData();
			$this->set('term_arr',$term_arr) ;
		}
	}
	
	public function pjActionSetLocale()
	{
		$this->setAjax(true);
		if ($this->isXHR())
		{
			$locale = @$_GET['locale'];
			$_SESSION[$this->defaultLocale] = (int) $locale;
			
			$this->loadSetFields(true);
			
			$months = __('months', true);
			ksort($months);
			
			$option_arr = array(
				'folder' => PJ_INSTALL_FOLDER,
				'validation' => array(
					'error_dates' => str_replace("{HOURS}", $this->option_arr['o_min_hour'], __('front_1_v_err_dates', true, false)),
					'error_title' => __('front_4_v_err_title', true),
					'error_email' => __('front_4_v_err_email', true),
					'error_length' => str_replace("{DAYS}", $this->option_arr['o_min_hour'], __('front_1_v_err_length', true, false))
				),
				'message_1' => __('front_msg_1', true),
				'message_2' => __('front_msg_2', true),
				'message_3' => __('front_msg_3', true),
				'message_4' => __('front_msg_4', true),
				'dateFormat' => $this->option_arr['o_date_format'],
				'dayNames' => array_values(__('day_names', true)),
				'monthNamesFull' => array_values($months),
				'closeButton' => __('front_1_close', true),
				'momentDateFormat' => pjUtil::toMomemtJS($this->option_arr['o_date_format']),
				'time_format' => $this->option_arr['o_time_period'] == '12hours' ? 'LT' : "HH:mm"
			);
			
			pjAppController::jsonResponse($option_arr);
		}
	}
	
	public function pjActionConfirmSend($option_arr, $booking_arr, $salt, $opt)
	{
		$Email = new pjEmail();
		if ($option_arr['o_send_email'] == 'smtp')
		{
			$Email
				->setTransport('smtp')
				->setSmtpHost($option_arr['o_smtp_host'])
				->setSmtpPort($option_arr['o_smtp_port'])
				->setSmtpUser($option_arr['o_smtp_user'])
				->setSmtpPass($option_arr['o_smtp_pass'])
			;
		}
		$Email->setContentType('text/html');

		$tokens = pjAppController::getTokens($booking_arr, $option_arr, $salt, $this->getLocaleId());
							
		$pjMultiLangModel = pjMultiLangModel::factory();
		
		$locale_id = isset($booking_arr['locale_id']) && (int) $booking_arr['locale_id'] > 0 ? (int) $booking_arr['locale_id'] : 1;
	
		$admin_email = $this->getAdminEmail();
		$admin_phone = $this->getAdminPhone();
		
		$pickup_email = null;
		$dropoff_email = null;
		if(isset($booking_arr['pickup_id']))
		{
			$pickup_arr = pjLocationModel::factory()->find($booking_arr['pickup_id'])->getData();
			if($pickup_arr['notify_email'] == 'T')
			{
				$pickup_email = $pickup_arr['email'];
			}
		}
		if(isset($booking_arr['return_id']))
		{
			$return_arr = pjLocationModel::factory()->find($booking_arr['return_id'])->getData();
			if($return_arr['notify_email'] == 'T')
			{
				$dropoff_email = $return_arr['email'];
			}
		}
		
		# Payment email
		if ($option_arr['o_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_payment_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_payment_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to CLIENT
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
					
				if($pickup_email != null)
				{
					$Email
						->setTo($pickup_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
				if($dropoff_email != null)
				{
					$Email
						->setTo($dropoff_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
		}
		if ($option_arr['o_admin_email_payment'] == 1 && $opt == 'payment')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_payment_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_payment_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
				
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to ADMIN
				$Email
					->setTo($admin_email)
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
			}
		}
		if(!empty($admin_phone))
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
												 ->where('t1.model','pjOption')
												 ->where('t1.locale', $locale_id)
												 ->where('t1.field', 'o_admin_sms_payment_message')
												 ->limit(0, 1)
												 ->findAll()->getData();
			if (count($lang_message) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				$params = array(
					'text' => $message,
					'type' => 'unicode',
					'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			}
		}
		
		
		# Confirmation email
		if ($option_arr['o_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_confirmation_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
						   
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to CLIENT
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
					
				if($pickup_email != null)
				{
					$Email
						->setTo($pickup_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
				if($dropoff_email != null)
				{
					$Email
						->setTo($dropoff_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
		}
		if ($option_arr['o_admin_email_confirmation'] == 1 && $opt == 'confirm')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_confirmation_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
						   
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to ADMIN
				$Email
					->setTo($admin_email)
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
			}
		}
		if(!empty($admin_phone))
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
												 ->where('t1.model','pjOption')
												 ->where('t1.locale', $locale_id)
												 ->where('t1.field', 'o_admin_sms_confirmation_message')
												 ->limit(0, 1)
												 ->findAll()->getData();
			if (count($lang_message) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				$params = array(
					'text' => $message,
					'type' => 'unicode',
					'key' => md5($option_arr['private_key'] . PJ_SALT)
				);
				$params['number'] = $admin_phone;
				$this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			}
		}
		
		# Cancel email
		if ($option_arr['o_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_cancel_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_email_cancel_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
						   
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to CLIENT
				$Email
					->setTo($booking_arr['c_email'])
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
					
				if($pickup_email != null)
				{
					$Email
						->setTo($pickup_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
				if($dropoff_email != null)
				{
					$Email
						->setTo($dropoff_email)
						->setFrom($admin_email)
						->setSubject($lang_subject[0]['content'])
						->send(pjUtil::textToHtml($message));
				}
			}
		}
		if ($option_arr['o_admin_email_cancel'] == 1 && $opt == 'cancel')
		{
			$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_cancel_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
			$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $locale_id)
											 ->where('t1.field', 'o_admin_email_cancel_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
						   
			if (count($lang_message) === 1 && count($lang_subject) === 1)
			{
				$message = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
				# Send to ADMIN
				$Email
					->setTo($admin_email)
					->setFrom($admin_email)
					->setSubject($lang_subject[0]['content'])
					->send(pjUtil::textToHtml($message));
			}
		}
	}
	
	public function pjActionConfirmAuthorize()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjAuthorize') === NULL)
		{
			$this->log('Authorize.NET plugin not installed');
			exit;
		}
		
		$pjBookingModel = pjBookingModel::factory();
		$booking_arr = $pjBookingModel->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
									  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
									  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
									  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
									  ->find($_POST['x_invoice_num'])->getData();
							
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
		
		if (count($booking_arr) > 0)
		{
			$params = array(
				'transkey' => $this->option_arr['o_authorize_transkey'],
				'x_login' => $this->option_arr['o_authorize_merchant_id'],
				'md5_setting' => $this->option_arr['o_authorize_md5_hash'],
				'key' => md5($this->option_arr['private_key'] . PJ_SALT)
			);
			
			$response = $this->requestAction(array('controller' => 'pjAuthorize', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
			if ($response !== FALSE && $response['status'] === 'OK')
			{
				$pjBookingModel->reset()
					->setAttributes(array('id' => $response['transaction_id']))
					->modify(array('status' => $this->option_arr['o_payment_status'], 'processed_on' => ':NOW()'));

				pjBookingPaymentModel::factory()->setAttributes(array('booking_id' => $response['transaction_id'], 'payment_type' => 'online'))
												->modify(array('status' => 'paid'));
					
				if (count($booking_arr) > 0)
				{
					$pjBookingExtraModel = pjBookingExtraModel::factory();
					$extra_arr = $pjBookingExtraModel->select("t1.*, t2.content as name, t3.price")
													  ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
													  ->join('pjExtra', "t3.id = t1.extra_id")
													  ->where('t1.booking_id',$booking_arr['id'])
													  ->findAll()->getData();
					$booking_arr['extra_arr'] = $extra_arr;
				}
				
				pjFront::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment');
				
			} elseif (!$response) {
				$this->log('Authorization failed');
			} else {
				$this->log('Booking not confirmed. ' . $response['response_reason_text']);
			}
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
	}

	public function pjActionConfirmPaypal()
	{
		$this->setAjax(true);
		
		if (pjObject::getPlugin('pjPaypal') === NULL)
		{
			$this->log('Paypal plugin not installed');
			exit;
		}
		
		
		$pjBookingModel = pjBookingModel::factory();
		$booking_arr = $pjBookingModel->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
									  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
									  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
									  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
									  ->find($_POST['custom'])->getData();
		if (count($booking_arr) == 0)
		{
			$this->log('No such booking');
			pjUtil::redirect($this->option_arr['o_thankyou_page']);
		}
		
		$params = array(
			'txn_id' => @$booking_arr['txn_id'],
			'paypal_address' => $this->option_arr['o_paypal_address'],
			'deposit' => @$booking_arr['required_deposit'],
			'currency' => $this->option_arr['o_currency'],
			'key' => md5($this->option_arr['private_key'] . PJ_SALT)
		);
		$response = $this->requestAction(array('controller' => 'pjPaypal', 'action' => 'pjActionConfirm', 'params' => $params), array('return'));
		
		if ($response !== FALSE && $response['status'] === 'OK')
		{
			$this->log('Booking confirmed');
			$pjBookingModel->reset()->setAttributes(array('id' => $booking_arr['id']))->modify(array(
				'status' => $this->option_arr['o_payment_status'],
				'txn_id' => $response['transaction_id'],
				'processed_on' => ':NOW()'
			));
			$pjBookingPaymentModel = pjBookingPaymentModel::factory();
			$bp_arr = $pjBookingPaymentModel->where('t1.booking_id', $booking_arr['id'])->where('t1.payment_type', 'online')->limit(1)->findAll()->getData();
			if(count($bp_arr) == 1)
			{
				$pjBookingPaymentModel->reset()->setAttributes(array('id' => $bp_arr[0]['id']))->modify(array('status' => 'paid'));
			}
			
			if (count($booking_arr) > 0)
			{
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				$extra_arr = $pjBookingExtraModel->select("t1.*, t2.content as name, t3.price")
												 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
												 ->join('pjExtra', "t3.id = t1.extra_id")
												 ->where('t1.booking_id',$booking_arr['id'])
												 ->findAll()->getData();
				$booking_arr['extra_arr'] = $extra_arr;
			}
			
			pjFront::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'payment');
			
		} elseif (!$response) {
			$this->log('Authorization failed');
		} else {
			$this->log('Booking not confirmed');
		}
		pjUtil::redirect($this->option_arr['o_thankyou_page']);
	}
	
	public function pjActionCancel()
	{
		$this->setLayout('pjActionIframe');
		
		$pjBookingModel = pjBookingModel::factory();
		
		if (isset($_POST['booking_cancel']))
		{
			$booking_arr = $pjBookingModel->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
									  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
									  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
									  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
									  ->find($_POST['id'])->getData();
			
			
			if (count($booking_arr) > 0)
			{
				$pjBookingModel->setAttributes(array('id' => $_POST['id']))->modify(array(
					'status' => 'cancelled'
				));
				
				$pjBookingExtraModel = pjBookingExtraModel::factory();
				$extra_arr = $pjBookingExtraModel->select("t1.*, t2.content as name, t3.price")
												 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
												 ->join('pjExtra', "t3.id = t1.extra_id")
												 ->where('t1.booking_id',$booking_arr['id'])
												 ->findAll()->getData();
				$booking_arr['extra_arr'] = $extra_arr;
									  
				pjFront::pjActionConfirmSend($this->option_arr, $booking_arr, PJ_SALT, 'cancel');
				
				pjUtil::redirect($this->option_arr['o_cancel_booking_page']);
			}
		} else {
			if (isset($_GET['hash']) && isset($_GET['id']))
			{
				$booking_arr = $pjBookingModel->select('t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location , t6.content as country_title ')
									  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
									  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
									  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
								      ->join('pjType', "t5.id = t1.type_id ")
								      ->join('pjMultiLang', "t6.foreign_id = t1.c_country AND t6.model = 'pjCountry' AND t6.locale = '".$this->getLocaleId()."' AND t6.field = 'name'", 'left')
									  ->find($_GET['id'])->getData();
				if (count($booking_arr) == 0)
				{
					$this->tpl['status'] = 2;
				} else {
					if ($booking_arr['status'] == 'cancelled')
					{
						$this->tpl['status'] = 4;
					}else if ($booking_arr['status'] == 'collected'){
						$this->tpl['status'] = 5;
					}else if ($booking_arr['status'] == 'completed'){
						$this->tpl['status'] = 6;
					} else {
						$hash = sha1($booking_arr['id'] . $booking_arr['created'] . PJ_SALT);
						
						if ($_GET['hash'] != $hash)
						{
							$this->tpl['status'] = 3;
						} else {
							
							$extra_arr = pjBookingExtraModel::factory()->select("t1.*, t2.content as name, t3.price")
															 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
															 ->join('pjExtra', "t3.id = t1.extra_id")
															 ->where('t1.booking_id',$booking_arr['id'])
															 ->findAll()->getData();
							$booking_arr['extra_arr'] = $extra_arr;
							$this->tpl['arr'] = $booking_arr;
						}
					}
				}
			} elseif (!isset($_GET['err'])) {
				$this->tpl['status'] = 1;
			}
		}
		
	}
	
	function pjActionGetLatLng()
	{
		$_address = $_GET['address'];
		$_address = preg_replace('/\s+/', '+', $_address);
	
		$google_api_key = isset($option_arr['o_google_map_api']) ? (!empty($option_arr['o_google_map_api']) ? '&key=' . $option_arr['o_google_map_api'] : "") : "";
		$gfile = "http://maps.googleapis.com/maps/api/geocode/json?address=$_address" . $google_api_key;
	
		$Http = new pjHttp();
		$response = $Http->request($gfile)->getResponse();
	
		$geoObj = pjAppController::jsonDecode($response);
	
		$data = array();
		$geoArr = (array) $geoObj;
		if ($geoArr['status'] == 'OK')
		{
			$geoArr['results'][0] = (array) $geoArr['results'][0];
			$geoArr['results'][0]['geometry'] = (array) $geoArr['results'][0]['geometry'];
			$geoArr['results'][0]['geometry']['location'] = (array) $geoArr['results'][0]['geometry']['location'];
				
			$data['lat'] = $geoArr['results'][0]['geometry']['location']['lat'];
			$data['lng'] = $geoArr['results'][0]['geometry']['location']['lng'];
		} else {
			$data['lat'] = NULL;
			$data['lng'] = NULL;
		}
	
		if (isset($data['lat']) && !is_array($data['lat']))
		{
			$data['code'] = 200;
		}else{
			$data['code'] = 100;
		}
		pjAppController::jsonResponse($data);
	}
	
	public function pjActionCheckWTime()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$date_from = pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']);
			$date_to = pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']);
				
			$from_ts = strtotime($date_from . " " . $_POST['hour_from'].":".$_POST['minutes_from']);
			$to_ts = strtotime($date_to . " " . $_POST['hour_to'].":".$_POST['minutes_to']);

			if($to_ts < $from_ts)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 109, 'text' => __('front_invalid_period', true)));
			}
			
			$wt_msg = __('wtime_arr', true);
			$min_hour_msg = str_replace("{HOURS}", $this->option_arr['o_min_hour'], __('front_1_v_err_dates', true, false));
			$min_day_msg = str_replace("{DAYS}", $this->option_arr['o_min_hour'], __('front_1_v_err_length', true, false));
			
			$seconds = abs($from_ts - $to_ts);
			$rental_days = floor($seconds / 86400);
			$rental_hours = ceil($seconds / 3600);
			$hours = intval($rental_hours - ($rental_days * 24));
			
			if($this->option_arr['o_booking_periods'] == 'perday')
			{
				$min_day = $this->option_arr['o_min_hour'];
				if($rental_days < $min_day)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 107, 'text' => $min_day_msg));
				}
			}else{
				$min_hour = $this->option_arr['o_min_hour'];
				if($rental_days == 0 && $hours < $min_hour)
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 108, 'text' => $min_hour_msg));
				}
			}
			
			$pickup_id = $_POST['pickup_id'];
			if(isset($_POST['same_location']))
			{
				$return_id = $pickup_id;
			}else{
				$return_id = $_POST['return_id'];
			}
				
			$pjDateModel = pjDateModel::factory();
			$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				
			$pickup_date = $pjDateModel->reset()->getDate($pickup_id, $date_from);
			if(!empty($pickup_date))
			{
				if($pickup_date['is_dayoff'] == 'T')
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => $wt_msg[7]));
				}else if($from_ts < strtotime($pickup_date['date'] . ' ' . $pickup_date['start_time'])){
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => $wt_msg[7]));
				}else if($from_ts > strtotime($pickup_date['date'] . ' ' . $pickup_date['end_time'])){
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => $wt_msg[7]));
				}
			}else{
				$wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime($pickup_id);
				if(!empty($wt_arr))
				{
					$pickup_weekday = strtolower(date('l', $from_ts));
					if($wt_arr[$pickup_weekday . '_dayoff'] == 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => $wt_msg[7]));
					}else if($from_ts < strtotime($date_from . ' ' . $wt_arr[$pickup_weekday . '_from'])){
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => $wt_msg[7]));
					}else if($from_ts > strtotime($date_from . ' ' . $wt_arr[$pickup_weekday . '_to'])){
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => $wt_msg[7]));
					}
				}
			}
				
			$return_date = $pjDateModel->reset()->getDate($return_id, $date_to);
			if(!empty($return_date))
			{
				if($return_date['is_dayoff'] == 'T')
				{
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 106, 'text' => $wt_msg[7]));
				}else if($to_ts < strtotime($return_date['date'] . ' ' . $return_date['start_time'])){
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => $wt_msg[7]));
				}else if($to_ts > strtotime($return_date['date'] . ' ' . $return_date['end_time'])){
					pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => $wt_msg[7]));
				}
			}else{
				$wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime($return_id);
				if(!empty($wt_arr))
				{
					$return_weekday = strtolower(date('l', $to_ts));
					if($wt_arr[$return_weekday . '_dayoff'] == 'T')
					{
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => $wt_msg[7]));
					}else if($to_ts < strtotime($date_to . ' ' . $wt_arr[$return_weekday . '_from'])){
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => $wt_msg[7]));
					}else if($to_ts > strtotime($date_to . ' ' . $wt_arr[$return_weekday . '_to'])){
						pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 105, 'text' => $wt_msg[7]));
					}
				}
			}
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => ''));
		}
		exit;
	}
	
	public function isXHR()
	{
		return parent::isXHR() || isset($_SERVER['HTTP_ORIGIN']);
	}
	
	static protected function allowCORS()
	{
		$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*';
		header('P3P: CP="ALL DSP COR CUR ADM TAI OUR IND COM NAV INT"');
		header("Access-Control-Allow-Origin: $origin");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With");
	}
}
?>