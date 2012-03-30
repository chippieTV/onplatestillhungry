<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Const_Simple_Registration
{
    const CURRENT_VERSION = '1.4';

    public static $default_preferences = array(
                'username_equals_screen_name' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'do_not_require_email_confirmation' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'do_not_require_accept_terms' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'do_not_require_password_confirmation' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'use_email_for_username' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'auto_generate_password' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'email_password_on_registration' => array('type' => Abprefs::TYPE_BOOLEAN, 'value' => FALSE),
                'password_subject' => array('type' => Abprefs::TYPE_STRING, 'value' => 'Welcome to {site_name} - your login & password enclosed'),
                'password_email' => array('type' => Abprefs::TYPE_TEXT, 'value' => 'Welcome to {site_name}!

Your login: {username}
Your password: {password}

Please keep this email for your reference.'),
        );

}