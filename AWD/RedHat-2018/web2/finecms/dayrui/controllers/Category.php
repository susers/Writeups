<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */

class Category extends M_Controller {

    /**
     * 栏目
     */
    public function index() {


        $id = (int)$this->input->get('id');
        $dir = $this->input->get('dir', TRUE);
        $page = max(1, (int)$this->input->get('page'));
        $category = $this->get_cache('category-'.SITE_ID);
        $category_dir = $this->get_cache('category-dir-'.SITE_ID);

        if ($id) {
            $cat = $category[$id];
            !$cat && $this->goto_404_page(fc_lang('栏目(%s)不存在', $id));
        } elseif ($dir) {
            $id = $category_dir[$dir];
            $cat = $category[$id];
            if (!$cat) {
                // 无法通过目录找到栏目时，尝试多及目录
                foreach ($category as $t) {
                    if ($t['setting']['urlrule']) {
                        $rule = $this->get_cache('urlrule', $t['setting']['urlrule']);
                        if ($rule['value']['catjoin'] && strpos($dir, $rule['value']['catjoin'])) {
                            $dir = trim(strchr($dir, $rule['value']['catjoin']), $rule['value']['catjoin']);
                            if (isset($category_dir[$dir])) {
                                $id = $category_dir[$dir];
                                $cat = $category[$id];
                                break;
                            }
                        }
                    }
                }
                // 返回无法找到栏目
                !$cat && $this->goto_404_page(fc_lang('栏目(%s)不存在', $dir));
            }
        } else {
            $this->goto_404_page(fc_lang('栏目参数不存在'));
        }

        // 设置模型信息
        $this->dir = $cat['mid'];

        // 验证是否存在子栏目，是否将下级第一个单页作为当前页
        if ($cat['child'] && $cat['setting']['getchild']) {
            $temp = explode(',', $cat['childids']);
            if ($temp) {
                foreach ($temp as $i) {
                    if ($category[$i]['id'] != $id && $category[$i]['show'] && !$category[$i]['child']) {
                        $id = $i;
                        $cat = $category[$i];
                        break;
                    }
                }
            }
        }
        if ($cat['tid'] && $this->dir) {
            // 模型
            $this->_module_init($this->dir);
            $tpl = $cat['child'] ? $cat['setting']['template']['category'] : $cat['setting']['template']['list'];
        } else {
            // 单页
            $cat['title'] = $cat['title'] ? $cat['title'] : $cat['name'];
            $cat['pageid'] = $cat['id'];
            $this->template->assign($cat);
            $tpl = $cat['setting']['template']['page'] ? $cat['setting']['template']['page'] : 'page.html';
        }

        // 定向URL
        #dr_is_redirect(3, $cat['url']);


        list($parent, $related) = $this->_related_cat($category, $id);

        if ($cat['mid']) {
            $this->template->module($cat['mid']);
        }
        $this->template->assign(dr_category_seo($cat, max(1, (int)$this->input->get('page'))));
        $this->template->assign(array(
            'cat' => $cat,
            'top' => $category[$id]['topid'] && $category[$category[$id]['topid']] ? $category[$category[$id]['topid']] : $cat,
            'page' => $page,
            'catid' => $id,
            'params' => array('catid' => $id),
            'parent' => $parent,
            'related' => $related,
            'urlrule' => dr_category_url($cat, SITE_ID, '{page}'),
        ));
        $this->template->display($tpl);

    }

}