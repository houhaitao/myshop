<?php echo $this->fetch('library/page_header.lbi'); ?>
<div class="con">
<div style="height:7.2em;"></div>
  <header>
    <nav class="ect-nav ect-bg icon-write">
      <?php echo $this->fetch('library/page_menu.lbi'); ?>
    </nav>
  </header>
 <div class="ect-wrapper text-center">
<div>
    <a class="<?php if ($this->_var['sort'] == 'goods_id' && $this->_var['order'] == 'DESC'): ?>ect-colory<?php endif; ?>" href="<?php echo url('groupbuy/index', array('id'=>$this->_var['id'],'page'=>$this->_var['page'],'sort'=>'goods_id','order'=>'DESC'));?>"><?php echo $this->_var['lang']['sort_default']; ?></a>
    <a class="<?php if ($this->_var['sort'] == 'sales_count' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'sales_count' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php endif; ?>" href="<?php echo url('groupbuy/index', array('id'=>$this->_var['id'],'page'=>$this->_var['page'],'sort'=>'sales_count', 'order'=> ($this->_var['sort']=='sales_count' && $this->_var['order']=='ASC')?'DESC':'ASC'));?>"><?php echo $this->_var['lang']['sort_sales']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a>
    <a class="<?php if ($this->_var['sort'] == 'click_num' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'click_num' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php else: ?><?php endif; ?>" href="<?php echo url('groupbuy/index', array('id'=>$this->_var['id'],'page'=>$this->_var['page'],'sort'=>'click_num', 'order'=> ($this->_var['sort']=='click_num' && $this->_var['order']=='ASC')?'DESC':'ASC'));?>"><?php echo $this->_var['lang']['sort_popularity']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a>
    <a class="<?php if ($this->_var['sort'] == 'cur_price' && $this->_var['order'] == 'DESC'): ?>select ect-colory<?php elseif ($this->_var['sort'] == 'cur_price' && $this->_var['order'] == 'ASC'): ?>ect-colory<?php else: ?><?php endif; ?>" href="<?php echo url('groupbuy/index', array('id'=>$this->_var['id'],'page'=>$this->_var['page'],'sort'=>'cur_price', 'order'=> ($this->_var['sort']=='cur_price' && $this->_var['order']=='ASC')?'DESC':'ASC'));?>" class="xl"><?php echo $this->_var['lang']['sort_price']; ?> <i class="glyphicon glyphicon-arrow-up"></i></a> </div>
  </div>

<div class="ect-margin-tb ect-pro-list ect-margin-bottom0 ect-border-bottom0">
    <ul id="J_ItemList">
      <li class="single_item"></li>
      <a href="javascript:;" class="get_more"></a> 
	</ul>
</div>
</div>
<?php echo $this->fetch('library/search.lbi'); ?> <?php echo $this->fetch('library/page_footer.lbi'); ?> 
<script type="text/javascript">
	get_asynclist("<?php echo url('groupbuy/asynclist', array('page'=>$this->_var['page'], 'sort'=>$this->_var['sort'], 'order'=>$this->_var['order']));?>" , '__TPL__/images/loader.gif');
</script>
</body></html>