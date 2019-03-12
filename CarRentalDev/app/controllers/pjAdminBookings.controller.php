<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionCheckPickup()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pickup_id = $_GET['pickup_id'];
			$result = 'true';
			if(!empty($pickup_id))
			{
				$from_arr = pjUtil::convertDateTime($_GET['date_from'], $this->option_arr['o_date_format']);
				$pjDateModel = pjDateModel::factory();
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
					
				$from_ts = $from_arr['ts'];
				$pickup_date = $pjDateModel->reset()->getDate($pickup_id, $from_arr['iso_date']);
				if(!empty($pickup_date))
				{
					if($pickup_date['is_dayoff'] == 'T')
					{
						$result = 'false';
					}else if($from_ts < strtotime($pickup_date['date'] . ' ' . $pickup_date['start_time'])){
						$result = 'false';
					}else if($from_ts > strtotime($pickup_date['date'] . ' ' . $pickup_date['end_time'])){
						$result = 'false';
					}
				}else{
					$wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime($pickup_id);
					if(!empty($wt_arr))
					{
						$pickup_weekday = strtolower(date('l', $from_ts));
						if($wt_arr[$pickup_weekday . '_dayoff'] == 'T')
						{
							$result = 'false';
						}else if($from_ts < strtotime($from_arr['iso_date'] . ' ' . $wt_arr[$pickup_weekday . '_from'])){
							$result = 'false';
						}else if($from_ts > strtotime($from_arr['iso_date'] . ' ' . $wt_arr[$pickup_weekday . '_to'])){
							$result = 'false';
						}
					}
				}
			}
			echo $result;
		}
		exit;
	}
	public function pjActionCheckReturn()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$return_id = $_GET['return_id'];
	
			$result = 'true';
			if($return_id != '')
			{
				$to_arr = pjUtil::convertDateTime($_GET['date_to'], $this->option_arr['o_date_format']);
	
				$pjDateModel = pjDateModel::factory();
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
					
				$to_ts = $to_arr['ts'];
				$return_date = $pjDateModel->reset()->getDate($return_id, $to_arr['iso_date']);
				if(!empty($return_date))
				{
					if($return_date['is_dayoff'] == 'T')
					{
						$result = 'false';
					}else if($to_ts < strtotime($return_date['date'] . ' ' . $return_date['start_time'])){
						$result = 'false';
					}else if($to_ts > strtotime($return_date['date'] . ' ' . $return_date['end_time'])){
						$result = 'false';
					}
				}else{
					$wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime($return_id);
					if(!empty($wt_arr))
					{
						$return_weekday = strtolower(date('l', $to_ts));
						if($wt_arr[$return_weekday . '_dayoff'] == 'T')
						{
							$result = 'false';
						}else if($to_ts < strtotime($to_arr['iso_date'] . ' ' . $wt_arr[$return_weekday . '_from'])){
							$result = 'false';
						}else if($to_ts > strtotime($to_arr['iso_date'] . ' ' . $wt_arr[$return_weekday . '_to'])){
							$result = 'false';
						}
					}
				}
			}
			echo $result;
		}
		exit;
	}
	public function pjActionGetBookings()
	{
		$this->checkLogin();
		
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$column = 'created';
			$direction = 'DESC';
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed', 'pending','cancelled', 'collected', 'completed')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			if(isset($_GET['filter']) && in_array($_GET['filter'], array('p_today', 'p_tomorrow','r_today', 'r_tomorrow')))
			{
				switch ($_GET['filter']) {
					case 'p_today':
						$pjBookingModel->where("DATE(t1.from)='".date('Y-m-d')."' AND t1.status='confirmed'");
					;
					break;
					case 'p_tomorrow':
						$pjBookingModel->where("DATE(t1.from)='".date('Y-m-d', time() + 86400)."' AND t1.status='confirmed'");
					;
					break;
					case 'r_today':
						$pjBookingModel->where("DATE(t1.to)='".date('Y-m-d')."' AND t1.status='collected'");
					;
					break;
					case 'r_tomorrow':
						$pjBookingModel->where("DATE(t1.to)='".date('Y-m-d', time() + +86400)."' AND t1.status='collected'");
					;
					break;
				}
			}
			if (isset($_GET['booking_id']) && !empty($_GET['booking_id']))
			{
				$pjBookingModel->where("t1.booking_id LIKE '%".$_GET['booking_id']."%'");
			}
			if (isset($_GET['car_id']) && (int) $_GET['car_id'] > 0)
			{
				$pjBookingModel->where('t1.car_id', $_GET['car_id']);
			}
			
			if (isset($_GET['type_id']) && (int) $_GET['type_id'] > 0)
			{
				$pjBookingModel->where('t1.type_id', $_GET['type_id']);
			}
			
			if (isset($_GET['pickup_from']) && !empty($_GET['pickup_from']) && isset($_GET['pickup_to']) && !empty($_GET['pickup_to']))
			{
				$pjBookingModel->where(sprintf("((`from` BETWEEN '%1\$s' AND '%2\$s') OR (`from` BETWEEN '%1\$s' AND '%2\$s'))",
					pjUtil::formatDate($_GET['pickup_from'], $this->option_arr['o_date_format']),
					pjUtil::formatDate($_GET['pickup_to'], $this->option_arr['o_date_format'])
				));
			} else {
				if (isset($_GET['pickup_from']) && !empty($_GET['pickup_from']))
				{
					$pjBookingModel->where('t1.from >=', pjUtil::formatDate($_GET['pickup_from'], $this->option_arr['o_date_format']));
				}
				if (isset($_GET['pickup_to']) && !empty($_GET['pickup_to']))
				{
					$pjBookingModel->where('t1.from <=', pjUtil::formatDate($_GET['pickup_to'], $this->option_arr['o_date_format']));
				}
			}
			
			if (isset($_GET['return_from']) && !empty($_GET['return_from']) && isset($_GET['return_to']) && !empty($_GET['return_to']))
			{
				$pjBookingModel->where(sprintf("((`to` BETWEEN '%1\$s' AND '%2\$s') OR (`to` BETWEEN '%1\$s' AND '%2\$s'))",
					pjUtil::formatDate($_GET['return_from'], $this->option_arr['o_date_format']),
					pjUtil::formatDate($_GET['return_from'], $this->option_arr['o_date_format'])
				));
			} else {
				if (isset($_GET['return_from']) && !empty($_GET['return_from']))
				{
					$pjBookingModel->where('t1.to >=', pjUtil::formatDate($_GET['return_from'], $this->option_arr['o_date_format']));
				}
				if (isset($_GET['return_to']) && !empty($_GET['return_to']))
				{
					$pjBookingModel->where('t1.to <=', pjUtil::formatDate($_GET['return_to'], $this->option_arr['o_date_format']));
				}
			}
			
			
			if (isset($_GET['pickup_id']) && (int) $_GET['pickup_id'] > 0)
			{
				$pjBookingModel->where('t1.pickup_id', $_GET['pickup_id']);
			}
			
			if (isset($_GET['return_id']) && (int) $_GET['return_id'] > 0)
			{
				$pjBookingModel->where('t1.return_id', $_GET['return_id']);
			}
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				
				$q = pjObject::escapeString($_GET['q']);
				$q = str_replace(array('%', '_'), array('\%', '\_'), $q);
				$pjBookingModel->where("(t1.c_name LIKE '%$q%' OR t1.c_email LIKE '%$q%' OR t1.c_phone LIKE '%$q%')");
			}
			
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}
			
			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}
			$data = $pjBookingModel->join('pjCar', 't2.id=t1.car_id')
							->join('pjMultiLang', "t3.foreign_id = t2.id AND t3.model = 'pjCar' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'model'", 'left')
							->join('pjType', "t4.id = t1.type_id", 'left')
							->join('pjMultiLang', "t5.foreign_id = t4.id AND t5.model = 'pjType' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'name'", 'left')
							->join('pjMultiLang', "t6.foreign_id = t1.pickup_id AND t6.model = 'pjLocation' AND t6.locale = '".$this->getLocaleId()."' AND t6.field = 'name'", 'left')
							->join('pjMultiLang', "t7.foreign_id = t1.return_id AND t7.model = 'pjLocation' AND t7.locale = '".$this->getLocaleId()."' AND t7.field = 'name'", 'left')
							->join('pjMultiLang', "t8.model='pjCar' AND t8.foreign_id=t1.car_id AND t8.field='make' AND t8.locale='".$this->getLocaleId()."'", 'left')
							->join('pjMultiLang', "t9.model='pjCar' AND t9.foreign_id=t1.car_id AND t9.field='model' AND t9.locale='".$this->getLocaleId()."'", 'left')
							->select("t1.id, t1.type_id,t1.car_id,t1.from, t1.to, t1.status, t1.total_price , t1.c_name, t1.c_phone, t3.content as model , t2.registration_number, t5.content as type, t6.content as pickup_location, t7.content as return_location, t8.content as make, t9.content as model")
							->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();
				
			foreach ($data as $key => $val){
				$data[$key]['pick_drop'] = date($this->option_arr['o_datetime_format'], strtotime($val['from'])) . ' ' . __('booking_at', true, false) . ' ' . $val['pickup_location'] . "<br/>";
				$data[$key]['pick_drop'] .= date($this->option_arr['o_datetime_format'], strtotime($val['to'])) . ' ' . __('booking_at', true, false) . ' ' . $val['return_location'];
				$data[$key]['car_info'] = $val['registration_number'] . ',<br/>' . $val['make'] . " " . $val['model'];
				$data[$key]['client'] = pjSanitize::html($val['c_name']) . '<br/>' . pjSanitize::html($val['c_phone']);
				$data[$key]['total_price'] = pjUtil::formatCurrencySign($val['total_price'],$this->option_arr['o_currency']);
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
			exit();
		}
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
												->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
												->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
			$this->set('type_arr', $type_arr);
			
			$pickup_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('pickup_arr', pjSanitize::clean($pickup_arr));
			
			$return_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('return_arr', pjSanitize::clean($return_arr));
				
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjBookingModel::factory()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
		}
		exit;
	}
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			
			if (isset($_POST['action_create']))
			{
				$data = array();
				$data['uuid'] = time();
				$data['booking_id'] = $this->getBookingID();
				$data['ip'] = pjUtil::getClientIp();
				
				list($_start_date, $_start_time) = explode(" ",$_POST['date_from']);
				list($_end_date, $_end_time) = explode(" ",$_POST['date_to']);
												
				$date_from = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format'])." ".$_start_time;
				$date_to = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format'])." ".$_end_time;
								
				$data['from'] = $date_from;
				$data['to'] = $date_to;
				
				if(!empty($_POST['pickup_date']))
				{
					list($_pickup_date, $_pickup_time) = explode(" ",$_POST['pickup_date']);
					$pickup_date = pjUtil::formatDate($_pickup_date, $this->option_arr['o_date_format'])." ".$_pickup_time;
					$data['pickup_date'] = $pickup_date;
				}else{
					$data['pickup_date'] = ':NULL';
				}
				
				$pjBookingModel = pjBookingModel::factory();
				$post = array_merge($_POST, $data);

				$insert_id = $pjBookingModel->setAttributes($post)->insert()->getInsertId();
				if ($insert_id !== false && (int) $insert_id > 0)
				{
					# EXTRAS
					if (isset($_POST['extra_id']) && count($_POST['extra_id']) > 0)
					{
					
						$pjBookingExtraModel = pjBookingExtraModel::factory();
						foreach ($_POST['extra_id'] as $k => $v)
						{
							$pjBookingExtraModel->setAttributes(array(
									'booking_id' => $insert_id,
									'extra_id' => $v,
									'price' => @$_POST['e_price'][$k],
									'quantity' => @$_POST['extra_cnt'][$k]
								))->insert();
								
						}
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR03");
					
				} else {
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR04");
				}
				
			}

			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('type_arr', pjSanitize::clean($type_arr));
			
			
			$pickup_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('pickup_arr', pjSanitize::clean($pickup_arr));
			
			$return_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('return_arr', pjSanitize::clean($return_arr));

			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
			$this->set('country_arr', $country_arr);
				
			# Timepicker
			$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
			$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
			$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
				
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjCarModel = pjCarModel::factory();
			$pjCarTypeModel = pjCarTypeModel::factory();
			$pjTypeExtraModel = pjTypeExtraModel::factory();
			$pjBookingExtraModel = pjBookingExtraModel::factory();

			$booking_arr = $pjBookingModel
				->select(sprintf("t1.*,
					AES_DECRYPT(t1.cc_num, '%1\$s') AS `cc_num`,
					AES_DECRYPT(t1.cc_exp, '%1\$s') AS `cc_exp`,
					AES_DECRYPT(t1.cc_code, '%1\$s') AS `cc_code`, t3.rent_type", PJ_SALT))
				->join('pjCar', 't2.id=t1.car_id')
				->join('pjType', "t3.id = t1.type_id")
				->find($_REQUEST['id'])->getData();

			if (empty($booking_arr) || count($booking_arr) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
			}
			
			$car_arr = $pjCarModel->find($booking_arr['car_id'])->getData();
			
			if (empty($car_arr) || count($car_arr) == 0)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR09");
			}
			
			if (isset($_POST['booking_update']))
			{
				$data = array();
				
				list($_start_date, $_start_time) = explode(" ",$_POST['date_from']);
				list($_end_date, $_end_time) = explode(" ",$_POST['date_to']);
												
				$date_from = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format'])." ".$_start_time;
				$date_to = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format'])." ".$_end_time;
								
				$data['from'] = $date_from;
				$data['to'] = $date_to;
				
				if(!empty($_POST['pickup_date']))
				{
					list($_pickup_date, $_pickup_time) = explode(" ",$_POST['pickup_date']);
					$pickup_date = pjUtil::formatDate($_pickup_date, $this->option_arr['o_date_format'])." ".$_pickup_time;
					$data['pickup_date'] = $pickup_date;
				}else{
					$data['pickup_date'] = ':NULL';
				}
				if(!empty($_POST['actual_dropoff_datetime']))
				{
					list($_actual_dropoff_date, $_actual_dropoff_time) = explode(" ",$_POST['actual_dropoff_datetime']);
					$actual_dropoff_datetime = pjUtil::formatDate($_actual_dropoff_date, $this->option_arr['o_date_format'])." ".$_actual_dropoff_time;
					$data['actual_dropoff_datetime'] = $actual_dropoff_datetime;
				}else{
					$data['actual_dropoff_datetime'] = ':NULL';
				}
									
				$post = array_merge($_POST, $data);
				
				if (!$pjBookingModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionIndex&err=AR02");
				}
				
				$pjBookingModel->reset()->set('id', $_POST['id'])->modify($post);
				
				$pjBookingExtraModel->where('booking_id', $_POST['id'])->eraseAll();
				
				if (isset($_POST['extra_id']) && count($_POST['extra_id']) > 0)
				{
					foreach ($_POST['extra_id'] as $k => $v)
					{
						$be_arr = array(
										'booking_id' => $_POST['id'],
										'extra_id' => $v,
										'price' => @$_POST['e_price'][$k],
										'quantity' => @$_POST['extra_cnt'][$k]
									);
						
						$pjBookingExtraModel->setAttributes($be_arr)->insert();
					}
				}
				
				$pjBookingPaymentModel = pjBookingPaymentModel::factory();
				$pjBookingPaymentModel->where('booking_id', $_POST['id'])->eraseAll();
				
				if (isset($_POST['payment_method']) && count($_POST['payment_method']) > 0)
				{
					foreach ($_POST['payment_method'] as $k => $v)
					{
						if(floatval($_POST['amount'][$k]) > 0)
						{
							$payment_arr = array(
											'booking_id' => $_POST['id'],
											'payment_method' => @$_POST['payment_method'][$k],
											'payment_type' => @$_POST['payment_type'][$k],
											'amount' => @$_POST['amount'][$k],
											'status' => @$_POST['payment_status'][$k]
										);
							
							$pjBookingPaymentModel->reset()->setAttributes($payment_arr)->insert();
						}
					}
				}
								
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminBookings&action=pjActionUpdate&id=".$_POST['id']. "&tab_id=" . $_POST['tab_id']."&err=AR01");
			}else {
				$this->set('arr', $booking_arr);
				$this->set('booking_car_arr', $car_arr);
				
			}
			
			$car_arr = array();
			$extra_price_arr = array();
			if ((int) $booking_arr['type_id'] > 0)
			{
				$car_arr = $pjCarTypeModel->select('t1.*,t4.registration_number, t2.content AS make, t3.content as model')
				->join('pjMultiLang', "t2.model='pjCar' AND t2.foreign_id=t1.car_id AND t2.field='make' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='model' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->join('pjCar', "t4.id = t1.car_id")
				->orderBy('t2.content ASC, t3.content ASC')
				->where('t1.type_id',$booking_arr['type_id'])
				->findAll()->getData()
				;
			}
			$this->set('car_arr', pjSanitize::clean($car_arr));
			
			$extra_arr = $pjTypeExtraModel->select('t1.*, t2.content as name , t3.price, t3.per, t3.count')
				->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.extra_id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjExtra', "t3.id = t1.extra_id")
				->orderBy('t2.content ASC')
				->where('t1.type_id',$booking_arr['type_id'])
				->findAll()->getData();
			
			$per_extra = __('per_extras', true, false);
			foreach($extra_arr as $v)
			{
				$extra_price_arr[$v['extra_id']] = pjUtil::formatCurrencySign($v['price'], $this->option_arr['o_currency']) . ' ' . $per_extra[$v['per']];
			}
			$this->set('extra_arr', pjSanitize::clean($extra_arr));
			$this->set('extra_price_arr', $extra_price_arr);
			$be_arr = $pjBookingExtraModel->where('booking_id', $booking_arr['id'])->findAll()->getDataPair('extra_id', 'extra_id');
			$this->set('be_arr', $be_arr);
			
			$be_quantity_arr = array();
			$extended_extra_arr = array();
			$be_quantity = $pjBookingExtraModel
							->where('t1.booking_id',$booking_arr['id'])
							->findAll()->getData();
			
			foreach ($be_quantity as $key => $val){
				$be_quantity_arr[$val['extra_id']] = $val['quantity'];
				$extended_extra_arr[$val['extra_id']] = $val;
			}
			
			$this->set('be_quantity_arr', $be_quantity_arr);
			$this->set('extended_extra_arr', $extended_extra_arr);
					
			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('type_arr', pjSanitize::clean($type_arr));
			
			$pickup_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('pickup_arr', pjSanitize::clean($pickup_arr));
			
			$return_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('return_arr', pjSanitize::clean($return_arr));
				
			$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
			$this->set('country_arr', $country_arr);

			$payment_arr = pjBookingPaymentModel::factory()->where('t1.booking_id',$booking_arr['id'])->findAll()->getData();
			$this->set('payment_arr', $payment_arr);
			
			$collected = 0;
			$security_returned = 0;
			foreach($payment_arr as $v)
			{
				if($v['payment_type'] != 'securityreturned' && $v['status'] == 'paid')
				{
					$collected += $v['amount'];
				}
				if($v['payment_type'] == 'securityreturned' && $v['status'] == 'paid'){
					$security_returned += $v['amount'];
				}
			}
			$this->set('collected', $collected - $security_returned);
			
			# Timepicker
			$this->appendJs('jquery-ui-sliderAccess.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
			$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
			$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('jquery.noty.packaged.min.js', PJ_THIRD_PARTY_PATH . 'noty/packaged/');
			$this->appendJs('pjAdminBookings.js');
		}
	}
	
	public function pjActionGetPrices()
	{
		$this->setAjax(true);
		
		$pjExtraModel = pjExtraModel::factory();
		$pjPriceModel = pjPriceModel::factory();
		$pjTypeModel = pjTypeModel::factory();
		
		
		list($_start_date, $_start_time) = explode(" ",$_POST['date_from']);
		list($_end_date, $_end_time) = explode(" ",$_POST['date_to']);
		
		$date_from = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format'])." ".$_start_time;
		$date_to = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format'])." ".$_end_time;

		$seconds = abs(strtotime($date_to) - strtotime($date_from));
		$rental_days = floor($seconds / 86400);
		$rental_hours = $seconds / 3600;
		
		$hours = intval($rental_hours - ($rental_days * 24));
		
		$price = 0;
    	$extra_price = 0;
    	$price_per_day = 0;
    	$price_per_hour = 0;
    	$price_per_day_detail = '';
    	$price_per_hour_detail = '';
    	$car_rental_fee = 0;
    	$car_rental_fee_detail = '';
    	$sub_total = 0;
    	$total_price = 0;
    	$required_deposit = 0;
    	$insurance_detail = '';
    	$tax_detail = '';
    	$required_deposit_detail = '';
    	
    	$car_rental_fee_arr = array();
				
		$e_arr = array();
		$extra_arr = array();
		$extra_qty_arr = array();
		if(isset($_POST['extra_id'])){
			foreach ($_POST['extra_id'] as $key => $extra_id){
				if((int) $extra_id){
					$e_arr[] = $extra_id;
					$extra_qty_arr[$extra_id] = $_POST['extra_cnt'][$key];
				}
				
			}
		}
		if(count($e_arr) > 0){
			$extra_arr = $pjExtraModel->where('t1.status', 'T')
									  ->where('t1.id  IN ('.implode(',',$e_arr).')')
									  ->findAll()
									  ->getData();
		}

		$real_rental_days = pjAppController::getRealRentalDays($date_from, $date_to, $this->option_arr);
		foreach ($extra_arr as $key => $val){
			switch ($val['per'])
			{
				case 'day':
					$extra_price +=  $val['price'] * $real_rental_days * $extra_qty_arr[$val['id']];
					break;
				case 'booking':
					$extra_price +=  $val['price'] * $extra_qty_arr[$val['id']];
					break;
			}
		}
		
		$type_arr = $pjTypeModel->find($_POST['type_id'])->getData();

		$price_arr = pjAppController::getPrices($date_from, $date_to, $type_arr, $this->option_arr);
		if($price_arr['price'] == 0)
		{
			$price_arr = pjAppController::getDefaultPrices($date_from, $date_to, $type_arr, $this->option_arr);
		}
		$car_rental_fee = $price_arr['price'];
		$price_per_day = $price_arr['price_per_day'];
    	$price_per_hour = $price_arr['price_per_hour'];
    	$price_per_day_detail = $price_arr['price_per_day_detail'];
    	$price_per_hour_detail = $price_arr['price_per_hour_detail'];
		
    	$price = $car_rental_fee + $extra_price;
    	
    	$insurance_types = __('insurance_type_arr', true, false);
		$insurance = $this->option_arr['o_insurance_payment'];
		$insurance_detail = pjUtil::formatCurrencySign($this->option_arr['o_insurance_payment'], $this->option_arr['o_currency']) . ' ' . strtolower($insurance_types['perbooking']);
    	if($this->option_arr['o_insurance_payment_type'] == 'percent')
		{
			$insurance = ($price * $this->option_arr['o_insurance_payment']) / 100;
			$insurance_detail = $this->option_arr['o_insurance_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($price, $this->option_arr['o_currency']);
		}elseif($this->option_arr['o_insurance_payment_type'] == 'perday'){
			$_rental_days = $rental_days;
			if($hours > 0)
			{
				if($this->option_arr['o_new_day_per_day'] == 0 && $this->option_arr['o_booking_periods'] == 'perday')
				{
					$_rental_days++;
				}
				if($this->option_arr['o_new_day_per_day'] > 0 && $hours > $this->option_arr['o_new_day_per_day']){
					$this->option_arr++;
				}
			}
			$insurance = $_rental_days * $this->option_arr['o_insurance_payment'];
			$insurance_detail = pjUtil::formatCurrencySign($this->option_arr['o_insurance_payment'], $this->option_arr['o_currency']) . ' ' . strtolower($insurance_types['perday']);
		}
    	$sub_total = $car_rental_fee + $extra_price + $insurance;
		
		$tax =  $this->option_arr['o_tax_payment'];
    	if($this->option_arr['o_tax_type'] == 1)
    	{
    		$tax = ($sub_total * $this->option_arr['o_tax_payment']) / 100;
    		$tax_detail = $this->option_arr['o_tax_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($sub_total, $this->option_arr['o_currency']);
    	}
    	$total_price = $sub_total + $tax;
    	    	
    	$security  = $this->option_arr['o_security_payment'];
		
		switch ($this->option_arr['o_deposit_type'])
		{
			case 'percent':
				$required_deposit = ($total_price * $this->option_arr['o_deposit_payment']) / 100;
				$required_deposit_detail = $this->option_arr['o_deposit_payment'] . '% ' . __('lblOf', true, false) . ' ' . pjUtil::formatCurrencySign($total_price, $this->option_arr['o_currency']);
				break;
			case 'amount':
				$required_deposit = $this->option_arr['o_deposit_payment'];
				$required_deposit_detail = '';
				break;
		}
		
		$total_amount_due = $total_price;
		if($_POST['status'] == 'confirmed'){
			$total_amount_due = $total_price - $required_deposit;
		}
		
		$price_per_day = number_format($price_per_day, 2, '.', '');
		$price_per_hour = number_format($price_per_hour, 2, '.', '');
		$car_rental_fee = number_format($car_rental_fee, 2, '.', '');
		$extra_price = number_format($extra_price, 2, '.', '');
		$insurance = number_format($insurance, 2, '.', '');
		$sub_total = number_format($sub_total, 2, '.', '');
		$tax = number_format($tax, 2, '.', '');
		$total_price = number_format($total_price, 2, '.', '');
		$required_deposit = number_format($required_deposit, 2, '.', '');
		$total_amount_due = number_format($total_amount_due, 2, '.', '');
		
		$price_per_day_label = pjUtil::formatCurrencySign($price_per_day, $this->option_arr['o_currency']);
		$price_per_hour_label = pjUtil::formatCurrencySign($price_per_hour, $this->option_arr['o_currency']);
		$car_rental_fee_label = pjUtil::formatCurrencySign($car_rental_fee, $this->option_arr['o_currency']);
		$extra_price_label = pjUtil::formatCurrencySign($extra_price, $this->option_arr['o_currency']);
		$insurance_label = pjUtil::formatCurrencySign($insurance, $this->option_arr['o_currency']);
		$sub_total_label = pjUtil::formatCurrencySign($sub_total, $this->option_arr['o_currency']);
		$tax_label = pjUtil::formatCurrencySign($tax, $this->option_arr['o_currency']);
		$total_price_label = pjUtil::formatCurrencySign($total_price, $this->option_arr['o_currency']);
		$required_deposit_label = pjUtil::formatCurrencySign($required_deposit, $this->option_arr['o_currency']);
		$total_amount_due_label = pjUtil::formatCurrencySign($total_amount_due, $this->option_arr['o_currency']);
		
		if($price_per_day > 0)
		{
			$car_rental_fee_arr[] = $price_per_day_label;
		}
		if($price_per_hour > 0)
		{
			$car_rental_fee_arr[] = $price_per_hour_label;
		}
		$car_rental_fee_detail = join(" + ", $car_rental_fee_arr);
		
		$rental_time = '';
		if($rental_days > 0 || $hours > 0){
			if($rental_days > 0){
				$rental_time .= $rental_days . ' ' . ($rental_days > 1 ? __('plural_day', true, false) : __('singular_day', true, false));
			}
			if($hours > 0){
				$rental_time .= ' ' . $hours . ' ' . ($hours > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false));
			}
		}
		
		pjAppController::jsonResponse(compact('rental_time', 'rental_days', 'hours',
												'price_per_day', 'price_per_hour', 'price_per_day_detail', 'price_per_hour_detail',
												'car_rental_fee', 'extra_price', 'insurance', 'sub_total', 'tax',
												'total_price', 'required_deposit', 'total_amount_due',
												'price_per_day_label', 'price_per_hour_label', 'car_rental_fee_label',
												'extra_price_label', 'insurance_label', 'sub_total_label', 'tax_label',
												'total_price_label', 'required_deposit_label', 'total_amount_due_label',
												'car_rental_fee_detail', 'insurance_detail', 'tax_detail', 'required_deposit_detail'));
		
								
	}
	
	public function pjActionExtraHoursUsage()
	{
		$this->setAjax(true);
		
		$extra_hours_usage = 0;
		
		list($_start_date, $_start_time) = explode(" ", $_POST['date_to']);
		list($_end_date, $_end_time) = explode(" ", $_POST['actual_dropoff_datetime']);
		
		$date_from = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format'])." ".$_start_time;
		$date_to = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format'])." ".$_end_time;
		
		$seconds = strtotime($date_to) - strtotime($date_from);
		if($seconds > 0)
		{
			$extra_hours_usage = ceil($seconds / 3600);
		}
		$extra_hours_usage = $extra_hours_usage > 0 ? $extra_hours_usage . ' ' . ($extra_hours_usage > 1 ? __('plural_hour', true, false) : __('singular_hour', true, false)) : __('booking_no', true, false);
		
		pjAppController::jsonResponse(compact('extra_hours_usage'));
	}
	
	public function pjActionExtraMileageCharge()
	{
		$this->setAjax(true);
		
		$extra_mileage_charge = 0;
		
		$type_arr = pjTypeModel::factory()->find($_POST['type_id'])->getData();
		
		$rental_days = $_POST['rental_days'];
		$daily_mileage_limit = floatval($type_arr['default_distance']);
		$price_for_extra_mileage = floatval($type_arr['extra_price']);
		
		$actual_mileage = 0;
		$extra_mileage_charge = 0;
		if(!empty($_POST['dropoff_mileage']) && !empty($_POST['pickup_mileage']))
		{
			$actual_mileage = $_POST['dropoff_mileage'] - $_POST['pickup_mileage'];
		}
		if($actual_mileage > 0)
		{
			$_em_charge = $actual_mileage - ($rental_days * $daily_mileage_limit);
			if($_em_charge > 0)
			{
				$extra_mileage_charge = $_em_charge * $price_for_extra_mileage;
			}
		}
		$_em_charge = $_em_charge . $this->option_arr['o_unit'];
		$extra_mileage_charge = $extra_mileage_charge > 0 ? $_em_charge . ' x ' . pjUtil::formatCurrencySign(number_format($price_for_extra_mileage, 2, '.', ''), $this->option_arr['o_currency']) . ' = ' .pjUtil::formatCurrencySign(number_format($extra_mileage_charge, 2, '.', ''), $this->option_arr['o_currency']) : __('booking_no', true, false);
		
		pjAppController::jsonResponse(compact('extra_mileage_charge', 'actual_mileage', 'rental_days', 'daily_mileage_limit', 'price_for_extra_mileage', '_em_charge'));
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			$pjBookingModel = pjBookingModel::factory();
			$arr = $pjBookingModel->find($_GET['id'])->getData();
			if ($pjBookingModel->reset()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingExtraModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				$pjBookingModel = pjBookingModel::factory();
				
				$pjBookingModel->reset()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingExtraModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetCars()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = array();
			if ((int) $_GET['type_id'] > 0)
			{
				$pjCarModel = pjCarModel::factory();
				$pjCarTypeModel = pjCarTypeModel::factory();
				
				$arr = $pjCarTypeModel
				->select('t2.id as car_id, t2.registration_number, t3.content AS make, t4.content AS model')
				->join('pjCar', "t1.car_id = t2.id", 'left')
				->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t2.id AND t3.field='make' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t4.model='pjCar' AND t4.foreign_id=t2.id AND t4.field='model' AND t4.locale='".$this->getLocaleId()."'", 'left')
				->where('type_id', $_GET['type_id'])
				->where('t2.status', 'T')
				->orderBy('make ASC')
				->findAll()->getData();
			
				$arr = pjSanitize::clean($arr);
			}
			
			$this->set('car_arr', $arr);
		}
	}
	
    public function pjActionGetExtras()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = array();
			if ((int) $_GET['type_id'] > 0)
			{
				$pjTypeExtraModel = pjTypeExtraModel::factory();
				
				$arr = $pjTypeExtraModel
				->select('t2.id as extra_id, t2.price , t2.per, t2.count , t3.content AS name')
				->join('pjExtra', "t1.extra_id = t2.id", 'left')
				->join('pjMultiLang', "t3.model='pjExtra' AND t3.foreign_id=t2.id AND t3.field='name' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->where('type_id', $_GET['type_id'])
				->where('t2.status', 'T')
				->orderBy('name ASC')
				->findAll()->getData();
			
				$arr = pjSanitize::clean($arr);
				
			}
			$this->set('extra_arr', $arr);
		}
	}
	
	public function pjActionCheckAvailability(){
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			pjAppController::jsonResponse($this->pjActionGetAvailability($_POST));
		}
		exit;
	}
	
	public function pjActionGetAvailability($data, $format=true)
	{
		$response = array('code' => 100);
		
		if (isset($data['date_from']) && isset($data['date_to']) && !empty($data['date_from']) && !empty($data['date_to']))
		{
			list($_start_date, $_start_time) = explode(" ",$data['date_from']);
			list($_end_date, $_end_time) = explode(" ",$data['date_to']);
			
			$date_from = pjUtil::formatDate($_start_date, $this->option_arr['o_date_format'])." ".$_start_time;
			$date_to = pjUtil::formatDate($_end_date, $this->option_arr['o_date_format'])." ".$_end_time;
			
			$date_from_ts = strtotime($date_from);
			$date_to_ts = strtotime($date_to);
			
		
			if($date_to_ts <= $date_from_ts){
				$response = array('code' => 100);
			}else{
				if (isset($data['type_id']) && (int) $data['type_id'] > 0 && isset($data['car_id']) && (int) $data['car_id'] > 0 )
				{
					$type_arr = pjTypeModel::factory()->find($data['type_id'])->getData();
					
					$min_hour = $this->option_arr['o_min_hour'];
					if($this->option_arr['o_booking_periods'] == 'perday'){
						$min_hour = $this->option_arr['o_min_hour'] * 24;
					}
					if( round($date_to_ts - $date_from_ts)/3600 < $min_hour){
						$response['code'] = 100;
						return $response;
					}
					
					$current_datetime = date('Y-m-d H:i:s', time() - ($this->option_arr['o_booking_pending'] * 3600));
					$pjBookingModel = pjBookingModel::factory()
						->where('t1.type_id', $data['type_id'])
						->where('t1.car_id', $data['car_id'])
						->where("(`status` = 'confirmed' OR `status` = 'collected' OR (`status` = 'pending' AND `created` >= '$current_datetime'))")
						->where(sprintf("(((`from` BETWEEN '%1\$s' AND '%2\$s') OR (`to` BETWEEN '%1\$s' AND '%2\$s')) OR (`from` < '%1\$s' AND `to` > '%2\$s') OR (`from` > '%1\$s' AND `to` < '%2\$s'))",$date_from, $date_to))
					;
					if (isset($data['id']) && (int) $data['id'])
					{
						$pjBookingModel->where('t1.id !=', $data['id']);
					}
					
					$booking_cnt = $pjBookingModel->findCount()->getData();
					
					if ($booking_cnt == 0)
					{
						$response['code'] = 200;
					}
				}else{
					$response['code'] = 300;
				}
			}
		}
		return $response;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			if (!in_array($_POST['column'], $pjBookingModel->getI18n()))
			{
				$pjBookingModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCar');
			}
		}
		exit;
	}
	
	
	public function pjActionGetCarMileage(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = pjCarModel::factory()->find($_GET['car_id'])->getData();
			echo $arr['mileage'];
			exit;
		}
	}
	
	public function pjActionGetCarMileageMsg(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$arr = pjCarModel::factory()
				->select('t1.*, t2.content AS make, t3.content AS model')
				->join('pjMultiLang', "t2.model='pjCar' AND t2.foreign_id=t1.id AND t2.field='make' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.id AND t3.field='model' AND t3.locale='".$this->getLocaleId()."'", 'left')
				->find($_GET['car_id'])->getData();
			
			$arr = pjSanitize::clean($arr);
				
			$this->set('arr', $arr);
		}
	}
	
	public function pjActionUpdateCarMileague(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjCarModel::factory()->where('id', $_POST['car_id'])->limit(1)->modifyAll($_POST);
		}
	}
	
	public function pjActionFormatBalance(){
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			echo pjUtil::formatCurrencySign($_GET['balance'], $this->option_arr['o_currency']);
			exit;
		}
	}
	
	public function pjActionDeletePayment(){
		$this->setAjax(true);
		
		$this->checkLogin();
	
		if ($this->isXHR())
		{
			if ($this->isAdmin())
			{
				$response = array('code' => 100);
				if (isset($_POST['id']))
				{
					if (pjBookingPaymentModel::factory()->reset()->setAttributes(array('id' => $_POST['id']))->erase()->getAffectedRows() == 1)
					{
						$response['code'] = 200;
					}
				}
				pjAppController::jsonResponse($response);
					
				exit;
			}
		}
	}
	
	public function pjActionReminderEmail()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['send_email']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['from']) &&
				!empty($_POST['subject']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass']);
				}
				
				
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($_POST['subject'])
					->send(pjUtil::textToHtml($_POST['message']));
				
					
				if (isset($r) && $r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$booking_arr = pjBookingModel::factory()->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
											  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
											  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
											  ->find($_GET['id'])->getData();
				
				if (count($booking_arr) > 0)
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
					
					$extra_arr = pjBookingExtraModel::factory()->select("t1.*, t2.content as name, t3.price")
													 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
													 ->join('pjExtra', "t3.id = t1.extra_id")
													 ->where('t1.booking_id',$booking_arr['id'])
													 ->findAll()->getData();
					$booking_arr['extra_arr'] = $extra_arr;
					$admin_email = $this->getAdminEmail();
					
					$tokens = pjAppController::getTokens($booking_arr, $this->option_arr, PJ_SALT, $this->getLocaleId());
					$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_email_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
					$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_email_confirmation_subject')
											 ->limit(0, 1)
											 ->findAll()->getData();
					
					$subject_client = str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']);
					$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
					$this->set('arr', array(
						'id' => $_GET['id'],
						'client_email' => $booking_arr['c_email'],
						'from' => $admin_email,
						'message' => $message_client,
						'subject' => $subject_client
					));
				}
				
			} else {
				exit;
			}
		}
	}
	
	public function pjActionReminderSms()
	{
		$this->setAjax(true);
	
		if ($this->isXHR() && $this->isLoged())
		{
			if (isset($_POST['send_sms']) && isset($_POST['to']) && !empty($_POST['to']) && !empty($_POST['message']) && !empty($_POST['id']))
			{
				$params = array(
					'text' => $_POST['message'],
					'type' => 'unicode',
					'key' => md5($this->option_arr['private_key'] . PJ_SALT)
				);
				
				
				$params['number'] = $_POST['to'];
				$result = $this->requestAction(array('controller' => 'pjSms', 'action' => 'pjActionSend', 'params' => $params), array('return'));
			
				if (isset($result) && (int) $result === 1)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'SMS has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'SMS failed to send.'));
			}
			
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$booking_arr = pjBookingModel::factory()->select(sprintf("t1.*, t2.content as type, t3.content as pickup_location , t4.content as return_location, AES_DECRYPT(t1.cc_num, '%s') AS `cc_num`, AES_DECRYPT(t1.cc_exp, '%s') AS `cc_exp`, AES_DECRYPT(t1.cc_code, '%s') AS `cc_code`", PJ_SALT, PJ_SALT, PJ_SALT))
											  ->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											  ->join('pjMultiLang', "t3.foreign_id = t1.pickup_id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'name'", 'left')
											  ->join('pjMultiLang', "t4.foreign_id = t1.return_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left')
											  ->find($_GET['id'])->getData();

				if (!empty($booking_arr))
				{
					$pjMultiLangModel = pjMultiLangModel::factory();
					
					$extra_arr = pjBookingExtraModel::factory()->select("t1.*, t2.content as name, t3.price")
													 ->join('pjMultiLang', "t2.foreign_id = t1.extra_id AND t2.model = 'pjExtra' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
													 ->join('pjExtra', "t3.id = t1.extra_id")
													 ->where('t1.booking_id',$booking_arr['id'])
													 ->findAll()->getData();
					$booking_arr['extra_arr'] = $extra_arr;
					
					$tokens = pjAppController::getTokens($booking_arr, $this->option_arr, PJ_SALT, $this->getLocaleId());
					$lang_message = $pjMultiLangModel->reset()->select('t1.*')
											 ->where('t1.model','pjOption')
											 ->where('t1.locale', $this->getLocaleId())
											 ->where('t1.field', 'o_sms_confirmation_message')
											 ->limit(0, 1)
											 ->findAll()->getData();
					$message_client = str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']);
					
					$this->set('arr', array(
						'id' => $_GET['id'],
						'client_phone' => pjUtil::formatPhone($booking_arr['c_phone']),
						'message' => $message_client
					));
				}
			} else {
				exit;
			}
		}
	}
}
?>