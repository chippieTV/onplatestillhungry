<h3><?php lang('additional_email_settings');?></h3>
<p>All the regular email settings in EE will apply as usually (activation etc.). On this page, you can choose whether or not to send the user an email on registration.</p>
<hr/>
<?php
    echo form_open($_form_base.AMP."method=save_email_settings", '' );
?>
<p><label for="password_subject">E-Mail Subject</label>
<?php echo form_input('password_subject', $preferences['password_subject'], "id='password_subject'")?></p>
<p><label for="password_email">E-Mail Body</label>&nbsp;<a href='#' class="infobox-link" id="infobox-infobox_body">Variables?</a>
<div class="infobox" id="infobox_body" style="display:none">
<h4>The following variables can be used, in both email subject and body:</h4>

<p><strong>User specific:</strong></p>
    <ul>
        <li>{screen_name}</li>
        <li>{username}</li>
        <li>{email}</li>
        <li>{password} (only if auto-generation of password is enabled in settings)</li>
        <li>{ip_address}</li>
        <li>{group_id}</li>
        <li>{join_date}</li>
    </ul>

<p><strong>Site specific:</strong></p>
    <ul>
        <li>{site_name}</li>
        <li>{site_url}</li>
        <li>{webmaster_email}</li>
    </ul>
</div>

<?php echo form_textarea('password_email', $preferences['password_email'], "class='email_body', id='password_email'")?></p>
<p><?php echo form_checkbox('email_password_on_registration', '1', $preferences['email_password_on_registration'])?> <?php echo lang('email_password_on_registration');?></p>
<p>&nbsp;</p>
<p><?php echo form_submit('delete',lang('btn_save'),'class="submit"'); ?></p>
<p><?php echo form_close();?></p>