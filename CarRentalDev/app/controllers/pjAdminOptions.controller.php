<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminOptions extends pjAdmin
{
	public function pjActionIndex()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			if (isset($_POST['options_update']))
			{
				$pjOptionModel = new pjOptionModel();
			
				foreach ($_POST as $key => $value)
				{
					if (preg_match('/value-(string|text|int|float|enum|bool|color)-(.*)/', $key) === 1)
					{
						list(, $type, $k) = explode("-", $key);
						if (!empty($k))
						{
							$pjOptionModel
								->reset()
								->where('foreign_id', $this->getForeignId())
								->where('`key`', $k)
								->limit(1)
								->modifyAll(array('value' => $value));
						}
					}
				}
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], 1, 'pjOption');
				}
				
				if (isset($_POST['next_action']))
				{
					switch ($_POST['next_action'])
					{
						case 'pjActionIndex':
							$err = 'AO01';
							break;
						case 'pjActionBooking':
							$err = 'AO02';
							break;
						case 'pjActionBookingForm':
							$err = 'AO03';
							break;
						case 'pjActionConfirmation':
							$err = 'AO04&tab_id=' . $_POST['tab_id'];
							break;
						case 'pjActionTerm':
							$err = 'AO05';
							break;
						case 'pjActionRentalSettings':
							$err = 'AO06';
							break;
					}
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOptions&action=" . @$_POST['next_action'] . "&err=$err");
			}
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBooking()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$pjOptionModel = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll();
			
			$this->set('arr', $pjOptionModel->getData());
			$this->set('o_arr', $pjOptionModel->getDataPair('key'));
			
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionRentalSettings()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
			
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
			
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionBookingForm()
	{
		$this->checkLogin();

		if ($this->isAdmin())
		{
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$this->set('arr', $arr);
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionInstall()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->set('o_arr', $this->models['Option']
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.key ASC')
				->findAll()
				->getData()
			);
					
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file, t2.title')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
				
			
			$this->set('install_locale_arr', $locale_arr);
			
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionConfirmation(){
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				
			$this->set('arr', $arr);
			
			pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
			
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
			
		}
	}
	
	public function pjActionTerm(){
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			
			$arr = pjOptionModel::factory()
				->where('t1.foreign_id', $this->getForeignId())
				->orderBy('t1.order ASC')
				->findAll()
				->getData();
			
			$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang(1, 'pjOption');
				
			$this->set('arr', $arr);
			
			pjObject::import('Model', array('pjLocale:pjLocale', 'pjLocale:pjLocaleLanguage'));
			$locale_arr = pjLocaleModel::factory()->select('t1.*, t2.file')
				->join('pjLocaleLanguage', 't2.iso=t1.language_iso', 'left')
				->where('t2.file IS NOT NULL')
				->orderBy('t1.sort ASC')->findAll()->getData();
			
			$lp_arr = array();
			foreach ($locale_arr as $item)
			{
				$lp_arr[$item['id']."_"] = $item['file']; //Hack for jquery $.extend, to prevent (re)order of numeric keys in object
			}
			$this->set('lp_arr', $locale_arr);
			$this->set('locale_str', pjAppController::jsonEncode($lp_arr));
			
			$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			$this->appendJs('pjAdminOptions.js');
			
		}
	}
	
	public function pjActionPrices(){
		
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if(isset($_POST['options_update']))
			{
				$pjPriceModel = pjPriceModel::factory();
				$pjPriceModel->where(array('type_id' => $_POST['type_id']))->eraseAll();
				
				foreach ($_POST['from'] as $k => $v)
				{
					$data = array();
					$data['type_id'] = $_POST['type_id'];
					$data['from'] = $_POST['from'][$k];
					$data['to'] = $_POST['to'][$k];
					$from_date = pjObject::escapeString(pjUtil::formatDate($_POST['date_from'][$k], $this->option_arr['o_date_format']));
					$to_date = pjObject::escapeString(pjUtil::formatDate($_POST['date_to'][$k], $this->option_arr['o_date_format']));
					$data['date_from'] = $from_date;
					$data['date_to'] = $to_date;
					$data['price'] = $_POST['price'][$k];
					if(isset($_POST['price_per'][$k])){
						$data['price_per'] = $_POST['price_per'][$k];
					}else{
						if($this->option_arr['o_booking_periods'] == 'perday'){
							$data['price_per'] = 'day';
						}else if($this->option_arr['o_booking_periods'] == 'perhour'){
							$data['price_per'] = 'hour';
						}
					}
					
					pjPriceModel::factory()->reset()->setAttributes($data)->insert();
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminTypes&action=pjActionUpdate&id=" . $_POST['type_id'] . "&tab_id=tabs-2&err=ATR01");
			}
		}
	}
	
	public function pjActionDeletePrice(){
		$this->setAjax(true);
		
		$this->checkLogin();
	
		if ($this->isXHR())
		{
			if ($this->isAdmin())
			{
				$response = array('code' => 100);
				if (isset($_POST['id']))
				{
					if (pjPriceModel::factory()->reset()->setAttributes(array('id' => $_POST['id']))->erase()->getAffectedRows() == 1)
					{
						$response['code'] = 200;
					}
				}
				pjAppController::jsonResponse($response);
					
				exit;
			}
		}
	}
	
	public function pjActionUpdateSettings()
	{
		$this->setAjax(true);
		
		$this->checkLogin();
	
		if ($this->isXHR())
		{
			if ($this->isAdmin())
			{
				$response = array('code' => 100);
				if (isset($_POST['key']))
				{
					pjOptionModel::factory()
									->reset()
									->where('foreign_id', $this->getForeignId())
									->where('`key`', $_POST['key'])
									->limit(1)
									->modifyAll(array('value' => $_POST['value']));
									
					$response = array('code' => 200);
				}
				pjAppController::jsonResponse($response);
					
				exit;
			}
		}
	}
	
	public function pjActionPreview()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			$this->appendJs('pjAdminOptions.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdateTheme()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			pjOptionModel::factory()
			->where('foreign_id', $this->getForeignId())
			->where('`key`', 'o_theme')
			->limit(1)
			->modifyAll(array('value' => 'theme1|theme2|theme3|theme4|theme5|theme6|theme7|theme8|theme9|theme10::theme' . $_GET['theme']));
	
		}
	}
}
?>