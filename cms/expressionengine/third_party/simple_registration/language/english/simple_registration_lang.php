<?php

$lang['simple_registration_module_name'] = "Simple Registration";
$lang['simple_registration_module_description'] = "Break free from the member templates and customize your registration form.";
$lang['welcome'] = "Welcome to Simple Registration";
$lang['settings'] = 'Settings';
$lang['your_reg_form'] = 'Your Simple Registration Form';

$lang['tab_welcome'] = "Welcome";
$lang['tab_form_code'] = "Your Form Code";
$lang['tab_settings'] = "Settings";
$lang['tab_invite'] = "Invite Only";
$lang['tab_email_settings'] = "Email Settings";
$lang['tab_signup_keys'] = "Signup Keys";
$lang['tab_docs'] = "Documentation";

$lang['title_registration_form'] = 'Simple Registration Form Settings';
$lang['title_form_code'] = 'Your Form Code';

$lang['auto_pwd_no_email_warning'] = '<strong>Please note:</strong> you have enabled auto-generation of the user\'s password on account creation, so you should also enable the "Send user an email with password on registration" feature in "Email Settings" (tab to the right). Or else the user won\'t know the password!';

$lang['btn_get_code'] = "Save & Get Form Code";
$lang['btn_save'] = 'Save';
$lang['btn_save_changes'] = 'Save Changes';

/* Registration Form */

$lang['reg_form_select_elements'] = 'Which elements do you want in your signup form?';
$lang['reg_form_select_expl'] = 'Pick and choose the elements you\'d like to include in your registration form. The checked elements will display.';
$lang['screen_name_expl'] = 'The user will enter a screen name (if disabled <strong>username</strong> will be used, if username is disabled as well email will be used)</strong>';
$lang['username_expl'] = 'The user will enter an username (disable this and <strong>email will be used</strong> for username)';
$lang['email_expl'] = 'The user will enter an e-mail address (this is the only required input for Simple Registration)';
$lang['email_confirm_expl'] = 'The user will confirm the e-mail address by inputting it again.';
$lang['password_expl'] = 'The user will enter a password (if disabled password will be shipped in e-mail on registration, think WordPress)';
$lang['password_confirm_expl'] = 'The user will confirm the password by entering it over again';
$lang['terms_expl'] = 'The user will have to tick a checkbox indicating that he/she has agreed to the Terms of Service';

/* General Settings */
$lang['general_settings'] = "General Settings";
$lang['username_equals_screen_name'] = 'Screen name equals username (user does not have to input screen name)';
$lang['do_not_require_email_confirmation'] = "Do not require email confirmation";
$lang['use_email_for_username'] = 'If this is set the email entered will be used for username';
$lang['do_not_require_accept_terms'] = "Do not require user to accept a 'Terms & Conditions'";
$lang['password_settings'] = "Password Settings";
$lang['do_not_require_password_confirmation'] = "Do not require password confirmation";
$lang['auto_generate_password'] = "Auto-generate password";
$lang['trigger'] = 'Trigger';

/* Email Settings */
$lang['email_settings'] = "Email Settings";
$lang['email_password_on_registration'] = 'Send user an email with password on registration? <em>This box must be ticked if you want the email above to  be sent to new users!</em>';

/* Signup Keys */
$lang['signup_keys'] = "Signup Keys";
$lang['signup_keys_expln'] = "Generate signup keys to attach members to member groups";
$lang['no_signup_keys_added'] = "No signup keys added yet.";
$lang['signup_key_added'] = "New signup key was added";
$lang['add_signup_key'] = "Add Signup Key";
$lang['membergroup'] = 'Member Group';
$lang['signup_key'] = 'Signup Key';
$lang['signup_key_deleted'] = 'Signup key deleted';
$lang['delete'] = 'Delete';
$lang['signup_keys_saved'] = 'Signup keys saved';

/* Errors */

$lang['sr_registration_not_allowed'] = '{exp:simple_registration:form} tag is used but the setting "Allow New Member Registrations?" is set to "No" - you must change this in Membership Preferences.';
$lang['sr_honeypot_err'] = 'Thanks! You entered the honeypot field and thus confirmed that you are not a human.';
$lang['sr_no_outher_registrations_error'] = 'This website only accept registrations through the main registration form';