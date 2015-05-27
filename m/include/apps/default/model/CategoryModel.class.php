<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：CategoryModel.class.php
 * ----------------------------------------------------------------------------
 * 功能描述：ECTOUCH 分类模型
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

class CategoryModel extends BaseModel {

    protected $table = 'category';

    /**
     * 获得分类的信息
     *
     * @param integer $cat_id 
     *
     * @return void
     */
    function get_cat_info($cat_id) {
        return $this->row('SELECT cat_name, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $this->pre . "category WHERE cat_id = '$cat_id'");
    }

    /**
     * 根据id获取获得分类
     *
     * @param integer $cat_id 
     *
     * @return void
     */
    function get_cat_list($cat_id = 0) {
        return $this->query('SELECT * FROM ' . $this->pre . "category WHERE is_show = '1' and parent_id = '$cat_id'");
    }

    /**
     * 根据提供的商品的分类列表，提供商品所属的分类树
     * @param $goods_list
     * @example $goods_list = array('1'=>array(1,2,3));
     * @param $all_cats
     */
    function get_goods_cats_list($goods_list,$all_cats)
    {
        $final = array();
        foreach($goods_list as $k=>$v)
        {
            $tmp_list = array();
            foreach($v as $_v)
            {
                $tree_list = array();
                $this->format_tree($_v,$all_cats,$tree_list);
                $tmp_list = array_merge($tmp_list,$tree_list);

            }
            $final[$k] = array_unique($tmp_list);
        }
        return $final;
    }

    function get_goods_ext_cats($goodids)
    {
        $sql = "select * from " . $this->pre . "goods_cat where goods_id in (".implode(',',$goodids).")";
        $res = $this->query($sql);
        $list = array();
        if(is_array($res))
        {
            foreach($res as $v)
            {
                $list[] = $v['cat_id'];
            }
        }
        return $list;
    }

    function get_all_cats()
    {
        $sql = "select * from " . $this->pre . "category";
        $res = $this->query($sql);
        return $res;
    }


    function format_tree($id,$tree,&$arr)
    {
        foreach($tree as $tr)
        {
            if($tr['cat_id']==$id)
            {
                array_push($arr,$tr['cat_id']);
                if($tr['parent_id']!=0)
                {
                    $this->format_tree($tr['parent_id'],$tree,$arr);
                }
                break;
            }


        }

    }

    function format_goods_list($arr)
    {
        $keys = array_keys($arr);
        $ext_ids = $this->get_goods_ext_cats($keys);
        $secs = array();
        $thirds = array();
        $finals = array();
        $goods_cats = array();
        foreach($arr as $k=>$v)
        {
            $tmp = $v;
            if(isset($ext_ids[$k]))
            {
                $tmp['cat_id'] = array_merge($tmp['cat_id'],$ext_ids[$k]);
            }
            $secs[$k] = $tmp;
            $goods_cats[$k] = $tmp['cat_id'];
        }
        $all_cats = $this->get_all_cats();
        $cats_data = $this->get_goods_cats_list($goods_cats,$all_cats);
        $current_actives = $this->get_current_active();
        foreach($secs as $k=>$v)
        {
            $tmp = $v;
            if(isset($cats_data[$k]))
            {
                $tmp['cat_id'] = $cats_data[$k];
            }
            $tmp['active_id'] = 0;
            $tmp['real_active_price'] = $tmp['real_promote_price'] > 0 ? $tmp['real_promote_price'] : $tmp['real_shop_price'];
            if(is_array($current_actives))
            {
                foreach($current_actives as $active_k=>$active_v)
                {
                    $values = explode(',',$active_v['act_range_ext']);
                    $zk_price = $tmp['real_shop_price'];
                    if($active_v['act_type']=='1') //减钱
                    {
                        $zk_price = $zk_price - $active_v['act_type_ext'];
                    }
                    elseif($active_v['act_type']=='2') //折扣
                    {
                        $zk_price = ($zk_price * $active_v['act_type_ext']) / 100;
                    }
                    else
                    {
                        continue;
                    }
                    if($active_v['act_range'] == '1') //分类
                    {
                        if(is_array($tmp['cat_id']))
                        {
                            foreach($tmp['catid'] as $_my)
                            {
                                if(in_array($_my, $values))
                                {
                                    if($tmp['real_active_price'] > $zk_price)
                                    {
                                        $tmp['real_active_price'] = $zk_price;
                                        $tmp['active_id'] = $active_v['act_id'];
                                    }
                                }
                            }
                        }
                    }
                    elseif($active_v['act_range'] == '2') //品牌
                    {
                        if(in_array($tmp['brand_id'], $values))
                        {
                            if($tmp['real_active_price'] > $zk_price)
                            {
                                $tmp['real_active_price'] = $zk_price;
                                $tmp['active_id'] = $active_v['act_id'];
                            }
                        }
                    }
                    elseif($active_v['act_range'] == '3') //商品
                    {
                        if(in_array($k, $values))
                        {
                            if($tmp['real_active_price'] > $zk_price)
                            {
                                $tmp['real_active_price'] = $zk_price;
                                $tmp['active_id'] = $active_v['act_id'];
                            }
                        }
                    }
                    else
                    {
                        continue;
                    }
                }
            }
            $tmp['active_info'] = $tmp['active_id'] > 0 ? $current_actives[$tmp['active_id']] : '';
            $tmp['active_price'] = $tmp['real_active_price'] > 0 ? price_format($tmp['real_active_price']) : '';
            $finals[$k] = $tmp;
        }


        return $finals;
    }

    function format_good_info($info)
    {
        $keys = array($info['goods_id']);
        $ext_ids = $this->get_goods_ext_cats($keys);
        $goods_cats = array();
        if(isset($ext_ids[$info['goods_id']]))
        {
            $info['cat_id'] = array_merge($info['cat_id'],$ext_ids[$info['goods_id']]);
        }
        $goods_cats[$info['goods_id']] = $info['cat_id'];
        $all_cats = $this->get_all_cats();
        $cats_data = $this->get_goods_cats_list($goods_cats,$all_cats);
        $current_actives = $this->get_current_active();

        if(isset($cats_data[$info['goods_id']]))
        {
            $info['cat_id'] = $cats_data[$info['goods_id']];
        }
        $info['active_id'] = 0;
        $info['real_active_price'] = $info['real_promote_price'] > 0 ? $info['real_promote_price'] : $info['real_shop_price'];
        if(is_array($current_actives))
        {
            foreach($current_actives as $active_k=>$active_v)
            {
                $values = explode(',',$active_v['act_range_ext']);
                $zk_price = $info['real_shop_price'];
                if($active_v['act_type']=='1') //减钱
                {
                    $zk_price = $zk_price - $active_v['act_type_ext'];
                }
                elseif($active_v['act_type']=='2') //折扣
                {
                    $zk_price = ($zk_price * $active_v['act_type_ext']) / 100;
                }
                else
                {
                    continue;
                }
                if($active_v['act_range'] == '1') //分类
                {
                    if(is_array($info['cat_id']))
                    {
                        foreach($info['catid'] as $_my)
                        {
                            if(in_array($_my, $values))
                            {
                                if($info['real_active_price'] > $zk_price)
                                {
                                    $info['real_active_price'] = $zk_price;
                                    $info['active_id'] = $active_v['act_id'];
                                }
                            }
                        }
                    }
                }
                elseif($active_v['act_range'] == '2') //品牌
                {
                    if(in_array($info['brand_id'], $values))
                    {
                        if($info['real_active_price'] > $zk_price)
                        {
                            $info['real_active_price'] = $zk_price;
                            $info['active_id'] = $active_v['act_id'];
                        }
                    }
                }
                elseif($active_v['act_range'] == '3') //商品
                {
                    if(in_array($info['goods_id'], $values))
                    {
                        if($info['real_active_price'] > $zk_price)
                        {
                            $info['real_active_price'] = $zk_price;
                            $info['active_id'] = $active_v['act_id'];
                        }
                    }
                }
                else
                {
                    continue;
                }
            }
        }
        $info['active_info'] = $info['active_id'] > 0 ? $current_actives[$info['active_id']] : '';
        $info['active_price'] = $info['real_active_price'] > 0 ? price_format($info['real_active_price']) : '';



        return $info;
    }

    function get_current_active()
    {
        $gmtime = gmtime();
        $time = time();
        $sql = "select * from " . $this->pre . "favourable_activity where start_time<={$gmtime} and end_time>={$gmtime}";
        $user_rank = ',' . $_SESSION['user_rank'] . ',';
        $sql .= " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'";
        $res = $this->query($sql);
        $currents = array();
        if(is_array($res))
        {
            foreach($res as $r)
            {
                $xs_pre = strlen('限时抢购');
                $tmp = $r;
                if(substr($r['act_name'],0,$xs_pre)=='限时抢购')
                {
                    $format_start_time = strtotime(date('Y-m-d ') . date("H:i:s", $r['start_time']+8*3600));
                    $format_end_time = strtotime(date('Y-m-d ') . date("H:i:s", $r['end_time']+8*3600));
                    if ($format_start_time > $time || $format_end_time < $time) {
                        continue;
                    }
                    $tmp['gmt_end_time'] = strtotime(date('Y-m-d ') . date("H:i:s", $r['end_time']));
                }
                else
                {
                    $tmp['gmt_end_time'] = $r['end_time'];
                }
                $currents[$r['act_id']] = $tmp;
            }
        }
        return $currents;
    }

    /**
     *
     * @access private
     * @param string $children 
     * @param unknown $brand 
     */
    function category_get_count($children, $brand, $ext, $keywords) {
        $display = $GLOBALS['display'];
        $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND " . "g.is_delete = 0 AND ($children OR " . model('Goods')->get_extension_goods($children) . ')';
        if ($keywords != '') {
            $where .= " AND (( 1 " . $keywords . " ) ) ";
        }
        if ($brand > 0) {
            $where .= "AND g.brand_id=$brand ";
        }
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->pre . 'goods AS g ' . ' LEFT JOIN ' . $this->pre . 'touch_goods AS xl ' . ' ON g.goods_id=xl.goods_id ' . ' LEFT JOIN ' . $this->pre . 'member_price AS mp ' . "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " . "WHERE $where $ext ";
        $res = $this->row($sql);
        return $res['count'];
    }

    /**
     * 获得指定分类下的推荐商品
     *
     * @access  public
     * @param   string      $type       推荐类型，可以是 best, new, hot, promote
     * @param   string      $cats       分类的ID
     * @param   integer     $brand      品牌的ID
     * @param   integer     $min        商品价格下限
     * @param   integer     $max        商品价格上限
     * @param   string      $ext        商品扩展查询
     * @return  array
     */
    function get_category_recommend_goods($type = '', $cats = '', $brand = 0, $min = 0, $max = 0, $ext = '') {
        $brand_where = ($brand > 0) ? " AND g.brand_id = '$brand'" : '';

        $price_where = ($min > 0) ? " AND g.shop_price >= $min " : '';
        $price_where .= ($max > 0) ? " AND g.shop_price <= $max " : '';

        $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
                "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
                'promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, b.brand_name ' .
                'FROM ' . $this->pre . 'goods AS g ' .
                'LEFT JOIN ' . $this->pre . 'brand AS b ON b.brand_id = g.brand_id ' .
                "LEFT JOIN " . $this->pre . "member_price AS mp " .
                "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
                'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' . $brand_where . $price_where . $ext;
        $num = 0;
        $type2lib = array('best' => 'recommend_best', 'new' => 'recommend_new', 'hot' => 'recommend_hot', 'promote' => 'recommend_promotion');
        $num = model('Common')->get_library_number($type2lib[$type]);
        switch ($type) {
            case 'best':
                $sql .= ' AND is_best = 1';
                break;
            case 'new':
                $sql .= ' AND is_new = 1';
                break;
            case 'hot':
                $sql .= ' AND is_hot = 1';
                break;
            case 'promote':
                $time = gmtime();
                $sql .= " AND is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time'";
                break;
        }

        if (!empty($cats)) {
            $sql .= " AND (" . $cats . " OR " . model('goods')->get_extension_goods($cats) . ")";
        }

        $order_type = C('recommend_order');
        $sql .= ($order_type == 0) ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY RAND() ' . ' LIMIT ' . $num;
        $res = $this->query($sql);
        $idx = 0;
        $goods = array();
        foreach ($res as $key => $value) {
            if ($value['promote_price'] > 0) {
                $promote_price = bargain_price($value['promote_price'], $value['promote_start_date'], $value['promote_end_date']);
                $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
            } else {
                $goods[$idx]['promote_price'] = '';
            }

            $goods[$idx]['id'] = $value['goods_id'];
            $goods[$idx]['name'] = $value['goods_name'];
            $goods[$idx]['brief'] = $value['goods_brief'];
            $goods[$idx]['brand_name'] = $value['brand_name'];
            $goods[$idx]['short_name'] = C('goods_name_length') > 0 ?
                    sub_str($value['goods_name'], C('goods_name_length')) : $value['goods_name'];
            $goods[$idx]['market_price'] = price_format($value['market_price']);
            $goods[$idx]['shop_price'] = price_format($value['shop_price']);
            $goods[$idx]['thumb'] = get_image_path($value['goods_id'], $value['goods_thumb'], true);
            $goods[$idx]['goods_img'] = get_image_path($value['goods_id'], $value['goods_img']);
            $goods[$idx]['url'] = build_uri('goods/index', array('id' => $value['goods_id']));

            $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $value['goods_name_style']);
            $idx++;
        }
        return $goods;
    }

    /**
     * 获得指定分类的所有上级分类
     *
     * @access  public
     * @param   integer $cat    分类编号
     * @return  array
     */
    function get_parent_cats($cat) {
        if ($cat == 0) {
            return array();
        }
        $sql = 'SELECT cat_id, cat_name, parent_id FROM ' . $this->pre . 'category';
        $arr = $this->query($sql);
        if (empty($arr)) {
            return array();
        }

        $index = 0;
        $cats = array();

        while (1) {
            foreach ($arr AS $row) {
                if ($cat == $row['cat_id']) {
                    $cat = $row['parent_id'];

                    $cats[$index]['cat_id'] = $row['cat_id'];
                    $cats[$index]['cat_name'] = $row['cat_name'];

                    $index++;
                    break;
                }
            }

            if ($index == 0 || $cat == 0) {
                break;
            }
        }
        return $cats;
    }

}
