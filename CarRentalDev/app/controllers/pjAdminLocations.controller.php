<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminLocations extends pjAdmin
{
	private $allowedExt = array('gif', 'png', 'jpg');
	
	private $allowedMimeTypes = array('image/gif', 'image/png', 'image/jpeg', 'image/pjpeg');
		
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_create']))
			{
				$pjLocationModel = pjLocationModel::factory();
				if (!$pjLocationModel->validates($_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=AE04");
				}
				$data = array();
				if(isset($_POST['notify_email']))
				{
					$data['notify_email'] = 'T';
				}else{
					$data['notify_email'] = 'F';
				}
				$id = $pjLocationModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$wt_arr = array(
							'location_id' => $id,
							'monday_from' => '00:00:00',
							'monday_to' => '23:59:00',
							'monday_dayoff' => 'F',
							'tuesday_from' => '00:00:00',
							'tuesday_to' => '23:59:00',
							'tuesday_dayoff' => 'F',
							'wednesday_from' => '00:00:00',
							'wednesday_to' => '23:59:00',
							'wednesday_dayoff' => 'F',
							'thursday_from' => '00:00:00',
							'thursday_to' => '23:59:00',
							'thursday_dayoff' => 'F',
							'friday_from' => '00:00:00',
							'friday_to' => '23:59:00',
							'friday_dayoff' => 'F',
							'saturday_from' => '00:00:00',
							'saturday_to' => '23:59:00',
							'saturday_dayoff' => 'F',
							'sunday_from' => '00:00:00',
							'sunday_to' => '23:59:00',
							'sunday_dayoff' => 'F'
					);
					pjWorkingTimeModel::factory()->setAttributes($wt_arr)->insert();
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjLocation', 'data');
					}
					$err = 'AL03';
				} else {
					$err = 'AL04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminLocations&action=pjActionIndex&err=$err");
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
				
				
				$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
				$this->set('country_arr', $country_arr);
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				
				$google_api_key = isset($this->option_arr['o_google_map_api']) ? (!empty($this->option_arr['o_google_map_api']) ? '?key=' . $this->option_arr['o_google_map_api'] : "") : "";
				$this->appendJs('http://maps.google.com/maps/api/js' . $google_api_key, '', true);
				$this->appendJs('pjAdminLocations.js');
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
			$pjLocationModel = pjLocationModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjLocation' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
				->join('pjMultiLang', "t3.foreign_id = t1.id AND t3.model = 'pjLocation' AND t3.locale = '".$this->getLocaleId()."' AND t3.field = 'address_1'", 'left')
				->join('pjMultiLang', "t4.foreign_id = t1.id AND t4.model = 'pjLocation' AND t4.locale = '".$this->getLocaleId()."' AND t4.field = 'city'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjLocationModel->where('t2.content LIKE', "%$q%");
			}
				
			$column = 't2.content';
			$direction = 'ASC';
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjLocationModel->where('t1.status', $_GET['status']);
			}
			
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjLocationModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$pjCarModel = pjCarModel::factory();
			$data = $pjLocationModel->select(sprintf('t1.*, t2.content as name , t3.content as address_1 , t4.content as city , 
			(SELECT COUNT(*) FROM `%s` WHERE `location_id` = `t1`.`id` LIMIT 1) AS `cnt`', $pjCarModel->getTable()))
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
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminLocations.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_update'], $_POST['id']))
			{
				$data = $_POST;
				if (isset($_FILES['thumb']))
				{
					$image = new pjImage();
					$image->setAllowedExt($this->allowedExt);
					$image->setAllowedTypes($this->allowedMimeTypes);
					
					if ($image->load($_FILES['thumb']))
					{
						$thumb = sprintf("%slocations/%u.%s", PJ_UPLOAD_PATH, $_POST['id'], $image->getExtension());
						
						$image->loadImage();
						$image->thumbnail(130, 98);
						$image->saveImage(PJ_INSTALL_PATH . $thumb);
						
						$data['thumb'] = $thumb;
					}
				}
				if(isset($_POST['notify_email']))
				{
					$data['notify_email'] = 'T';
				}else{
					$data['notify_email'] = 'F';
				}
				pjLocationModel::factory()->where('id', $_POST['id'])->limit(1)->modifyAll($data);
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjLocation', 'data');
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminLocations&action=pjActionIndex&err=AL01");
				
			} else {
				$arr = pjLocationModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminLocations&action=pjActionIndex&err=AL08");
				}
				
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjLocation');
			
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
				
				
				$country_arr = pjCountryModel::factory()
						->select('t1.id, t2.content AS country_title')
						->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->orderBy('`country_title` ASC')->findAll()->getData();
				$this->set('country_arr', $country_arr);
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				
				$google_api_key = isset($this->option_arr['o_google_map_api']) ? (!empty($this->option_arr['o_google_map_api']) ? '?key=' . $this->option_arr['o_google_map_api'] : "") : "";
				$this->appendJs('http://maps.google.com/maps/api/js' . $google_api_key, '', true);
				$this->appendJs('pjAdminLocations.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionDeleteThumb()
	{
		$this->setAjax(true);
		
		if ($this->isXHR())
		{
			if ($_SERVER['REQUEST_METHOD'] != 'POST')
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'HTTP method not allowed.'));
			}
			
			if (!(isset($_POST['id']) && pjValidation::pjActionNumeric($_POST['id'])))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 101, 'text' => 'Missing, empty or invalid parameters.'));
			}
			
			$pjLocationModel = pjLocationModel::factory();
			
			$arr = $pjLocationModel->find($_POST['id'])->getData();
			
			if (empty($arr))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 102, 'text' => 'Location not found.'));
			}
			
			if (empty($arr['thumb']) || !is_file(PJ_INSTALL_PATH . $arr['thumb']))
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 103, 'text' => 'Thumbnail not found.'));
			}
			
			if (@unlink(PJ_INSTALL_PATH . $arr['thumb']) === FALSE)
			{
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 104, 'text' => 'Thumbnail has not been deleted.'));
			}
			
			$pjLocationModel
				->reset()
				->set('id', $arr['id'])
				->modify(array('thumb' => ':NULL'));
			
			pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Thumbnail has been deleted.'));
		}
		exit;
	}
	
	public function pjActionDelete()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjLocationModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjWorkingTimeModel::factory()->where('location_id',  $_GET['id'])->eraseAll();
				pjDateModel::factory()->where('location_id',  $_GET['id'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjLocation')->where('foreign_id', $_GET['id'])->eraseAll();
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
				pjLocationModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjWorkingTimeModel::factory()->whereIn('location_id', $_POST['record'])->eraseAll();
				pjDateModel::factory()->whereIn('location_id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjLocation')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjLocationModel = pjLocationModel::factory();
			if (!in_array($_POST['column'], $pjLocationModel->getI18n()))
			{
				$pjLocationModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjLocation');
			}
		}
		exit;
	}
		
	private static function pjActionGeocode($post, $option_arr)
	{
		$address = array();
		$address[] = @$post['zip'];
		$address[] = @$post['address_1'];
		$address[] = @$post['city'];
		$address[] = @$post['state'];

		foreach ($address as $key => $value)
		{
			$tmp = preg_replace('/\s+/', '+', $value);
			$address[$key] = $tmp;
		}
		$_address = join(",+", $address);

		$google_api_key = isset($option_arr['o_google_map_api']) ? (!empty($option_arr['o_google_map_api']) ? '&key=' . $option_arr['o_google_map_api'] : "") : "";
		$gfile = "https://maps.googleapis.com/maps/api/geocode/json?address=$_address" . $google_api_key;
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
			
			$data['zip'] = NULL;
			$geocodeFromLatlon = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$data['lat'].','.$data['lng']. $google_api_key);
			$output2 = json_decode($geocodeFromLatlon);
			if(!empty($output2))
			{
				$addressComponents = $output2->results[0]->address_components;
				foreach($addressComponents as $addrComp)
				{
					if($addrComp->types[0] == 'postal_code')
					{
						$data['zip'] = $addrComp->long_name;
					}
				}
			}			
		} else {
			$data['lat'] = NULL;
			$data['lng'] = NULL;
			$data['zip'] = NULL;
		}
		return $data;
	}
	
	public function pjActionGetGeocode()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$geo = pjAdminLocations::pjActionGeocode(array_merge($_POST, $_POST['i18n'][1]), $this->option_arr);
			$response = array('code' => 100);
			if (isset($geo['lat']) && !is_array($geo['lat']))
			{
				$response = $geo;
				$response['code'] = 200;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
}
?>