<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Show extends M_Controller {


    /**
     * 阅读
     */
    public function index() {


        $id = (int)$this->input->get('id');
        $page = max(1, (int)$this->input->get('page'));

        $index = $this->db->where('id', $id)->get(SITE_ID.'_index')->row_array();
        if (!$index) {
            $this->goto_404_page(fc_lang('无法通过%s找到对应的模型', $id));
        }
        // 设置模型信息
        $this->dir = $index['mid'];
        if (!$this->dir) {
            $this->goto_404_page(fc_lang('此内容mid参数不存在'));
        }

        $this->_module_init($this->dir);
        $category = $this->get_cache('category-'.SITE_ID);

        // 正式内容缓存查询结果
        $name = 'show'.$this->dir.SITE_ID.$id;
        $data = $this->get_cache_data($name);
        if (!$data) {
            $this->load->model('content_model');
            $data = $this->content_model->get($id);
            if (!$data) {
                $this->goto_404_page(fc_lang('内容(id#%s)不存在', $id));
            }
            // 定向URL
            dr_is_redirect(4, $data['url']);

            // 检测转向字段
            $redirect = 0;
            foreach ($this->module[$this->dir]['field'] as $t) {
                if ($t['fieldtype'] == 'Redirect'
                    && $data[$t['fieldname']]) {
                    $this->db->where('id', $id)->set('hits', 'hits+1', FALSE)->update(SITE_ID.'_'.$this->dir);

                    redirect($data[$t['fieldname']], 'location', 301);
                    exit;
                }
            }

            $data['catid'] = intval($data['catid']);
            $cat = $category[$data['catid']];

            // 处理关键字标签
            $data['tag'] = $data['keywords'];
            $data['keyword_list'] = dr_tag_list($data['keywords'], 0);

            // 上一篇文章
            $this->db->where('catid', $data['catid'])->where('status', 9);
            $this->db->where('id<', $data['id']);
            $this->db->order_by('id desc');
            $data['prev_page'] = $this->db->limit(1)->get($this->content_model->prefix)->row_array();

            // 下一篇文章
            $this->db->where('catid', $data['catid'])->where('status', 9);
            $this->db->where('id>', $data['id']);
            $this->db->order_by('id asc');
            $data['next_page'] = $this->db->limit(1)->get($this->content_model->prefix)->row_array();

            // 缓存数据
            $data['uid'] != $this->uid && $data = $this->set_cache_data($name, $data, SYS_CACHE_MSHOW);
        } else {
            $cat = $category[$data['catid']];
        }

        // 状态判断
        if ($data['status'] == 10 && !($this->uid == $data['uid'] || $this->member['adminid'])) {
            $this->goto_404_page(fc_lang('您暂时无法访问'));
        }

        // 驗證url重複
        $murl = ltrim($data['url'], '/');
        $nurl =  ltrim($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'], '/');
        if (strlen($murl) != strlen($nurl) || $nurl != $murl) {
            header('Location: '.SITE_URL.$murl, TRUE, 301);
            exit;
        }

        // 格式化输出自定义字段
        $fields = $this->module[$this->dir]['field'];
        $fields = $cat['field'] ? array_merge($fields, $cat['field']) : $fields;
        $fields['inputtime'] = array('fieldtype' => 'Date');
        $fields['updatetime'] = array('fieldtype' => 'Date');
        $data = $this->field_format_value($fields, $data, $page);

        // 判断分页
        if ($page && isset($data['content_page'])
            && $data['content_page'] && !$data['content_page'][$page]) {
            $this->goto_404_page(fc_lang('该分页不存在'));
        }

        // 栏目下级或者同级栏目
        list($parent, $related) = $this->_related_cat($category, $data['catid']);

        $this->template->assign($data);
        $this->template->assign(dr_show_seo($data, $page));
        $this->template->assign(array(
            'cat' => $cat,
            'page' => $page,
            'top' => $category[$data['catid']]['topid'] && $category[$category[$data['catid']]['topid']] ? $category[$category[$data['catid']]['topid']] : $cat,
            'parent' => $parent,
            'params' => array('catid' => $data['catid']),
            'related' => $related,
            'urlrule' => dr_show_url( $data, '{page}'),
        ));

        if ($cat['mid']) {
            $this->template->module($cat['mid']);
        }

        $tpl = isset($data['template']) && strpos($data['template'], '.html') !== FALSE ? $data['template'] : ($cat['setting']['template']['show'] ? $cat['setting']['template']['show'] : 'show.html');

        $this->template->display($tpl);

    }

}