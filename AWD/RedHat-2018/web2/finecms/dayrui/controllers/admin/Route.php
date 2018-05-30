<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
class Route extends M_Controller {

	/**
     * 更新URL路由
     */
    public function index() {

		$name = $code = $note = '';
		$server = strtolower($_SERVER['SERVER_SOFTWARE']);

		if (strpos($server, 'apache') !== FALSE) {
			$name = 'Apache';
			$note = '<font color=red><b>将以下内容保存为.htaccess文件，放到网站根目录</b></font>';
			$code = 'RewriteEngine On'.PHP_EOL
			.'RewriteBase /'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
			.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ /index.php [NC,L]';
		} elseif (strpos($server, 'iis/7') !== FALSE || strpos($server, 'iis/8') !== FALSE) {
			$name = $server;
			$note = '<font color=red><b>将以下内容保存为Web.config文件，放到网站根目录</b></font>';
			$code = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
			.'<configuration>'.PHP_EOL
			.'    <system.webServer>'.PHP_EOL
			.'        <rewrite>'.PHP_EOL
			.'            <rules>'.PHP_EOL
			.'		<rule name="finecms" stopProcessing="true">'.PHP_EOL
			.'		    <match url="^(.*)$" />'.PHP_EOL
			.'		    <conditions logicalGrouping="MatchAll">'.PHP_EOL
			.'		        <add input="{HTTP_HOST}" pattern="^(.*)$" />'.PHP_EOL
			.'		        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />'.PHP_EOL
			.'		        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />'.PHP_EOL
			.'		    </conditions>'.PHP_EOL
			.'		    <action type="Rewrite" url="index.php" /> '.PHP_EOL
			.'                </rule>'.PHP_EOL
			.'            </rules>'.PHP_EOL
			.'        </rewrite>'.PHP_EOL
			.'    </system.webServer> '.PHP_EOL
			.'</configuration>';
		} elseif (strpos($server, 'iis/6') !== FALSE) {
			$name = $server;
			$note = '建议使用isapi_rewrite第三版,老版本的rewrite不支持RewriteCond语法<br><font color=red><b>将以下内容保存为.htaccess文件，放到网站根目录</b></font>';
			$code = 'RewriteEngine On'.PHP_EOL
			.'RewriteBase /'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
			.'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
			.'RewriteRule !.(js|ico|gif|jpe?g|bmp|png|css)$ /index.php';
		} elseif (strpos($server, 'nginx') !== FALSE) {
			$name = $server;
			$note = '<font color=red><b>将以下代码放到Nginx配置文件中去（如果是绑定了域名，所绑定目录也要配置下面的代码），您懂得！</b></font>';
			$code = 'location / { '.PHP_EOL
			.'    if (-f $request_filename) {'.PHP_EOL
			.'           break;'.PHP_EOL
			.'    }'.PHP_EOL
			.'    if ($request_filename ~* "\.(js|ico|gif|jpe?g|bmp|png|css)$") {'.PHP_EOL
			.'        break;'.PHP_EOL
			.'    }'.PHP_EOL
			.'    if (!-e $request_filename) {'.PHP_EOL
			.'        rewrite . /index.php last;'.PHP_EOL
			.'    }'.PHP_EOL
			.'}';
		} else {
			$name = $server;
			$note = '<font color=red><b>当前服务器不提供伪静态规则，请自己将所有页面定向到index.php文件</b></font>';
		}

		$this->template->assign('menu', $this->get_menu_v3(array(
			fc_lang('URL规则') => array('admin/urlrule/index', 'magnet'),
			fc_lang('添加') => array('admin/urlrule/add', 'plus'),
			fc_lang('伪静态规则') => array('admin/route/index', 'safari'),
		)));
		$this->template->assign(array(
			'name' => $name,
			'code' => $code,
			'note' => $note,
			'count' => $code ? count(explode(PHP_EOL, $code)) : 0,
		));
		$this->template->display('route_index.html');
	}

	/**
     * 生成路由临时文件
     */
    public function todo() {

		$code = array();
	    $module = $this->get_cache('module');
		$urlrule = $this->get_cache('urlrule');

		echo '<pre>';




		foreach ($this->site_info as $siteid => $site) {

			// 站点URL
			if ($site['SITE_REWRITE']) {

				$value = $urlrule[intval($site['SITE_REWRITE'])]['value'];
				if ($value) {
					$code[] = array(
						'name' => '站点[' . $siteid . '] 站点URL规则',
						'preg' => '开始',
						'rule' => ''
					);

                    if ($value['share_search_page']) {
                        list($preg, $rname) = $this->_rule_preg_value($value['share_search_page']);
                        $write = 'search/index/rewrite/$'.$rname['{param}'];
                        $code[] = array(
                            'name' => '内容模型搜索',
                            'preg' => $preg,
                            'rule' => $write
                        );
                    }

                    if ($value['tags']) {
                        list($preg, $rname) = $this->_rule_preg_value($value['tags']);
                        $write = 'tag/index/name/$'.$rname['{tag}'];
                        $code[] = array(
                            'name' => 'tag关键词库',
                            'preg' => $preg,
                            'rule' => $write
                        );
                    }
					$code[] = array(
						'name' => '站点[' . $siteid . '] 站点URL规则',
						'preg' => '结束',
						'rule' => ''
					);
				}
			}


			// 栏目
			$category = $this->get_cache('category-'.$siteid);
			if ($category) {
				foreach ($category as $t) {
					$dir = '';
					$value = $urlrule[intval($t['setting']['urlrule'])]['value'];
					if ($t['tid'] != 2 && $value && !$t['setting']['html']) {
						$code[] = array(
							'name' => '站点['.$siteid.'] 栏目['.$t['name'].' '.$t['dirname'].']',
							'preg' => '开始',
							'rule' => ''
						);
						if ($value['list_page']) {
							// 模型栏目列表(分页)
							$rule = str_replace('{modname}', $dir, $value['list_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							if (isset($rname['{dirname}'])) {
								// 目录格式
								$write = 'category/index/dir/$'.$rname['{dirname}'].'/page/$'.$rname['{page}'];
							} elseif (isset($rname['{pdirname}'])) {
								// 层次目录格式
								$write = 'category/index/dir/$'.$rname['{pdirname}'].'/page/$'.$rname['{page}'];
							} else {
								// id模式
								$write = 'category/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
							}
							$code[] = array(
								'name' => '栏目列表(分页)',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['list']) {
							// 模型栏目列表
							$rule = str_replace('{modname}', $dir, $value['list']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							if (isset($rname['{dirname}'])) {
								// 目录格式
								$write = 'category/index/dir/$'.$rname['{dirname}'];
							} elseif (isset($rname['{pdirname}'])) {
								// 层次目录格式
								$write = 'category/index/dir/$'.$rname['{pdirname}'];
							} else {
								// id模式
								$write = 'category/index/id/$'.$rname['{id}'];
							}
							$code[] = array(
								'name' => '栏目列表',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['show_page']) {
							// 模型内容页(分页)
							$rule = str_replace('{modname}', $dir, $value['show_page']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'show/index/id/$'.$rname['{id}'].'/page/$'.$rname['{page}'];
							$code[] = array(
								'name' => '内容页(分页)',
								'preg' => $preg,
								'rule' => $write
							);
						}
						if ($value['show']) {
							// 模型内容页
							$rule = str_replace('{modname}', $dir, $value['show']);
							list($preg, $rname) = $this->_rule_preg_value($rule);
							$write = 'show/index/id/$'.$rname['{id}'];
							$code[] = array(
								'name' => '内容页',
								'preg' => $preg,
								'rule' => $write
							);
						}

						$code[] = array(
							'name' => '站点['.$siteid.'] 栏目['.$t['name'].' '.$t['dirname'].']',
							'preg' => '结束',
							'rule' => ''
						);
					}
				}
			}

			$code[] = array(
				'name' => '站点['.$siteid.'] 栏目',
				'preg' => '全部结束',
				'rule' => ''
			);

		}

		if (!$code) {
			exit('没有设置伪静态');
		}

		$html = '';
		foreach ($code as $t) {
			if ($t['rule']) {
				$html.= '	// '.$t['name'].PHP_EOL;
				$html.= '	"'.$t['preg'].'"'.$this->_space($t['preg']).'=>	"'.$t['rule'].'",'.PHP_EOL;
			} else {
				$html.= PHP_EOL.'	/*-------------------'.$t['name'].' '.$t['preg'].'-----------------*/ '.PHP_EOL.PHP_EOL;
			}
		}

		echo '<textarea class="form-control" style="height:'.(count(explode(PHP_EOL, $html)) * 10).'px">'.$html.'</textarea>';
		exit;

    }

	// 正则解析
	private function _rule_preg_value($rule) {

		$rule = trim(trim($rule, '/'));

		if (preg_match_all('/\{(.*)\}/U', $rule, $match)) {

			$value = array();
			foreach ($match[0] as $k => $v) {
				$value[$v] = ($k + 1);
			}

			$preg = preg_replace(
				array(
					'#\{id\}#U',
					'#\{uid\}#U',
					'#\{mid\}#U',
					'#\{fid\}#U',
					'#\{page\}#U',

					'#\{pdirname\}#Ui',
					'#\{dirname\}#Ui',
					'#\{modname\}#Ui',
					'#\{name\}#Ui',

					'#\{tag\}#U',
					'#\{param\}#U',

					'#\{y\}#U',
					'#\{m\}#U',
					'#\{d\}#U',

					'#\{.+}#U',
					'#/#'
				),
				array(
					'(\d+)',
					'(\d+)',
					'(\d+)',
					'(\w+)',
					'(\d+)',

					'([\w\/]+)',
					'([a-z0-9]+)',
					'([a-z]+)',
					'([a-z]+)',

					'(.+)',
					'(.+)',

					'(\d+)',
					'(\d+)',
					'(\d+)',

					'(.+)',
					'\/'
				),
				$rule
			);

            // 替换特殊的结果
            $preg = str_replace(
                array('(.+))}-'),
                array('(.+)-'),
                $preg
            );

			return array($preg, $value);
		}

		return array($rule, array());
	}

	// 将规则生成至文件
	private function _to_file($path, $data, $note) {

		$file =  WEBPATH.'config/rewrite.php';

		$string = '<?php'.PHP_EOL.PHP_EOL;
		$string.= 'if (!defined(\'BASEPATH\')) exit(\'No direct script access allowed\');'.PHP_EOL.PHP_EOL;
		$string.= '// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。'.PHP_EOL.PHP_EOL;

		if ($data) {

			arsort($data);
            $end = array();
			foreach ($data as $key => $val) {
                if (strpos($key, '(.+)') === 0) {
                    $end[$key] = $val;
                } else {
				    $string.= '$route[\''.$key.'\']'.$this->_space($key).'= \''.$val.'\'; // '.$this->_get_name($val).' 对应规则：'.$note[$key].PHP_EOL;
                }
			}
            if ($end) {
                $string.= PHP_EOL.PHP_EOL.PHP_EOL;
                foreach ($end as $key => $val) {
                    $string.= '$route[\''.$key.'\']'.$this->_space($key).'= \''.$val.'\'; // '.$this->_get_name($val).' 对应规则：'.$note[$key].PHP_EOL;
                }
            }
		}

		file_put_contents($file, $string);
	}

	// 获取页面名称
	private function _get_name($rule) {
		if (strpos($rule, 'show/index') !== FALSE) {
			return '【内容页】';
		} elseif (strpos($rule, 'category/index') !== FALSE) {
			return '【栏目页】';
		} elseif (strpos($rule, 'search/index') !== FALSE) {
			return '【搜索页】';
		} elseif (strpos($rule, 'tag/index') !== FALSE) {
			return '【标签页】';
		}
	}

	/**
	 * 补空格
	 *
	 * @param	string	$name	变量名称
	 * @return	string
	 */
	private function _space($name) {
		$len = strlen($name) + 2;
	    $cha = 60 - $len;
	    $str = '';
	    for ($i = 0; $i < $cha; $i ++) $str .= ' ';
	    return $str;
	}
}