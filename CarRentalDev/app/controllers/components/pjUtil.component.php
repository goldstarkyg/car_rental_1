<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	static public function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'UNKNOWN';
	}
	
	static public function textToHtml($content)
	{
		$content = preg_replace('/\r\n|\n/', '<br />', $content);
		return '<html><head><title></title></head><body>'.$content.'</body></html>';
	}
	
	static public function formatPhone($value)
	{
		$value = trim($value);
		$value = preg_replace('/^\+/', '00', $value);
		$value = preg_replace('/\D+/', '', $value);
		
		return $value;
	}
	
	public static function formatCurrencySign($price, $currency, $separator = " ")
	{
		switch ($currency)
		{
			case 'USD':
				$format = "$" . $separator . $price;
				break;
			case 'GBP':
				$format = "&pound;" . $separator . $price;
				break;
			case 'EUR':
				$format = "&euro;" . $separator . $price;
				break;
			case 'JPY':
				$format = "&yen;" . $separator . $price;
				break;
			case 'AUD':
			case 'CAD':
			case 'NZD':
			case 'CHF':
			case 'HKD':
			case 'SGD':
			case 'SEK':
			case 'DKK':
			case 'PLN':
				$format = $price . $separator . $currency;
				break;
			case 'NOK':
			case 'HUF':
			case 'CZK':
			case 'ILS':
			case 'MXN':
				$format = $currency . $separator . $price;
				break;
			default:
				$format = $price . $separator . $currency;
				break;
		}
		return $format;
	}
	
	public static function getField($key, $return=false, $escape=false)
	{
		if (pjObject::getPlugin('pjWebsiteContent') !== NULL)
		{
			return pjWebsiteContentUtil::getField($key, $return, $escape);
		} else {
			return pjToolkit::getField($key, $return, $escape);
		}
	
	}
	
	public static function toMomemtJS($format)
	{
		$f = str_replace(
			array('Y', 'm', 'n', 'd', 'j'), 
			array('YYYY', 'MM', 'M', 'DD', 'D'), 
			$format
		);
		
		return $f;
	}
	
	public static function convertDateTime($date_time, $date_format)
	{
		$time_format = 'h:i A';
		if(count(explode(" ", $date_time)) == 3)
		{
			list($_date, $_time, $_period) = explode(" ", $date_time);
			$iso_time = pjUtil::formatTime($_time . ' ' . $_period, $time_format);
		}else{
			list($_date, $_time) = explode(" ", $date_time);
			$iso_time = pjUtil::formatTime($_time, $time_format);
		}
		$iso_date = pjUtil::formatDate($_date, $date_format);
		$iso_date_time = $iso_date . ' ' . $iso_time;
		$ts = strtotime($iso_date_time);
	
		return compact('iso_date', 'iso_time', 'iso_date_time', 'ts');
	}
}
?>