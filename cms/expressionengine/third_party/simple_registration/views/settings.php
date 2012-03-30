<?php
echo form_open($_form_base.AMP."method=update_settings", '' );
?>
<fieldset style="padding:1.5em;margin:1em">
    <legend> <?php print lang('general_settings');?> </legend>
    <ul>
    <li><?php print form_checkbox('username_equals_screen_name', '1', $preferences['username_equals_screen_name']); ?> <?php print lang('username_equals_screen_name');?></li>
    <li><?php print form_checkbox('do_not_require_email_confirmation', '1', $preferences['do_not_require_email_confirmation']); ?> <?php print lang('do_not_require_email_confirmation');?></li>
    <li><?php print form_checkbox('use_email_for_username', '1', $preferences['use_email_for_username']); ?> <?php print lang('use_email_for_username');?></li>
    <li><?php print form_checkbox('do_not_require_accept_terms', '1', $preferences['do_not_require_accept_terms']); ?> <?php print lang('do_not_require_accept_terms');?></li>        

    <li>
    <?php print form_checkbox('do_not_require_password_confirmation', '1', $preferences['do_not_require_password_confirmation']); ?> <?php print lang('do_not_require_password_confirmation');?>
    <li>
    <?php print form_checkbox('auto_generate_password', '1', $preferences['auto_generate_password']); ?> <?php print lang('auto_generate_password');?>
    <li>

    <li><?php echo form_checkbox('email_password_on_registration', '1', $preferences['email_password_on_registration']) . lang('email_password_on_registration');?></li>
    <li><?php echo form_input('password_subject', $preferences['password_subject'])?></li>
    <li><?php echo form_textarea('password_email', $preferences['password_email'])?></li>
     
    </ul>


</fieldset>



 <?php print form_submit('submit',lang('btn_get_code'),'class="submit"')?>

<?php print form_close()?>

