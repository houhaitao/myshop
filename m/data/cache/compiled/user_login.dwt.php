<?php echo $this->fetch('library/user_header.lbi'); ?>
 <form name="formLogin" action="<?php echo url('user/login');?>" method="post" class="validforms">
 <div class="flow-consignee ect-bg-colorf">
     <section>
      <ul>
   		<li>
    		<div class="input-text"><b><?php echo $this->_var['lang']['username']; ?>：</b><span><input placeholder="<?php echo $this->_var['lang']['username']; ?>/<?php echo $this->_var['lang']['mobile']; ?>/<?php echo $this->_var['lang']['email']; ?>" name="username" type="text"  class="inputBg" id="username" datatype="*" ></span></div>
        </li>
        <li>
    		<div class="input-text"><b><?php echo $this->_var['lang']['label_password']; ?>：</b><span><input placeholder="<?php echo $this->_var['lang']['label_password']; ?>"  name="password" type="password" class="inputBg" datatype="*6-16" /></span></div>
        </li>
        <?php if ($this->_var['enabled_captcha']): ?>
       <li>
          <div class="input-text code"><b><?php echo $this->_var['lang']['comment_captcha']; ?></b><span>
             <input name="captcha" type="text" placeholder="<?php echo $this->_var['lang']['comment_captcha']; ?>">
             </span><img src="<?php echo url('Public/captcha', array('rand'=>$this->_var['rand']));?>" alt="captcha" class="img-yzm" onClick="this.src='<?php echo url('public/captcha', array('t'=>'Math.random()'));?>'" /></div>
            </li>
            <?php endif; ?>
      </ul>
    </section>
  </div>
  <p class="ect-checkbox ect-padding-tb ect-margin-tb ect-margin-bottom0 ect-padding-lr">
     <input type="checkbox" value="1" name="remember" id="remember" class="l-checkbox" />
     <label for="remember"><?php echo $this->_var['lang']['remember']; ?><i></i></label>
  </p>
   <input type="hidden" name="back_act" value="<?php echo $this->_var['back_act']; ?>" />
  <div class="ect-padding-lr ect-padding-tb"> <input type="submit" class="btn btn-info ect-btn-info ect-colorf" value="<?php echo $this->_var['lang']['now_landing']; ?>" /></div>
  </form>
  <p class="ect-padding-lr ect-margin-tb text-right ect-margin-bottom0" style="clear:both"><a href="<?php echo url('user/get_password_phone');?>"><?php echo $this->_var['lang']['forgot_password']; ?></a>  <a href="<?php echo url('user/register');?>"><?php echo $this->_var['lang']['free_registered']; ?></a></p>
  <div class="ect-padding-lr ect-margin-tb user-hezuo">
  </div>
  </div>
<?php echo $this->fetch('library/search.lbi'); ?>
<?php echo $this->fetch('library/page_footer.lbi'); ?>
</body>
</html>
