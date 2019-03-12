<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminTypes extends pjAdmin
{
	
	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['action_create']))
			{
				$data = array();
				
				$pjTypeModel = pjTypeModel::factory();
				if (!$pjTypeModel->validates($_POST))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTypes&action=pjActionIndex&err=AE04");
				}
				if($this->option_arr['o_booking_periods'] == 'both'){
					$data['rent_type'] = 'both';
				}elseif($this->option_arr['o_booking_periods'] == 'perday'){
					$data['rent_type'] = 'day';
				}elseif($this->option_arr['o_booking_periods'] == 'perhour'){
					$data['rent_type'] = 'hour';
				}
				
				$id = $pjTypeModel->setAttributes(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					$err = 'AT03';
					if (isset($_POST['i18n']))
					{
						pjMultiLangModel::factory()->saveMultiLang($_POST['i18n'], $id, 'pjType', 'data');
					}
					
					# EXTRAS
					$pjTypeExtraModel = new pjTypeExtraModel();
					if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
					{
						$pjTypeExtraModel->begin();
						foreach ($_POST['extra_id'] as $extra_id)
						{
							$pjTypeExtraModel->setAttributes(array(
								'type_id' => $id,
								'extra_id' => $extra_id
							))->insert();
						}
						$pjTypeExtraModel->commit();
					}
							
					# IMAGE 
					if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']))
					{
						$pjImage = new pjImage();
						if ($pjImage->getErrorCode() !== 200)
						{
							$pjImage->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
							if ($pjImage->load($_FILES['image']))
							{
								$resp = $pjImage->isConvertPossible();
								if ($resp['status'] === true)
								{
									$hash = md5(uniqid(rand(), true));
									$path = PJ_UPLOAD_PATH . 'types/thumbs/' . $id . '_' . $hash . '.' . $pjImage->getExtension();
									$pjImage->loadImage();
									$pjImage->resizeSmart(220, 140);
									$pjImage->saveImage($path);
									
									pjTypeModel::factory()->where('id', $id)->limit(1)->modifyAll(array('thumb_path'  => $path ));
								}
							}
						}
					}
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTypes&action=pjActionUpdate&id=$id&err=$err");
				} else {
					$err = 'AT04';
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminTypes&action=pjActionIndex&err=$err");
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
				
				
				$extra_arr = pjExtraModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('extra_arr', pjSanitize::clean($extra_arr));
			
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('pjAdminTypes.js');
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
			$pjCarTypeModel = pjCarTypeModel::factory();
			
			$pjTypeModel = pjTypeModel::factory()
				->join('pjMultiLang', "t2.foreign_id = t1.id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left');
			
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjTypeModel->where('t2.content LIKE', "%$q%");
			}
				
			$column = 't2.content';
			$direction = 'ASC';
			
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('T', 'F')))
			{
				$pjTypeModel->where('t1.status', $_GET['status']);
			}
			
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjTypeModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			
			$data = $pjTypeModel->select(sprintf('t1.*,  t2.content  as type , UCASE(MID(t1.transmission,1,1)) as transmission,
			(SELECT COUNT(*) FROM `%s` WHERE `type_id` = `t1`.`id` LIMIT 1) AS `cnt`', $pjCarTypeModel->getTable()))
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
			$this->appendJs('pjAdminTypes.js');
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
				$data = array();
				if($this->option_arr['o_booking_periods'] == 'both'){
					$data['rent_type'] = 'both';
				}elseif($this->option_arr['o_booking_periods'] == 'perday'){
					$data['rent_type'] = 'day';
				}elseif($this->option_arr['o_booking_periods'] == 'perhour'){
					$data['rent_type'] = 'hour';
				}
				
				$pjTypeModel = pjTypeModel::factory();
				$pjTypeModel->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				if (isset($_POST['i18n']))
				{
					pjMultiLangModel::factory()->updateMultiLang($_POST['i18n'], $_POST['id'], 'pjType', 'data');
				}
				
				# EXTRAS
				$pjTypeExtraModel = new pjTypeExtraModel();
				$pjTypeExtraModel->where('type_id', $_POST['id'])->eraseAll();
				if (isset($_POST['extra_id']) && is_array($_POST['extra_id']) && count($_POST['extra_id']) > 0)
				{
					$pjTypeExtraModel->begin();
					foreach ($_POST['extra_id'] as $extra_id)
					{
						$pjTypeExtraModel->setAttributes(array(
							'type_id' => $_POST['id'],
							'extra_id' => $extra_id
						))->insert();
					}
					$pjTypeExtraModel->commit();
				}
					
				# IMAGE 
				if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']))
				{
					$pjImage = new pjImage();
					if ($pjImage->getErrorCode() !== 200)
					{
						$pjImage->setAllowedTypes(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg'));
						if ($pjImage->load($_FILES['image']))
						{
							$resp = $pjImage->isConvertPossible();
							if ($resp['status'] === true)
							{
								$type_arr = $pjTypeModel->reset()->find($_POST['id'])->getData();
								if (file_exists(PJ_INSTALL_PATH . $type_arr['thumb_path'])) {
									@unlink($type_arr['thumb_path']);
								}
								
								$hash = md5(uniqid(rand(), true));
								$path = PJ_UPLOAD_PATH . 'types/thumbs/' . $_POST['id'] . '_' . $hash . '.' . $pjImage->getExtension();
								@unlink($path);
								$pjImage->loadImage();
								$pjImage->resizeSmart(220, 140);
								$pjImage->saveImage($path);
								
								$pjTypeModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('thumb_path'  => $path ));
							}
						}
					}
				}
				
				pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminTypes&action=pjActionUpdate&id=".$_POST['id']."&tab_id=tabs-1&err=AT01");
				
			} else {
				$arr = pjTypeModel::factory()->find($_GET['id'])->getData();
				if (count($arr) === 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminTypes&action=pjActionIndex&err=AT08");
				}
				
				$arr['i18n'] = pjMultiLangModel::factory()->getMultiLang($arr['id'], 'pjType');
			
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
				
				$extra_arr = pjExtraModel::factory()->select('t1.*, t2.content AS name')
					->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left')
					->where('status', 'T')->orderBy('name ASC')->findAll()->getData();
				$this->set('extra_arr', pjSanitize::clean($extra_arr));
			
				$this->set('type_extra_arr', pjTypeExtraModel::factory()->where('t1.type_id', $arr['id'])->findAll()->getDataPair(NULL, 'extra_id'));
				
				$this->set('price_arr', pjPriceModel::factory()->where('t1.type_id', $arr['id'])->orderBy('t1.date_from ASC')->findAll()->getData());
				
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
			
				$this->appendJs('jquery.multiselect.min.js', PJ_THIRD_PARTY_PATH . 'multiselect/');
				$this->appendCss('jquery.multiselect.css', PJ_THIRD_PARTY_PATH . 'multiselect/');
			
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('jquery.multilang.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
				$this->appendJs('jquery.tipsy.js', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendCss('jquery.tipsy.css', PJ_THIRD_PARTY_PATH . 'tipsy/');
				$this->appendJs('pjAdminTypes.js');
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
			pjTypeExtraModel::factory()->where('type_id', $_GET['id'])->eraseAll();
			
			$response = array();
			if (pjTypeModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjMultiLangModel::factory()->where('model', 'pjType')->where('foreign_id', $_GET['id'])->eraseAll();
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
				pjTypeExtraModel::factory()->whereIn('type_id', $_POST['record'])->eraseAll();
				pjTypeModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjMultiLangModel::factory()->where('model', 'pjType')->whereIn('foreign_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionSave()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjTypeModel = pjTypeModel::factory();
			if (!in_array($_POST['column'], $pjTypeModel->getI18n()))
			{
				$pjTypeModel->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			} else {
				pjMultiLangModel::factory()->updateMultiLang(array($this->getLocaleId() => array($_POST['column'] => $_POST['value'])), $_GET['id'], 'pjType', 'data');
			}
		}
		exit;
	}
	
	public function pjActionDeleteImage()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjTypeModel = pjTypeModel::factory();
			
			$arr = $pjTypeModel->find($_POST['id'])->getData();
			
			$response = array();
			if (file_exists(PJ_INSTALL_PATH . $arr['thumb_path'])) {
				if(@unlink(PJ_INSTALL_PATH . $arr['thumb_path'])){
					$pjTypeModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array('thumb_path' => ':NULL'));
					$response['code'] = 200;
				}else{
					$response['code'] = 100;
				}
			}else{
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
}
?>