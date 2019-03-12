<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdmin extends pjAppController
{
	public $defaultUser = 'admin_user';
	
	public $requireLogin = true;
	
	public function __construct($requireLogin=null)
	{
		$this->setLayout('pjActionAdmin');
		
		if (!is_null($requireLogin) && is_bool($requireLogin))
		{
			$this->requireLogin = $requireLogin;
		}
		
		if ($this->requireLogin)
		{
			if (!$this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin', 'pjActionForgot')))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
			}
		}
	}
	public function afterFilter()
	{
		parent::afterFilter();
		if ($this->isLoged() && !in_array(@$_GET['action'], array('pjActionLogin')))
		{
			$this->appendJs('index.php?controller=pjAdmin&action=pjActionMessages', PJ_INSTALL_URL, true);
		}
	}
	public function beforeRender()
	{
		
	}
		
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor() )
		{
			$pjBookingModel = pjBookingModel::factory();
			$pjCarModel = pjCarModel::factory();
			$pjCarTypeModel = pjCarTypeModel::factory();
			
			$cnt_new_reservations_today = $pjBookingModel->where("CURDATE() = DATE(t1.`created`)")->where("(t1.car_id IN (SELECT TC.id FROM `".$pjCarModel->getTable()."` AS TC))")->findCount()->getData();
			$cnt_pickups_today = $pjBookingModel->reset()->where("CURDATE() = DATE(t1.`from`)")->where("(t1.car_id IN (SELECT TC.id FROM `".$pjCarModel->getTable()."` AS TC))")->where('t1.status', 'confirmed')->findCount()->getData();
			$cnt_returns_today = $pjBookingModel->reset()->where("CURDATE() = DATE(t1.`to`)")->where('t1.status', 'collected')->where("(t1.car_id IN (SELECT TC.id FROM `".$pjCarModel->getTable()."` AS TC))")->findCount()->getData();
			$cnt_avail_today = $pjCarModel->where("(t1.id NOT IN(SELECT TB.car_id FROM `".$pjBookingModel->getTable()."` AS TB WHERE (CURDATE() BETWEEN DATE(TB.`from`) AND DATE(TB.`to`)) AND TB.status<>'cancelled'))")->findCount()->getData();
			
			$latest_bookings = $pjBookingModel
											->reset()->select("t1.*, CONCAT(t3.content, ' ', t4.content) as car_name, t5.registration_number, t2.content as car_type")
											->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='make' AND t3.locale='".$this->getLocaleId()."'", 'left')
											->join('pjMultiLang', "t4.model='pjCar' AND t4.foreign_id=t1.car_id AND t4.field='model' AND t4.locale='".$this->getLocaleId()."'", 'left')
											->join('pjCar', "t5.id = t1.car_id", 'left')
											->where("(t5.id IS NOT NULL)")
											->limit(10)
											->orderBy('t1.created DESC')
											->findAll()->getData();
											
			$today_pickups = $pjBookingModel
											->reset()->select("t1.*, CONCAT(t3.content, ' ', t4.content) as car_name, t5.registration_number, t2.content as car_type")
											->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='make' AND t3.locale='".$this->getLocaleId()."'", 'left')
											->join('pjMultiLang', "t4.model='pjCar' AND t4.foreign_id=t1.car_id AND t4.field='model' AND t4.locale='".$this->getLocaleId()."'", 'left')
											->join('pjCar', "t5.id = t1.car_id", 'left')
											->where("CURDATE() = DATE(t1.`from`)")
											->where('t1.status', 'confirmed')
											->where("(t5.id IS NOT NULL)")
											->orderBy('t1.created DESC')
											->findAll()->getData();
			$today_returns = $pjBookingModel
											->reset()->select("t1.*, CONCAT(t3.content, ' ', t4.content) as car_name, t5.registration_number, t2.content as car_type")
											->join('pjMultiLang', "t2.foreign_id = t1.type_id AND t2.model = 'pjType' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'name'", 'left')
											->join('pjMultiLang', "t3.model='pjCar' AND t3.foreign_id=t1.car_id AND t3.field='make' AND t3.locale='".$this->getLocaleId()."'", 'left')
											->join('pjMultiLang', "t4.model='pjCar' AND t4.foreign_id=t1.car_id AND t4.field='model' AND t4.locale='".$this->getLocaleId()."'", 'left')
											->join('pjCar', "t5.id = t1.car_id", 'left')
											->where("(t5.id IS NOT NULL)")
											->where("CURDATE() = DATE(t1.`to`)")
											->where('t1.status', 'collected')
											->orderBy('t1.created DESC')
											->findAll()->getData();
											
			
			$this->set('cnt_new_reservations_today', $cnt_new_reservations_today);
			$this->set('cnt_today_pickup', $cnt_pickups_today);
			$this->set('cnt_today_return', $cnt_returns_today);
			$this->set('cnt_avail_today', $cnt_avail_today);
			$this->set('latest_bookings', $latest_bookings);
			$this->set('today_pickups', $today_pickups);
			$this->set('today_returns', $today_returns);
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionForgot()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['forgot_user']))
		{
			if (!isset($_POST['forgot_email']) || !pjValidation::pjActionNotEmpty($_POST['forgot_email']) || !pjValidation::pjActionEmail($_POST['forgot_email']))
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			}
			$pjUserModel = pjUserModel::factory();
			$user = $pjUserModel
				->where('t1.email', $_POST['forgot_email'])
				->limit(1)
				->findAll()
				->getData();
				
			if (count($user) != 1)
			{
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=AA10");
			} else {
				$user = $user[0];
				
				$Email = new pjEmail();
				$Email
					->setTo($user['email'])
					->setFrom($user['email'])
					->setSubject(__('emailForgotSubject', true));
				
				if ($this->option_arr['o_send_email'] == 'smtp')
				{
					$Email
						->setTransport('smtp')
						->setSmtpHost($this->option_arr['o_smtp_host'])
						->setSmtpPort($this->option_arr['o_smtp_port'])
						->setSmtpUser($this->option_arr['o_smtp_user'])
						->setSmtpPass($this->option_arr['o_smtp_pass'])
					;
				}
				
				$body = str_replace(
					array('{Name}', '{Password}'),
					array($user['name'], $user['password']),
					__('emailForgotBody', true)
				);

				if ($Email->send($body))
				{
					$err = "AA11";
				} else {
					$err = "AA12";
				}
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionForgot&err=$err");
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionMessages()
	{
		$this->setAjax(true);
		header("Content-Type: text/javascript; charset=utf-8");
	}
	
	public function pjActionLogin()
	{
		$this->setLayout('pjActionAdminLogin');
		
		if (isset($_POST['login_user']))
		{
			if (!isset($_POST['login_email']) || !isset($_POST['login_password']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_email']) ||
				!pjValidation::pjActionNotEmpty($_POST['login_password']) ||
				!pjValidation::pjActionEmail($_POST['login_email']))
			{
				// Data not validate
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=4");
			}
			$pjUserModel = pjUserModel::factory();

			$user = $pjUserModel
				->where('t1.email', $_POST['login_email'])
				->where(sprintf("t1.password = AES_ENCRYPT('%s', '%s')", pjObject::escapeString($_POST['login_password']), PJ_SALT))
				->limit(1)
				->findAll()
				->getData();

			if (count($user) != 1)
			{
				# Login failed
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=1");
			} else {
				$user = $user[0];
				unset($user['password']);
															
				if (!in_array($user['role_id'], array(1,2,3)))
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['role_id'] == 2 && $user['is_active'] == 'F')
				{
					# Login denied
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=2");
				}
				
				if ($user['status'] != 'T')
				{
					# Login forbidden
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin&err=3");
				}
				
				# Login succeed
				$last_login = date("Y-m-d H:i:s");
				if($user['last_login'] == $user['created'])
				{
					$user['last_login'] = date("Y-m-d H:i:s");
				}
    			$_SESSION[$this->defaultUser] = $user;
    			
    			# Update
    			$data = array();
    			$data['last_login'] = $last_login;
    			$pjUserModel->reset()->setAttributes(array('id' => $user['id']))->modify($data);

    			if ($this->isAdmin())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
    			if ($this->isEditor())
    			{
	    			pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionIndex");
    			}
			}
		} else {
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdmin.js');
		}
	}
	
	public function pjActionLogout()
	{
		if ($this->isLoged())
        {
        	unset($_SESSION[$this->defaultUser]);
        }
       	pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionLogin");
	}
	
	public function pjActionProfile()
	{
		$this->checkLogin();
		
		if (!$this->isAdmin())
		{
			if (isset($_POST['profile_update']))
			{
				$pjUserModel = pjUserModel::factory();
				$arr = $pjUserModel->find($this->getUserId())->getData();
				$data = array();
				$data['role_id'] = $arr['role_id'];
				$data['status'] = $arr['status'];
				$post = array_merge($_POST, $data);
				if (!$pjUserModel->validates($post))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA14");
				}
				$pjUserModel->set('id', $this->getUserId())->modify($post);
				
				pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdmin&action=pjActionProfile&err=AA13");
			} else {
				$this->set('arr', pjUserModel::factory()->find($this->getUserId())->getData());
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdmin.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
}
?>