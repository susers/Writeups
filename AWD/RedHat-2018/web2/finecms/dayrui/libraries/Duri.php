<?php



/**
 * URI([ģ��Ŀ¼|Ӧ��Ŀ¼/[����Ŀ¼|��ԱĿ¼/]]/������/����/����1/ֵ1/����2/ֵ2 ... )
 */
 
class Duri {

	private $app;		// Ӧ�����
	private $path;		// ģ����߻�ԱĿ¼
	private $class;		// ������
	private $param;		// ����
	private $method;	// ����
	private $segments;	// uri�����ʽ��
	private $directory;	// Ŀ¼��admin����member��
	
	/**
     * ���캯��
     */
    public function __construct() {
		
    }

	/**
	 * ��ʼ��uri 
	 *
	 * @param   string $uri
	 * @return  object
	 */
	private function init($uri) {
	
		$this->app = $this->path = $this->class = $this->param = $this->method = $this->segments = $this->directory = '';
		$this->segments	= explode('/', trim($uri, '/'));
		
		foreach ($this->segments as $i => $t) {
			$this->segments[$i] = str_replace(
					array('$',     '(',     ')',     '%28',   '%29'), // Bad
					array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;'), // Good
					$t);
		}
		// ��֤uri
		if ($this->segments) $this->_validate();
		
		return $this;
	}
	
	/**
	 * ��ǰ��ַ��uri
	 *
	 * @param   intval $mark Ϊ1ʱ�����page/total/search/order����
	 * @return  string
	 */
	public function uri($mark = 0, $router = FALSE) {
	
		$ci = &get_instance();
		$uri = '/';
		
		APP_DIR && $uri.= basename(APP_DIR).'/';
		$ci->router->directory && $uri.= $ci->router->directory;
		$ci->router->class && $uri.= $ci->router->class.'/';
		$ci->router->method && $uri.= $ci->router->method.'/';
		
		if ($router == TRUE) {
            return trim($uri, '/');
        }
		
		$uri_string = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : (strlen($_SERVER['REQUEST_URI']) == 1 || $_SERVER['REQUEST_URI'] == '/'.SELF ? '' : $_SERVER['REQUEST_URI']);
		
		parse_str($uri_string, $uri_array);
		unset($uri_array['s'], $uri_array['d'], $uri_array['c'], $uri_array['m']);
		
		if ($uri_array) {
			foreach ($uri_array as $k => $v) {
				if ($mark && in_array($k, array('page', 'total', 'order', 'search'))) {
                    continue;
                }
				$uri .= $k.'/'.$v.'/';
			}
		}
		
		return trim($uri, '/');
	}
	
	/**
	 * uriת��ci·�� 
	 *
	 * @param   string $uri
	 * @return  array
	 */
	public function uri2ci($uri) {
	
		$uri = trim($uri, '/');
		if (!$uri) return array();
		
		$this->init($uri);
		$data = array();
		
		$this->app && $data['app'] = $this->app;
		$this->path && $data['path'] = $this->path;
		$this->class && $data['class'] = $this->class;
		$this->param && $data['param'] = $this->param;
		$this->method && $data['method'] = $this->method;
		$this->directory && $data['directory'] = $this->directory;
		$this->param && $this->segments && $data['param_str'] = implode('/', $this->segments);
		
		return $data;
	}
	
	
	/**
	 * uriת��URL��ַ
	 *
	 * @param   string $uri
	 * @return  string
	 */
	public function uri2url($uri) {
	
		$uri = trim($uri, '/');
		if (!$uri) {
            return 'null';
        }
		
		if (strpos($uri, 'http://') === 0) {
            return $uri;
        }
		
		$this->init($uri);
		$_uri = ($this->app ? $this->app : $this->path).'/'.$this->class.'/'.$this->method;
		$_uri = trim(trim($_uri, '/'), '/');

		return dr_url($_uri, $this->param);
	}
	
	/**
	 * ��֤uri 
	 *
	 * @param   array	$arr
	 * @return  arr
	 */
	private function _validate() {
		if ($this->segments[0] == 'admin' || ($this->segments[0] == 'member' && ($this->app || $this->path))) {
			// ��һ�������ǿ�����Ŀ¼(admin)
			$this->directory = array_shift($this->segments);
			$this->class = array_shift($this->segments);
			$this->method = array_shift($this->segments);
			$this->param = $this->_get_param($this->segments);
			return TRUE;
		} elseif (!$this->app && is_dir(FCPATH.'app/'.$this->segments[0])) {
			// ��һ��������Ӧ��Ŀ¼
			$this->app = array_shift($this->segments);
			// �ݹ���֤
			$this->_validate();
			return TRUE;
		} elseif (!$this->path && is_dir(FCPATH.'module/'.$this->segments[0])) {
			// ��һ��������ģ��Ŀ¼
			$this->path	= array_shift($this->segments);
			// �ݹ���֤
			$this->_validate();
			return TRUE;
		} elseif (is_file(APPPATH.'controllers/'.$this->segments[0]).'.php') {
			// ��һ�������ǿ�����
			$this->class = array_shift($this->segments);
			$this->method = array_shift($this->segments);
			$this->param = $this->_get_param($this->segments);
			return TRUE;
		}
		// ��һ������ʲô������
		return FALSE;
	}
	
	/**
	 * ��ʣ��uri����ת���ɲ�������
	 *
	 * @param   array	$arr
	 * @return  array
	 */
	private function _get_param($arr) {
	
		if (!$arr) {
            return NULL;
        }
		
		$i = 0;
		$param = array();
		
		foreach ($arr as $k => $t) {
			if ($i%2 == 0) {
                $param[$t] = isset($arr[$k+1]) ? $arr[$k+1] : '';
            }
			$i ++;
		}
		
		return $param;
	}
	
}