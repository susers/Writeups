<?php

/**
 * FineCMS 公益软件
 *
 * @策划人 李睿
 * @开发组自愿者  邢鹏程 刘毅 陈锦辉 孙华军
 */


class Tag extends M_Controller {

    /**
     * 关键词库
     */
    public function index() {

        // 获取tag数据
        $tag = dr_safe_replace(urldecode($this->input->get('name')));
        !$tag && exit($this->goto_404_page(fc_lang('Tag参数不存在')));

        $name = 'tags_'.SITE_ID.'-'.$tag;
        list($data, $parent, $related) = $this->get_cache_data($name);
        if (!$data) {
            $rule = $this->get_cache('urlrule', (int)SITE_REWRITE, 'value');;
            $join = $rule['catjoin'] ? $rule['catjoin'] : '/';
            $tag = @end(@explode($join, $tag)); // 转换tag值
            $cache = $this->get_cache('tags-'.SITE_ID);
            if ($cache[$tag]) {
                $data = $this->_tag_row($cache[$tag]['id']);
                // 格式化显示
                //$data = format_value(\Php7cms\Service::L('cache')->get('tag-'.SITE_ID.'-field'), $data);
                $parent = $related = array();
                // 是否存在子tag
                if ($cache[$tag]['codes']) {
                    $parent = array();
                    $related[$tag] = $cache[$tag];
                    foreach ($cache[$tag]['codes'] as $t) {
                        $related[$t] = $cache[$t];
                    }
                } elseif ($cache[$tag]['pid'] && $cache[$cache[$tag]['pid']]) {
                    // 本身就是子词
                    $parent = $cache[$cache[$tag]['pid']];
                    $related[$cache[$tag]['pid']] = $cache[$cache[$tag]['pid']];
                    foreach ($cache[$cache[$tag]['pid']]['codes'] as $t) {
                        $related[$t] = $cache[$t];
                    }
                }
                // 合并缓存数据
                $data['tags'] = $cache[$tag]['tags'];
                $this->set_cache_data($name, array($data, $parent, $related), SYS_CACHE_TAG);
            }
        }

        !$data && $data = array('code' => $tag, 'name' => $tag, 'tags' => $tag);

        $this->template->assign(array(
            'tag' => $data,
            'parent' => $parent,
            'related' => $related,
            'meta_title' => $data['name'].SITE_SEOJOIN.SITE_TITLE,
                'meta_keywords' => SITE_KEYWORDS,
                'meta_description' => SITE_DESCRIPTION,
        ));
        $this->template->display('tag.html');

    }



    // 通过关键字获取tag
    private function _tag_row($id) {

        // 首先查询
        $data = $this->db->where('id', (int)$id)->get(SITE_ID.'_tag')->row_array();

        $data && $this->db->where('id', (int)$id)->set('hits', intval($data['hits'])+1)->update(SITE_ID.'_tag');

        return $data;
    }
}