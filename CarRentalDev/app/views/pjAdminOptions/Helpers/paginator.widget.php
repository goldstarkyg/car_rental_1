<?php
class Paginator
{
	function display($isAjax, $pages)
	{
		?>
		<ul class="paginator">
		<?php
		if ($isAjax)
		{
			$url = parse_url($_SERVER['HTTP_REFERER']);
			$query_string = $url['query'];
		} else {
			$query_string = $_SERVER['QUERY_STRING'];
		}
	
		$sort = NULL; //'col_name='. (isset($_GET['col_name']) && !empty($_GET['col_name']) ? $_GET['col_name'] : 'listing_title'). '&amp;direction='. (isset($_GET['direction']) && in_array($_GET['direction'], array('asc', 'desc')) ? $_GET['direction'] : 'asc') . '&amp;';
		
		if (preg_match('/page=\d+/', $query_string))
		{
			$query_string = preg_replace('/page=\d+/', $sort . 'page=%u', $query_string);
		} else {
			$query_string .= '&'.$sort.'page=%u';
		}
		for ($i = 1; $i <= $pages; $i++)
		{
			if ((isset($_GET['page']) && (int) $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1))
			{
				?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php printf(htmlentities($query_string), $i); ?>" class="focus"><?php echo $i; ?></a></li><?php
			} else {
				?><li><a href="<?php echo $_SERVER['PHP_SELF']; ?>?<?php printf(htmlentities($query_string), $i); ?>"><?php echo $i; ?></a></li><?php
			}
		}
		?>
		</ul>
		<?php
	}
/**
 *
 * Build pagination numbers
 * @param int $records Total records
 * @param int $per_page Per page records
 * @param int $current Current page
 * @param int $delta Number of pages to show in the middle
 * @param int $first_last Number of pages to show at the begining and at the end
 * @return array
 * @access public
 */
	function build($records, $per_page, $current, $delta, $first_last) 
	{
		$total = ceil($records / $per_page);
		
		$current = ($current > $total) ? $total : $current;
		$current = ($current < 1) ? 1 : $current;
	
		$pages = array();
		for ($i=1; $i <= $total; $i++)
		{
			if (($i == $first_last+1 && $current > $first_last+$delta+1) || ($i == $total-$first_last and $current < $total-$first_last-$delta))
			{
				$pages[] = "...";
			}
			if ($i <= $first_last || $i > $total-$first_last)
			{
				$pages[] = $i;
			} elseif ($i >= $current-$delta and $i <= $current+$delta) {
				$pages[] = $i;
			}
		}
		return $pages;
	}
/**
 *
 * Render pagination
 * @param array $pages Pages array. Get it from Paginator::build
 * @param int $current Current page
 * @param string $url Links location URL
 * @param array $urlParams Params that are need to be passed in the URL
 * @param bool $single Show single/multiple link
 * @return string
 * @access public
 */
	function render($pages, $current, $url, $urlParams = array(), $single=false, $reverse=false)
	{
		if ($reverse)
		{
			$prev = 0;
			$p_arr = array();
			foreach ($pages as $k => $v)
			{
				$p_arr[$k] = strpos($v, ".") !== false ? ($prev + 1) . $v : $v;
				$prev = $v;
			}
					
			$_pages = $p_arr;
			arsort($_pages);		
			$_pages = array_values($_pages);
		}
		
		$pagination = array();
		$params = array();
		
		foreach ($urlParams as $key => $val)
		{
			if (!in_array($key, array('page', 'pagin')))
			{
				$params[] = $key . '=' . $val;
			}
		}
		
		$sep1 = strpos($url, '?') === false ? '?' : '&amp;';
		$sep2 = count($params) > 0 ? '&amp;' : NULL;
		$params = join('&amp;', $params);
		
		if (is_array($pages))
		{
			foreach ($pages as $key => $value) 
			{
				$index = !$reverse ? $value : $_pages[$key];
				if ($index == $current) 
				{
					$pagination[] = '<li><span class="current">'.$value.'</span></li>';
				} elseif ($value>0) {
					$pagination[] = '<li><a href="'.$url.$sep1.$params.$sep2.'page='.$index.'" class="pagin" rel="'.$value.'">'.$value.'</a></li>';
				} else {
					$pagination[] = '<li><span class="dots">'.$value.'</span></li>';
				}
			}
			if ($single)
			{
				if (count($pages) > 1 && !isset($_GET['pagin']))
				{
					$pagination[] = '<li><a href="'.$url.$sep1.$params.$sep2.'pagin=false">one page</a></li>';
				} elseif (count($pages) == 1 && isset($_GET['pagin'])) {
					$pagination[] = '<li><a href="'.$url.$sep1.$params.'">multiple pages</a></li>';
				}
			}
		}
		return join("\n", $pagination);
	}	
}
?>