<h3>Registration Form</h3>
<p>The code below is the HTML you'll need to paste into your template to create a registration form. It's based on your <a href="<?php echo $_base?>">current settings</a>.</p>
<hr/>

<?php
if($preferences['auto_generate_password'] && !$preferences['email_password_on_registration'])
{?>
    <div class='warning'>
        <?php echo lang('auto_pwd_no_email_warning');?>    
    </div>
<?php }?>

<textarea class='formcode bottompad'>
{exp:simple_registration:form}
<?php if(!$preferences['username_equals_screen_name']) {?>
    <p>Screen name: <input type="text" name="screen_name"/></p>
<?php } ?>
<?php if(!$preferences['use_email_for_username']) {?>
    <p>Username: <input type="text" name="username"/></p>
<?php } ?>
    E-mail: <input type="text" name="email"/>
<?php if(!$preferences['do_not_require_email_confirmation']) {?>
    <p>Confirm E-mail: <input type="text" name="email_confirm"/></p>
<?php } ?>
<?php if(!$preferences['auto_generate_password']) {?>
    <p>Password: <input type="password" name="password"/></p>
<?php } ?>
<?php if(!$preferences['do_not_require_password_confirmation']) {?>
    <p>Confirm Password: <input type="password" name="password_confirm"/></p>
<?php } ?>
<?php if(!$preferences['do_not_require_accept_terms']) {?>
    <p><input type="checkbox" name="accept_terms" value="y"/> I agree to the terms of service</p>
<?php }

if($use_membership_captcha)
{
?>
    <p>Submit the word you see below:</p>
    <p>{captcha}</p>
    <p><input type="text" name="captcha"></p>
<?php } ?>

    <p><input type="submit" value="Register account"/></p>
{/exp:simple_registration:form}
</textarea>


<h3>Forgot Password Form</h3>
<p>Below is "Forgot Password?" form you can use anywhere</p>
<hr/>
<textarea class='formcode bottompad'>
{exp:simple_registration:forgot_password}
    Your email: <input type="text" name="email"/><br/>
    <input type="submit" value="Send me password reset link"/>
{/exp:simple_registration:forgot_password}
</textarea>