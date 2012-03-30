<h3><?php echo lang('reg_form_select_elements')?></h3>
<p><?php echo lang('reg_form_select_expl');?></p>
<p>&nbsp;</p>
<fieldset id="simple_registration_form">
    <legend> <?php print lang('your_reg_form');?> </legend>
<?php
echo form_open($_form_base.AMP."method=save_settings", '' );
?>
<ul>
<li id="screen_name_block">
<label id="title4" for="screen_name">
Screen Name <input type="checkbox" value="1" name="username_equals_screen_name" id="toggle-screen_name" class="activate_check" checked/>
</label>
<div id="screen_name" class="input_box"><?php echo lang('screen_name_expl');?></div>
</li>

<li>
<label for="username">
Username <input type="checkbox" value="1" name="use_email_for_username" id="toggle-username" class="activate_check" checked/>
</label>
<div id="username" class="input_box"><?php echo lang('username_expl');?></div>
</li>


<li>
<label for="email">
Email Address
</label>
<div id="email" class="input_box"><?php echo lang('email_expl');?></div>
</li>

<li>
<label for="confirm_email">
Confirm Email Address <input value="1" name="do_not_require_email_confirmation" type="checkbox" id="toggle-confirm_email" class="activate_check" checked/>
</label>
<div>
<div id="confirm_email" class="input_box"><?php echo lang('email_confirm_expl');?></div>
</div>
</li>

<li>
<label for="password">
Password <input type="checkbox" value="1" name="auto_generate_password" id="toggle-password" class="activate_check" checked/>
</label>
<div>
<div id="password" class="input_box"><?php echo lang('password_expl');?></div>
</div>
</li>

<li>
<label for="confirm_password">
Confirm Password <input type="checkbox" value="1" name="do_not_require_password_confirmation" id="toggle-confirm_password" class="activate_check" checked/>
</label>
<div>
<div id="confirm_password" class="input_box"><?php echo lang('password_confirm_expl');?></div>
</div>
</li>


<li>
<label for="terms">
Terms of Service <input type="checkbox" value="1" name="do_not_require_accept_terms" id="toggle-terms" class="activate_check" checked/>
</label>
<div>
<div class="input_box" id="terms">
<?php echo lang('terms_expl');?>
</div>
</div>
</li>
</ul>
    <?php print form_submit('save',lang('btn_get_code'),'class="submit"')?>
    <?php print form_close()?>

</fieldset>