<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminCars extends pjAdmin
{
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_create']))
			{
				$pjCarModel = pjCarModel::factory();
				if (!$pjCarModel->validates($_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCars&action=pjActionIndex&err=AE04");
				}

				$id = $pjCarModel->setAttributes($_POST)->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AC03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjCar', 'data');
					}
					
					# TYPES
					$pjCarTypeModel = new pjCarTypeModel();
					if (isset($_POST['type_id']) && is_array($_POST['type_id']) && count($_POST['type_id']) > 0)
					{
						$pjCarTypeModel->begin();
						foreach ($_POST['type_id'] as $type_id)
						{
							$pjCarTypeModel->reset()->setAttributes(array(
								'car_id' => $id,
								'type_id' => $type_id
							))->insert();
						}
						$pjCarTypeModel->commit();
					}
					
				} else {
					$err = 'AC04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminCars&action=pjActionIndex&err=$err");
			} else {
							
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				
				$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
				$this->set('type_arr', pjSanitize::clean($type_arr));
				
				$location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
				$this->set('location_arr', pjSanitize::clean($location_arr));
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCars.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGet()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCarModel = pjCarModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'model'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCar' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'make'", 'left')
				->join('pjMultiLang', "t4.foreign_id = t1.location_id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'name'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				
				$pjCarModel->where('t2.content LIKE', "%$q%");
				$pjCarModel->orWhere('t3.content LIKE', "%$q%");
				$pjCarModel->orWhere('t4.content LIKE', "%$q%");
				$pjCarModel->orWhere('t1.registration_number LIKE', "%$q%");
			}
				
			$column = 't2.content';
			$direction = 'ASC';
			
			
			if (isset($_GET['type_id']) && (int) $_GET['type_id'] > 0)
			{
				$pjCarModel->where(sprintf(" t1.id IN (SELECT `car_id` FROM `%s` WHERE `type_id` = '%u')", pjCarTypeModel::factory()->getTable(), $_GET['type_id']));
			}
	
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjCarModel->where('t1.status', $_GET['status']);
			}
			
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjCarModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$sub_query = "(SELECT GROUP_CONCAT(TML.content SEPARATOR '<br/>') FROM " . pjCarTypeModel::factory()->getTable() . " AS TCT
							LEFt OUTER JOIN " . pjMultiLangModel::factory()->getTable() . " AS TML ON TML.model='pjType' AND TML.foreign_id=TCT.type_id AND TML.field='name' AND TML.locale='".$this->getLocaleId()."'
							WHERE TCT.car_id=t1.id) as car_types";
			
			$data = $pjCarModel->select('t1.*, CONCAT(t3.content, " ", t2.content)  as make , t4.content as location_name, t1.location_id, ' . $sub_query)
				->orderBy("$column $direction")->limit($rowCount, $offset)->findAll()->getData();

			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
			$this->set('location_arr', pjSanitize::clean($location_arr));
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminCars.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_update']))
			{
				pjCarModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($_POST);
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjCar', 'data');
				}
				# TYPES
				$pjCarTypeModel = new pjCarTypeModel();
				$pjCarTypeModel->where('car_id', $_POST['id'])->eraseAll();
				if (isset($_POST['type_id']) && is_array($_POST['type_id']) && count($_POST['type_id']) > 0)
				{
					$pjCarTypeModel->begin();
					foreach ($_POST['type_id'] as $type_id)
					{
						$pjCarTypeModel->reset()->setAttributes(array(
							'car_id' => $_POST['id'],
							'type_id' => $type_id
						))->insert();
					}
					$pjCarTypeModel->commit();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminCars&action=pjActionIndex&err=AC01");
				
			} else {
				$arr = pjCarModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminCars&action=pjActionIndex&err=AC08");
				}
				
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjCar');
			
				$this->set('arr', $arr);
				
				$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
					->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
					->where('t2.file IS NOT NULL')
					->orderBy('t1.sort ASC')->findAll()->getData();
				
				$lp_arr = array();
				foreach ($locale_arr as $item)
				{
					$lp_arr[$item['id']."_"] = $item['file'];
				}
				$this->set('lp_arr', $locale_arr);
				$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
				
				$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
				$this->set('type_arr', pjSanitize::clean($type_arr));
				
				$location_arr = pjLocationModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjLocation' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
					
				$this->set('location_arr', pjSanitize::clean($location_arr));
			
				$this->set('car_type_arr', pjCarTypeModel::factory()->where('t1.car_id', $arr['id'])->findAll()->getDataPair(NULL, 'type_id'));
				
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminCars.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjCarTypeModel::factory()->where('car_id', $_GET['id'])->eraseAll();
				
			$response = array();
			if (pjCarModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjCar')->where('foreign_id', $_GET['id'])->eraseAll();
				$pjBookingModel = pjBookingModel::factory();
				$booking_id_arr = $pjBookingModel->where("car_id", $_GET['id'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					$pjBookingModel->reset()->where("car_id", $_GET['id'])->eraseAll();
					pjBookingExtraModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjCarTypeModel::factory()->whereIn('car_id', $_POST['record'])->eraseAll();
				pjCarModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjCar')->whereIn('foreign_id', $_POST['record'])->eraseAll();
				$pjBookingModel = pjBookingModel::factory();
				$booking_id_arr = $pjBookingModel->whereIn("car_id",  $_POST['record'])->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					$pjBookingModel->reset()->whereIn("car_id",  $_POST['record'])->eraseAll();
					pjBookingExtraModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
					pjBookingPaymentModel::factory()->whereIn('booking_id', $booking_id_arr)->eraseAll();
				}
			}
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjCarModel = pjCarModel::factory();
			if (!in_array($_POST['column'], $pjCarModel->getI18n()))
			{
				$pjCarModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjCar', 'data');
			}
		}
		exit;
	}
	
	public function pjActionCheckRegistrationNumber(){
		$this->setAjax(true);
		
		if ($this->isXHR() && isset($_GET['registration_number']))
		{
			$pjCarModel = pjCarModel::factory();
			if (isset($_GET['id']) && (int) $_GET['id'] > 0)
			{
				$pjCarModel->where('t1.id !=', $_GET['id']);
			}
			echo $pjCarModel->where('t1.registration_number', $_GET['registration_number'])->findCount()->getData() == 0 ? 'true' : 'false';
		}
		exit;
	}
	
	public function pjActionAvailability()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
						
			$type_arr = pjTypeModel::factory()->select('t1.*, t2.content AS name')
				->join('pjMultiLang', "t2.model='pjType' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
				->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				
			$this->set('type_arr', pjSanitize::clean($type_arr));
			
			$pjCarModel = pjCarModel::factory()
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'model'", 'left')
							->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCar' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'make'", 'left')
							->join('pjCarType', "t4.car_id = t1.id", 'left')
							->join('pjMultiLang', "t5.foreign_id = t4.type_id AND t5.model = 'pjType' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'name'", 'left');
			
			$car_arr = $pjCarModel	->select('t1.*, CONCAT(t3.content, " ", t2.content)  as car_name, t5.content as type')
									->orderBy("car_name ASC")->findAll()->getData();
							
			$this->set('car_arr', $car_arr);
			
			$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
			$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
			
			$this->appendJs('pjAdminCars.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionLoadAvailability()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
			
			$date_from = isset($_POST['date_from']) ? pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']) . ' 00:00:00' : date('Y-m-d') . ' 00:00:00';
			$date_to = isset($_POST['date_to']) ? pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']) . ' 23:59:59' : date('Y-m-d', time() + (7 * 86400)) . ' 23:59:59' ;
			
			$min_date = isset($_POST['date_from']) ? pjUtil::formatDate($_POST['date_from'], $this->option_arr['o_date_format']) : date('Y-m-d');
			$max_date = isset($_POST['date_to']) ? pjUtil::formatDate($_POST['date_to'], $this->option_arr['o_date_format']) : date('Y-m-d', time() + (7 * 86400));
			
			/*$pjBookingModel->where('`from` <=', $date_to);
			$pjBookingModel->where('`to` >=', $date_from);
			
			if (isset($_POST['car_type']) && (int) $_POST['car_type'] > 0)
			{
				$pjBookingModel->where('t1.type_id', $_POST['car_type']);
			}
			if (isset($_POST['car_id']) && is_array($_POST['car_id']))
			{
				$pjBookingModel->whereIn('t1.car_id', $_POST['car_id']);
			}
			
			$min_max = $pjBookingModel->select("MIN(`from`) as min_from, MAX(`to`) as max_to")
							->orderBy("`from` ASC")->findAll()->getData();
			if(count($min_max) > 0)
			{
				if(!empty($min_max[0]['min_from']) && $min_max[0]['min_from'] < $date_from)
				{
					$min_date = date('Y-m-d', strtotime($min_max[0]['min_from']));
				}
				if(!empty($min_max[0]['max_to']) && $min_max[0]['max_to'] > $date_to)
				{
					$max_date = date('Y-m-d', strtotime($min_max[0]['max_to']));
				}
			}
			$pjBookingModel->reset();*/
			$pjBookingModel->where('`from` <=', $date_to);
			$pjBookingModel->where('`to` >=', $date_from);
			
			if (isset($_POST['car_type']) && (int) $_POST['car_type'] > 0)
			{
				$pjBookingModel->where('t1.type_id', $_POST['car_type']);
			}
			if (isset($_POST['car_id']) && is_array($_POST['car_id']))
			{
				$pjBookingModel->whereIn('t1.car_id', $_POST['car_id']);
			}
			$data = $pjBookingModel->join('pjCar', 't2.id=t1.car_id')
							->join('pjMultiLang', "t3.foreign_id = t2.id AND t3.model = 'pjCar' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'model'", 'left')
							->join('pjType', "t4.id = t1.type_id", 'left')
							->join('pjMultiLang', "t5.foreign_id = t4.id AND t5.model = 'pjType' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'name'", 'left')
							->join('pjMultiLang', "t6.foreign_id = t1.pickup_id AND t6.model = 'pjLocation' AND t6.locale = '".$this->getLocaleId()."' AND t6.field = 'name'", 'left')
							->join('pjMultiLang', "t7.foreign_id = t1.return_id AND t7.model = 'pjLocation' AND t7.locale = '".$this->getLocaleId()."' AND t7.field = 'name'", 'left')
							->join('pjMultiLang', "t8.model='pjCar' AND t8.foreign_id=t1.car_id AND t8.field='make' AND t8.locale='".$this->getLocaleId()."'", 'left')
							->where('t1.status', 'confirmed')->orWhere('t1.status', 'pending')->orWhere('t1.status', 'collected')
							->select("t1.id, t1.type_id, t1.car_id,t1.from, t1.to, t1.status, t1.c_name, t1.created, t3.content as model , t2.registration_number,
										t5.content as type, t6.content as pickup_location, t7.content as return_location, t8.content as make")
							->orderBy("`from` ASC")->findAll()->getData();
							
			$booking_arr = array();
			foreach ($data as $key => $val){
				$val['pick_drop'] = date($this->option_arr['o_datetime_format'], strtotime($val['from'])) . ' ' . __('booking_at', true, false) . ' ' . $val['pickup_location'] . "<br/>";
				$val['pick_drop'] .= date($this->option_arr['o_datetime_format'], strtotime($val['to'])) . ' ' . __('booking_at', true, false) . ' ' . $val['return_location'];
				$val['car_info'] = $val['registration_number'] . ',<br/>' . $val['make'] . " " . $val['model'];
				$booking_arr[$val['car_id']][] = $val;
			}
				
			$pjCarModel = pjCarModel::factory()
							->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjCar' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'model'", 'left')
							->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjCar' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'make'", 'left')
							->join('pjCarType', "t4.car_id = t1.id", 'left')
							->join('pjMultiLang', "t5.foreign_id = t4.type_id AND t5.model = 'pjType' AND t5.locale = '".$this->getLocaleId()."' AND t5.field = 'name'", 'left');
			if (isset($_POST['car_id']) && is_array($_POST['car_id']))
			{
				$pjCarModel->whereIn('t1.id', $_POST['car_id']);
			}
			if (isset($_POST['car_type']) && (int) $_POST['car_type'] > 0)
			{
				$pjCarModel->where("(t1.id IN(SELECT TCT.car_id FROM `".pjCarTypeModel::factory()->getTable()."` AS TCT WHERE TCT.type_id = '".$_POST['car_type']."'))");
			}
			$car_arr = $pjCarModel	->select('t1.*, CONCAT(t3.content, " ", t2.content)  as car_name, t5.content as type')
									->orderBy("car_name ASC")->findAll()->getData();
							
			$this->set('car_arr', $car_arr);
			
			$avail_arr = array();
			foreach($car_arr as $v)
			{
				$run_date = $min_date;
				$car_booking_arr = array();
				if(isset($booking_arr[$v['id']]))
				{
					$car_booking_arr = $booking_arr[$v['id']];
				}
				while($run_date <= $max_date)
				{
					$cell_content = array();
					if(!empty($car_booking_arr))
					{
						foreach($car_booking_arr as $booking)
						{
							$_from = date('Y-m-d', strtotime($booking['from']));
							$_to = date('Y-m-d', strtotime($booking['to']));
							
							if($booking['status'] == 'pending')
							{
								if( (strtotime($booking['created']) + ($this->option_arr['o_booking_pending'] * 3600)) < time() )
								{
									$booking['status'] = 'pending-over';
								}
							}else if($booking['status'] == 'collected'){
								$booking['status'] = 'confirmed';
							}
							
							if($run_date == $_from && $run_date == $_to)
							{
								$cell_content[] = '<div class="pj-booking-block pj-booking-'.$booking['status'].'"><a href="'. $_SERVER['PHP_SELF'] . '?controller=pjAdminBookings&action=pjActionUpdate&id='.$booking['id'].'">'.pjSanitize::html($booking['c_name']).'</a><label>'.__('lblPickupAt', true, false).': <span>'.date('H:i', strtotime($booking['from'])).'</span></label><label class="pj-from">'.strtolower(__('lblFrom', true, false)).': <span>'.$booking['pickup_location'].'</span></label><label class="pj-dropoff">'.__('lblReturnAt', true, false).': <span>'.date('H:i', strtotime($booking['to'])).'</span></label><label class="pj-from">'.strtolower(__('lblAt', true, false)).': <span>'.$booking['return_location'].'</span></label></div>';
							}else if($run_date == $_from){
								$cell_content[] = '<div class="pj-booking-block pj-booking-'.$booking['status'].'"><a href="'. $_SERVER['PHP_SELF'] . '?controller=pjAdminBookings&action=pjActionUpdate&id='.$booking['id'].'">'.pjSanitize::html($booking['c_name']).'</a><label>'.__('lblPickupAt', true, false).': <span>'.date('H:i', strtotime($booking['from'])).'</span></label class="pj-from"><label class="pj-from">'.strtolower(__('lblFrom', true, false)).': <span>'.$booking['pickup_location'].'</span></label></div>';
							}else if($run_date == $_to){
								$cell_content[] = '<div class="pj-booking-block pj-booking-'.$booking['status'].'"><label class="pj-dropoff">'.__('lblReturnAt', true, false).': <span>'.date('H:i', strtotime($booking['to'])).'</span></label><label class="pj-from">'.strtolower(__('lblAt', true, false)).': <span>'.$booking['return_location'].'</span></label></div>';
							}else if($run_date > $_from && $run_date < $_to){
								$cell_content[] = '<div class="pj-booking-block pj-booking-middle pj-booking-'.$booking['status'].'" data-status="'.$booking['status'].'">&nbsp;</div>';
							}
						}
					}
					
					$avail_arr[$v['id']][$run_date] = $cell_content;
					$run_date = date('Y-m-d', strtotime($run_date) + 86400);
				}
			}
			
			$this->set('min_date', $min_date);
			$this->set('max_date', $max_date);
			$this->set('avail_arr', $avail_arr);
		}
	}
}
?>