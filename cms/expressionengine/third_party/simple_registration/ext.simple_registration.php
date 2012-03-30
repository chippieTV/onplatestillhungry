<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('Ab_ExtBase')) {
    require_once PATH_THIRD.'simple_registration/libraries/ab/ab_extbase.php';
}

require_once PATH_THIRD.'simple_registration/const.simple_registration.php';


class Simple_registration_ext extends Ab_ExtBase
{
    public $name            = 'Simple Registration';
    public $version         = Const_Simple_Registration::CURRENT_VERSION;
    public $description     = 'Enables you to remove otherwise required information from the EE signup form.';
    public $settings_exist  = 'n';
    public $docs_url        = 'http://www.addonbakery.com';

    const TRIGGER_SIGNUP = 'on_signup';
    const TRIGGER_ACTIVATION = 'on_activation';

	/**
	 * Settings
	 */
	function settings()
	{
	    $settings = array();
	    return $settings;
	}

	function activate_extension()
	{
		$this->EE->load->dbforge();

		$register_hooks = array(
			'member_member_register_start' => 'on_member_register_start',
            'member_member_register' => 'on_member_register',
            'member_register_validate_members' => 'on_member_register_validate_members',
		);

		$class_name = get_class($this);
		foreach($register_hooks as $hook => $method)
		{
			$data = array(
				'class'        => $class_name,
				'method'       => $method,
				'hook'         => $hook,
				'settings'     => "",
				'priority'     => 10,
				'version'      => $this->version,
				'enabled'      => "y"
			);
			$this->EE->db->insert('extensions', $data);
		}

	}

    /**
     * Generate a random password
     * @return string
     */
    private function generate_password()
    {
        $len = rand(6,8);
        return substr(md5(rand().time()), 0, $len);
    }


    function on_member_register_validate_members($member_id)
    {
        if(!is_array($member_id))
        {
            $member_id = intval($member_id);
            if($member_id > 0)
            {
                // check if we have any pending signup key
                $this->EE->db->from('simple_registration_pending_events ev, simple_registration_signup_keys k');
                $this->EE->db->where('k.simple_registration_signup_key_id','ev.signup_key_id', FALSE);
                $this->EE->db->where('member_id', $member_id);
                $q = $this->EE->db->get();

                if($q->num_rows() > 0)
                {
                    foreach($q->result() as $pending_event)
                    {
                        if ($this->EE->extensions->active_hook('simple_registration_signyp_key_triggered') === TRUE)
                        {
                            $this->EE->extensions->call('simple_registration_signyp_key_triggered', $pending_event->member_id, $pending_event->signup_key, 'activation', $this);
                            if ($this->EE->extensions->end_script === TRUE) return;
                        }

                        $this->EE->db->where('simple_registration_pending_event_id', $pending_event->simple_registration_pending_event_id);
                        $this->EE->db->delete('simple_registration_pending_events');

                        // member group
                        $member_group_id = $pending_event->member_group_id;
                        $this->EE->db->where('member_id', $member_id);
                        $this->EE->db->update('members', array('group_id' => $member_group_id));
                    }
                }
            }
        }
    }

    /**
	 * This hook is called before member is registered.
	 */
	function on_member_register_start()
	{
        $this->EE->load->config('simple_registration');
        $this->EE->lang->loadfile('simple_registration');

        if($this->EE->input->post('simple_registration') != 'y')
        {
            if($this->EE->config->item('simple_registration_no_other_registrations'))
            {
                show_error($this->EE->lang->line('sr_no_outher_registrations_error'));
            }
            else
            {
                return;
            }
        }

        if($this->EE->input->post('simple_registration_ajax') == 'y')
        {
            $this->EE->output = new Sr_Output();
            $this->EE->output->is_ajax = TRUE;
        }

        $this->EE->load->library('abmembers');
        $this->EE->load->library('abprefs', array('module_name' => 'simple_registration')); // can't load this in constructor due to EE bug #12479

        // check honeypot
        $honeypot = $this->EE->config->item('simple_registration_global_honeypot_field');
        if(!$honeypot) {
            $honeypot = $this->EE->input->post('simple_registration_hp');
        }

        if($honeypot)
        {
            if($this->EE->input->post($honeypot) != '')
            {
                $honeypot_info = array(
                    'ip_address' => $this->EE->input->ip_address(),
                    'email' => $this->EE->input->post('email'),
                    'username' => $this->EE->input->post('username'),
                    'screen_name' => $this->EE->input->post('screen_name'),
                    'url' => $this->EE->input->post('url'),
                    'honeypot' => $this->EE->input->post($honeypot),
                    'honeypot_key' => $honeypot,
                );

                if ($this->EE->extensions->active_hook('simple_registration_honeypot_hit') === TRUE)
                {
                    $this->EE->extensions->call('simple_registration_honeypot_hit', $honeypot_info, $this);
                    if ($this->EE->extensions->end_script === TRUE) return;
                }

                $data = array(	'title' 	=> $this->EE->lang->line('simple_registration'),
                                'heading'	=> $this->EE->lang->line('error'),
                                'content'	=> $this->EE->lang->line('sr_honeypot_err'),
                                'link'		=> array($this->EE->functions->fetch_site_index(), stripslashes($this->EE->config->item('site_name')))
                             );

                $this->EE->output->show_message($data);
            }
        }

        // after we've checked the honeypot value we check any of the rewrites
        foreach($this->EE->config->item('simple_registration_input_name_rewrites') as $real_key => $rewritten_key) {
            if(isset($_POST[$rewritten_key])) {
                $_POST[$real_key] = $_POST[$rewritten_key];
            }
        }

        if($this->EE->abprefs->get('auto_generate_password'))
        {
            $_POST['password'] = $this->generate_password();
        }

        if($this->EE->abprefs->get('use_email_for_username'))
        {
            $_POST['username'] = $this->EE->input->post('email');

            if($_POST['username'] == '')
            {
                // if we ever get here email is empty. email can never be empty.
                // to hide the "you must enter a username" message we just set the username to some random garbage
                $_POST['username'] = md5($this->generate_password());
            }
        }

        if($this->EE->abprefs->get('username_equals_screen_name'))
        {
            $username = $this->EE->input->post('username');
            $_POST['screen_name'] = $username ? $username : md5(time());    // just hide the screen name error if username is empty
        }

        if($this->EE->abprefs->get('do_not_require_email_confirmation'))
        {
            $_POST['email_confirm'] = $this->EE->input->post('email');
        }

        if($this->EE->abprefs->get('do_not_require_password_confirmation'))
        {
            $_POST['password_confirm'] = $this->EE->input->post('password');
        }

        if($this->EE->abprefs->get('do_not_require_accept_terms'))
        {
            $_POST['accept_terms'] = 'y';
        }

        /**
         * Handle custom member fields
         */
        $custom_member_fields = $this->EE->abmembers->get_member_fields();
        foreach($custom_member_fields as $field_name => $field_col_name)
        {
            if(isset($_POST[$field_name]))
            {
                $_POST[$field_col_name] = $_POST[$field_name];
                unset($_POST[$field_name]);
            }
        }

        /**
         * If the password should be mailed out to the user we need to keep it here. We only do it if
         * this setting is enabled + it's only stored in the session 0.0001 secs until the other hook
         * is called ;)
         */
        if($this->EE->abprefs->get('email_password_on_registration'))
        {
            if(!isset($_SESSION)){
			    session_start();
		    }
            $_SESSION['USR_PWD'] = $_POST['password'];
        }
	}



	/**
	 * This hook is called when a member registration is submitted. It will check if we have
	 *
	 * @param $data
	 */
	function on_member_register($data, $member_id)
	{
        if($this->EE->input->post('simple_registration') != 'y')
        {
          return;
        }

        $this->EE->load->library('srlib');
        $this->EE->load->library('abprefs', array('module_name' => 'simple_registration')); // can't load this in constructor due to EE bug #12479

        // check if we have a signup key
        $signup_key = $this->EE->input->post('signup_key');
        if($signup_key)
        {
            $signup_key_info = $this->EE->srlib->get_signup_key_info($signup_key);
            if($signup_key_info)
            {
                if($signup_key_info['trigger_event'] == 'on_signup')
                {
                    if ($this->EE->extensions->active_hook('simple_registration_signyp_key_triggered') === TRUE)
                    {
                        $this->EE->extensions->call('simple_registration_signyp_key_triggered', $member_id, $signup_key, 'signup', $this);
                        if ($this->EE->extensions->end_script === TRUE) return;
                    }

                    if($signup_key_info['member_group_id'] > 0)
                    {
                        $this->EE->db->where('member_id', $member_id);
                        $this->EE->db->update('members', array('group_id' => $signup_key_info['member_group_id']));
                    }
                }
                else if($signup_key_info['trigger_event'] == 'on_activation')
                {
                    // save it for later
                    $this->EE->db->insert('simple_registration_pending_events', array('member_id' => $member_id, 'signup_key_id' => $signup_key_info['signup_key_id']));
                }
            }
        }

        if($this->EE->abprefs->get('email_password_on_registration'))
        {
            // first things first
            if(!isset($_SESSION)){
			    session_start();
		    }
            $password = $_SESSION['USR_PWD'];
            unset($_SESSION['USR_PWD']);

            $this->EE->load->library('template', NULL, 'TMPL');
            $this->EE->load->library('email');

            $vars = array();    // temp array
            foreach($data as $data_key => $data_value)
            {
                $vars[$data_key] = $data_value;
            }
            $vars['password'] = $password;
            $vars['site_name'] = $this->EE->config->item('site_name');
            $vars['webmaster_email'] = $this->EE->config->item('webmaster_email');

            $email = $data['email'];
            $email_body = $this->EE->TMPL->parse_variables($this->EE->abprefs->get('password_email'), array($vars));
            $email_subject = $this->EE->TMPL->parse_variables($this->EE->abprefs->get('password_subject'), array($vars));

            if ($this->EE->extensions->active_hook('simple_registration_email_password') === TRUE)
            {
                $email_info = array('email_body' => $email_body, 'email_subject' => $email_subject, 'email' => $email);
                $email_info = $this->EE->extensions->call('simple_registration_email_password', $email_info, $this);
                if ($this->EE->extensions->end_script === TRUE) return;
                if($email_info && is_array($email_info)) {
                    $email_body = $email_info['email_body'];
                    $email_subject = $email_info['email_subject'];
                    $email = $email_info['email'];
                }
            }

            $this->EE->email->wordwrap = true;
            $this->EE->email->from($this->EE->config->item('webmaster_email'), $this->EE->config->item('site_name'));
            $this->EE->email->message($email_body);
            $this->EE->email->subject($email_subject);
            $this->EE->email->to($email);
            $this->EE->email->send();
        }

        $return_url = $this->EE->security->xss_clean($this->EE->input->post('RET'));
        $skip_success_message = ($this->EE->input->post('skip_success_message') == 'y');
        $is_ajax = ($this->EE->input->post('simple_registration_ajax') == 'y');

        if ($this->EE->extensions->active_hook('simple_registration_success') === TRUE)
        {
            $this->EE->extensions->call('simple_registration_success', $member_id, $data, $this);
            if ($this->EE->extensions->end_script === TRUE) return;
        }

        if($return_url && $return_url != $this->EE->config->site_url() || $skip_success_message || $is_ajax )
        {
            $this->EE->output = new Sr_Output();
            $this->EE->output->return_url = $return_url;
            $this->EE->output->skip_success_message = $skip_success_message;
            $this->EE->output->is_ajax = $is_ajax;
        }
	}



}
// END CLASS


/**
 * Get rid of the EE message templates
 */
class Sr_Output extends EE_Output
{
    public $return_url = '';
    public $skip_success_message = FALSE;
    public $is_ajax = FALSE;

	public function show_message($data, $xhtml = TRUE)
	{
        $EE =& get_instance();
        if(isset($data['link']) && $data['link'][0] == $EE->config->item('site_url'))
        {
            $data['link'][0] = $this->return_url;
        }

        if($this->skip_success_message)
        {
            $EE->functions->redirect($data['link'][0]);
        }
        else
        {

            if($this->is_ajax)
		    {
                if(isset($data['content']))
                {
                    $data['content_no_tags'] = strip_tags($data['content']);
                }

                if(isset($data['title']) && strtolower($data['title']) == 'error')
                {
                    $data['status'] = 'error';
                    $data['error_fields'] = $this->get_field_errors($data['content_no_tags']);

                }
                else if(!isset($data['status']))
                {
                    $data['status'] = 'success';
                }


                $this->send_ajax_response($data);
            }
            else
            {
                parent::show_message($data, $xhtml);
            }
        }
	}

    private $err_to_field = array(
        'disallowed_screen_chars' => 'screen_name',
        'invalid_email_address' => 'email',
        'missing_username' => 'username',
        'invalid_characters_in_username' => 'username',
        'missing_password' => 'password',
        'not_secure_password' => 'password',
        'invalid_password' => 'password',
        'missing_email' => 'email',
        'banned_email' => 'email',
        'missmatched_passwords' => 'password_confirm',
        'username_too_short' => 'username',
        'password_too_short' => 'password',
        'username_password_too_long' => 'username',
        'username_taken' => 'username',
        'screen_name_taken' => 'screen_name',
        'email_taken' => 'email',
        'valid_user_email' => 'email',
        'password_based_on_username' => 'password',
        'invalid_screen_name' => 'screen_name',

    );

    /**
     * Try to figgure out which fields failed based on the error messages. Kinda hacky yes, but it's currently
     * the only way I can think of how to do it in EE ..
     *
     * @param $err_content (the notags version w\n etc.)
     * @return array
     */
    private function get_field_errors($err_content)
    {
        $errs = explode("\n", trim($err_content,"\n"));

        if(count($errs) == 0)
        {
            return array();
        }

        $EE = get_instance();

        $error_fields = array();
        $ee_lang_array = $EE->lang->language;

        $custom_member_fields = array();
        $f = $EE->db->get('member_fields');
        foreach($f->result() as $c_field)
        {
            $custom_member_fields[$c_field->m_field_name] = $c_field->m_field_label;
        }

        foreach($errs as $error_text)
        {
            $found_lang_key = FALSE;
            foreach($ee_lang_array as $lang_key => $lang_text)
            {
                if($lang_text == $error_text)
                {
                    if(isset($this->err_to_field[$lang_key]))
                    {
                        $error_fields[$this->err_to_field[$lang_key]] = array('error_key' => $lang_key, 'message' => $lang_text);
                        $found_lang_key = TRUE;
                    }
                    else
                    {
                        $error_fields['unknown'] = array('error_key' => $lang_key, 'message' => $lang_text);
                    }



                    break;
                }
            }

            if(!$found_lang_key)
            {
                // didn't find any .. check for custom member fields
                $found_c_field = FALSE;

                foreach($custom_member_fields as $c_field_name => $c_field_label)
                {

                    foreach($ee_lang_array as $e_lang_key => $e_lang_text)
                    {
                        if($e_lang_text .'&nbsp;'.$c_field_label == $error_text)
                        {
                            $error_fields[$c_field_name] = array('error_key' => $e_lang_key, 'message' => $error_text);
                            $found_c_field = TRUE;
                            break;
                        }
                    }

                    if($found_c_field)
                    {
                        break;
                    }
                }

                if(!$found_c_field)
                {
                    // do a wild guess then ..
                    $guess_field = 'unknown';
                    $guess_for = array('password', 'username', 'screen_name');

                    foreach($guess_for as $guess_field_name)
                    {
                        if(strpos($error_text, $guess_field_name) !== FALSE)
                        {
                            $guess_field = $guess_field_name;
                        }
                    }

                    if(!isset($error_fields[$guess_field])) // only put something there if we don't already have an error there
                    {
                        $error_fields[$guess_field] = array('error_key' => 'none', 'message' => $error_text);
                    }
                }
            }
        }

        return $error_fields;
    }
}


/* End of file ext.get_listed.php */
/* Location: ./system/expressionengine/third_party/get_listed/ext.get_listed.php */