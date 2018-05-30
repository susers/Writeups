<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */
 
class Search extends M_Controller {


    /**
     * 内容搜索
     */
    public function index() {

		// 搜索参数
		$get = $this->input->get(NULL, TRUE);
		$get = isset($get['rewrite']) ? dr_rewrite_decode($get['rewrite']) : $get;

		$catid = (int)$get['catid'];
		$_GET['page'] = $get['page'];
        $page = max(1, (int)$_GET['page']);
		$get['keyword'] = dr_safe_replace(str_replace(array('%', ' '), array('', '%'), urldecode($get['keyword'])));
		unset($get['c'], $get['m'], $get['id'], $get['page']);
		if (!$get['mid']) {
            $this->msg(fc_lang('缺少mid参数'));
        }

        $this->dir = $get['mid'];
        $this->_module_init($this->dir);

        $this->load->model('search_model');
        $this->search_model->init($this->dir);

        list($total, $sql) = $this->search_model->get($get, $page);

        $category = $this->get_cache('category-'.SITE_ID);
		list($parent, $related) = $this->_related_cat($category, $catid);

        $this->template->assign(dr_search_seo($this->module[$this->dir], $get, $page));
		$this->template->assign(array(
		    'mid' => $get['mid'],
			'cat' => $category[$catid],
			'get' => $get,
			'params' => $get,
			'caitd' => $catid,
			'parent' => $parent,
			'related' => $related,
			'keyword' => $get['keyword'],
			'urlrule' => dr_search_url($get, 'page', '{page}'),
			'search_total' => $total,
			'search_sql' => urlencode($sql),
		));
        if ($category[$catid]['mid']) {
            $this->template->module($category[$catid]['mid']);
        }
		$this->template->display($catid && $category[$catid]['setting']['template']['search'] ? $category[$catid]['setting']['template']['search'] : 'search.html');
    }

}