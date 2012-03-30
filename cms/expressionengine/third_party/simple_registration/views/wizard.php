<?php
echo form_open($_form_base.AMP."method=update_settings", '' );
?>

<div id="sr-wizard">

<img src="<?php echo $cp_theme_path?>images/email_password.png" alt="email + password"/>

<img src="<?php echo $cp_theme_path?>images/email_password_confirm.png" alt="email + password"/>

<img src="<?php echo $cp_theme_path?>images/email_confirm_password_confirm.png" alt="email + password"/>

<img src="<?php echo $cp_theme_path?>images/username_email_password_confirm.png" alt="email + password"/>    

</div>

<?php echo form_close(); ?>