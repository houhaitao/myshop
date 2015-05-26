<?php

/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：ActivityControoller.class.php
 * ----------------------------------------------------------------------------
 * 功能描述：优惠活动控制器
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */

/* 访问控制 */
defined('IN_ECTOUCH') or die('Deny Access');

class ActivityController extends CommonController {

    private $children = '';
    private $brand = '';
    private $goods = '';
    private $size = 10;
    private $page = 1;
    private $sort = 'last_update';
    private $order = 'ASC';

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 优惠活动 列表
     */
    public function index() {
        $this->parameter();
        $this->assign('page', $this->page);
        $this->assign('size', $this->size);
        $this->assign('sort', $this->sort);
        $this->assign('order', $this->order);
        $this->display('activity.dwt');
    }

    /**
     * 优惠活动 - 异步加载
     */
    public function asynclist() {
        // 开始工作
        $this->parameter();
        $asyn_last = intval(I('post.last')) + 1;
        $this->size = I('post.amount');
        $this->page = ($asyn_last > 0) ? ceil($asyn_last / $this->size) : 1;
        $list = model('Activity')->get_activity_info($this->size, $this->page);
        foreach ($list as $key => $activity) {
            $this->assign('activity', $activity);
            $sayList [] = array(
                'single_item' => ECTouch::view()->fetch('library/asynclist_info.lbi')
            );
        }
        die(json_encode($sayList));
        exit();
    }

    /**
     * 优惠活动 - 活动商品列表
     */
    public function goods_list() {
        $this->parameter();
        $id = intval(I('request.id'));
        $this->assign('id', $id);
        $this->assign('page', $this->page);
        $this->assign('size', $this->size);
        $this->assign('sort', $this->sort);
        $this->assign('order', $this->order);
        $this->display('activity_goods_list.dwt');
    }

    /**
     * 限时抢购
     */
    public function xsgoods_list()
    {
        $this->parameter();
        $this->assign('page', $this->page);
        $this->assign('size', $this->size);
        $this->assign('sort', $this->sort);
        $this->assign('order', $this->order);
        $this->display('activity_xsgoods_list.dwt');
    }

    /**
     * 限时抢购活动列表
     */
    public function xsasynclist_list()
    {
        $this->parameter();
        $time = time();
        $sql = "act_name like '限时抢购%' and start_time<=" . $time . " and end_time>=" . $time;
        $user_rank = ',' . $_SESSION['user_rank'] . ',';
        $sql .= " AND CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'";
        $my_res = $this->model->table('favourable_activity')->field()->where($sql)->order('sort_order ASC')->select();
        $list = array();
        $sayList = array();
        $goods_ids = array();
        foreach($my_res as $res) {

            $format_start_time = strtotime(date('Y-m-d ') . date("H:i:s", $res['start_time']+8*3600));
            $format_end_time = strtotime(date('Y-m-d ') . date("H:i:s", $res['end_time']+8*3600));
            if($format_start_time > $time || $format_end_time < $time)
            {
                continue;
            }

            if ($res['act_range'] != FAR_ALL && !empty($res['act_range_ext'])) {
                if ($res['act_range'] == FAR_CATEGORY) {
                    $this->children = " cat_id " . db_create_in(get_children_cat($res['act_range_ext']));
                } elseif ($res['act_range'] == FAR_BRAND) {
                    $this->brand = "g.brand_id " . db_create_in($res['act_range_ext']);
                } else {
                    $this->goods = " AND g.goods_id " . db_create_in($res['act_range_ext']);
                }
            }
            $asyn_last = intval(I('post.last')) + 1;
            $this->size = I('post.amount');

            $this->page = ($asyn_last > 0) ? ceil($asyn_last / $this->size) : 1;
            $goodslist = model('Activity')->category_get_goods($this->children, $this->brand, $this->goods, $this->price_min, $this->price_max, $this->ext, $this->size, $this->page, $this->sort, $this->order,$res);
            foreach ($goodslist as $key => $value) {
                if(!in_array($key, $goods_ids)) {
                    $this->assign('act_goods', $value);
                    $sayList [] = array(
                        'single_item' => ECTouch::view()->fetch('library/asynclist_info.lbi')
                    );
                    $goods_ids[] = $key;
                }
            }
        }
        die(json_encode($sayList));
        exit();
    }

    /**
     * 优惠活动 - 活动商品列表 -异步加载
     */
    public function asynclist_list() {
        $this->parameter();
        $id = intval(I('request.id'));
        if (!$id) {
            $url = url('activity/index');
            ecs_header("Location: $url\n");
        }
        $res = $this->model->table('favourable_activity')->field()->where("act_id = '$id'")->order('sort_order ASC')->find();
        $list = array();

        if ($res['act_range'] != FAR_ALL && !empty($res['act_range_ext'])) {
            if ($res['act_range'] == FAR_CATEGORY) {
                $this->children = " cat_id " . db_create_in(get_children_cat($res['act_range_ext']));
            } elseif ($res['act_range'] == FAR_BRAND) {
                $this->brand = "g.brand_id " . db_create_in($res['act_range_ext']);
            } else {
                $this->goods = " AND g.goods_id " . db_create_in($res['act_range_ext']);
            }
        }
        $this->assign('id', $id);
        $asyn_last = intval(I('post.last')) + 1;
        $this->size = I('post.amount');
        $this->page = ($asyn_last > 0) ? ceil($asyn_last / $this->size) : 1;
        $goodslist = model('Activity')->category_get_goods($this->children, $this->brand, $this->goods, $this->price_min, $this->price_max, $this->ext, $this->size, $this->page, $this->sort, $this->order);
        foreach ($goodslist as $key => $value) {
            $this->assign('act_goods', $value);
            $sayList [] = array(
                'single_item' => ECTouch::view()->fetch('library/asynclist_info.lbi')
            );
        }
        die(json_encode($sayList));
        exit();
    }

    /**
     * 处理参数便于搜索商品信息
     */
    private function parameter() {
        // 如果分类ID为0，则返回总分类页
        $page_size = C('page_size');
        $this->size = intval($page_size) > 0 ? intval($page_size) : 10;
        /* 排序、显示方式以及类型 */
        $default_display_type = C('show_order_type') == '0' ? 'list' : (C('show_order_type') == '1' ? 'grid' : 'album');
        $default_sort_order_method = C('sort_order_method') == '0' ? 'DESC' : 'ASC';
        $default_sort_order_type = C('sort_order_type') == '0' ? 'goods_id' : (C('sort_order_type') == '1' ? 'shop_price' : 'last_update');

        $this->sort = (isset($_REQUEST['sort']) && in_array(trim(strtolower($_REQUEST['sort'])), array(
                    'goods_id',
                    'shop_price',
                    'last_update',
                    'sales_volume',
                    'click_count'
                ))) ? trim($_REQUEST['sort']) : $default_sort_order_type; // 增加按人气、按销量排序 by wang
        $this->order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array(
                    'ASC',
                    'DESC'
                ))) ? trim($_REQUEST['order']) : $default_sort_order_method;
        $display = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array(
                    'list',
                    'grid',
                    'album'
                ))) ? trim($_REQUEST['display']) : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
        $display = in_array($display, array(
                    'list',
                    'grid',
                    'album'
                )) ? $display : 'album';
        setcookie('ECS[display]', $display, gmtime() + 86400 * 7);
    }

}
