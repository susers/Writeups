<?php

class Dfilter {

	private $ci;

	/**
     * 构造函数
     */
    public function __construct() {
		$this->ci = &get_instance();
    }

	/**
	 * 举例测试
	 */
	public function __test($value,  $p1) {
		return $value;
	}
}
