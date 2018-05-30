<?php

class Dvalidate {
	
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
		return TRUE;
	}
	
	/**
	 * 验证会员名称是否存在
	 */
	public function check_member($value) {
		if (!$value) return TRUE;
		return $this->ci->db->where('username', $value)->count_all_results('member') ? FALSE : TRUE;
	}
	
	/**
	 * 验证手机号码是否可用
	 */
	public function check_phone($value) {
		if (!$value) return TRUE;
		if (strlen($value) == 11 && is_numeric($value)) return FALSE;
		return TRUE;
	}
}