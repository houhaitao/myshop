<form method="GET" class="sort" name="listform">
  <div class="ect-wrapper text-center">
    <div> <a class="<?php if ($this->_var['sort'] == 'goods_id' && $this->_var['order'] == 'DESC'): ?>ect-colory<?php endif; ?>" href="<?php echo url('brand/goods_list#goods_list',array('id'=>$this->_var['brand_id'],'display'=>$this->_var['display'],'brand'=>$this->_var['brand_id'],'price_min'=>$this->_var['price_min'],'price_max'=>$this->_var['price_max'],'filter_attr'=>$this->_var['filter_attr'], 'sort'=>'goods_id', 'order'=> 'DESC', 'keywords'=>$this->_var['keywords']));?>"><?php echo $this->_var['lang']['sort_default']; ?></a>
    <a class="<?php if ($this->_var['sort'] == 'sales_volume' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'sales_volume' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php else: ?><?php endif; ?>" href="<?php echo url('brand/goods_list#goods_list',array('id'=>$this->_var['brand_id'],'display'=>$this->_var['display'],'brand'=>$this->_var['brand_id'],'price_min'=>$this->_var['price_min'],'price_max'=>$this->_var['price_max'],'filter_attr'=>$this->_var['filter_attr'], 'sort'=>'sales_volume', 'order'=> ($this->_var['sort']=='sales_volume' && $this->_var['order']=='ASC')?'DESC':'ASC', 'keywords'=>$this->_var['keywords']));?>"><?php echo $this->_var['lang']['sort_sales']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a> 
    <a class="<?php if ($this->_var['sort'] == 'click_count' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'click_count' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php else: ?><?php endif; ?>" href="<?php echo url('brand/goods_list#goods_list',array('id'=>$this->_var['brand_id'],'display'=>$this->_var['display'],'brand'=>$this->_var['brand_id'],'price_min'=>$this->_var['price_min'],'price_max'=>$this->_var['price_max'],'filter_attr'=>$this->_var['filter_attr'], 'sort'=>'click_count', 'order'=> ($this->_var['sort']=='click_count' && $this->_var['order']=='ASC')?'DESC':'ASC', 'keywords'=>$this->_var['keywords']));?>"><?php echo $this->_var['lang']['sort_popularity']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a> 
    <a class="<?php if ($this->_var['sort'] == 'shop_price' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'shop_price' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php else: ?><?php endif; ?>" href="<?php echo url('brand/goods_list#goods_list',array('id'=>$this->_var['brand_id'],'display'=>$this->_var['display'],'brand'=>$this->_var['brand_id'],'price_min'=>$this->_var['price_min'],'price_max'=>$this->_var['price_max'],'filter_attr'=>$this->_var['filter_attr'], 'sort'=>'shop_price', 'order'=> ($this->_var['sort']=='shop_price' && $this->_var['order']=='ASC')?'DESC':'ASC', 'keywords'=>$this->_var['keywords']));?>"><?php echo $this->_var['lang']['sort_price']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a> </div>
  </div>
  <input type="hidden" name="category" value="<?php echo $this->_var['category']; ?>" />
  <input type="hidden" name="display" value="<?php echo $this->_var['pager']['display']; ?>" id="display" />
  <input type="hidden" name="brand" value="<?php echo $this->_var['brand_id']; ?>" />
  <input type="hidden" name="price_min" value="<?php echo $this->_var['price_min']; ?>" />
  <input type="hidden" name="price_max" value="<?php echo $this->_var['price_max']; ?>" />
  <input type="hidden" name="filter_attr" value="<?php echo $this->_var['filter_attr']; ?>" />
  <input type="hidden" name="page" value="<?php echo $this->_var['page']; ?>" />
  <input type="hidden" name="sort" value="<?php echo $this->_var['sort']; ?>" />
  <input type="hidden" name="order" value="<?php echo $this->_var['order']; ?>" />
  <input type="hidden" name="keywords" value="<?php echo $this->_var['keywords']; ?>" />
</form>
<div class="ect-margin-tb ect-pro-list ect-margin-bottom0 ect-border-bottom0">
  <ul id="J_ItemList">
    <li class="single_item"></li>
    <a href="javascript:;" class="get_more"></a>
  </ul>
</div>
<!--
  <div class="srp album flex-f-row" id="J_ItemList" style="opacity:1;"> 
    <div class="product flex_in single_item">
      <div class="pro-inner"></div>
    </div>
    <a href="javascript:;" class="get_more"></a>
  </div>
  --> 
<?php if ($this->_var['category'] > 0): ?>
</form>
<?php endif; ?>