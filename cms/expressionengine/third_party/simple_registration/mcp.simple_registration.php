<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('Ab_McpBase')) { 
	require_once PATH_THIRD.'simple_registration/libraries/ab/ab_mcpbase'.EXT;	
}

require_once PATH_THIRD.'simple_registration/const.simple_registration'.EXT;

/**
 * Let's you put a registration form anywhere
 *
 * @package		Simple_registration
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		bjorn
 * @link		http://ee.bybjorn.com/
 */
class Simple_registration_mcp extends Ab_McpBase
{
	var $base;			// the base url for this module			
	var $form_base;		// base url for forms
    var $cp_theme_path;
	var $module_name = "simple_registration";

    private $triggers = array('on_signup' => 'Signup', 'on_activation' => 'Activation');

	public function __construct( $switch = TRUE )
	{
        parent::__construct($switch);

        $this->cp_theme_path = $this->EE->config->slash_item('theme_folder_url').'third_party/simple_registration/';

        // Make a local reference to the ExpressionEngine super object 
		$this->base	 	 = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;
		$this->form_base = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.$this->module_name;

		// uncomment this if you want navigation buttons at the top
        $this->EE->cp->set_right_nav(array(
				lang('tab_welcome') => $this->base,
                lang('tab_settings') => $this->base.AMP.'method=settings',
                lang('tab_form_code') => $this->base.AMP.'method=form_code',
                lang('tab_email_settings') => $this->base.AMP.'method=email_settings',
                lang('tab_signup_keys') => $this->base.AMP.'method=signup_keys',
                lang('tab_docs') => 'http://ee.bybjorn.com/simple_registration',
				
			));

        $this->EE->cp->add_to_head('<link type="text/css" rel="stylesheet" href="'.$this->cp_theme_path.'css/simple_registration.css" />');

        // load the javascript needed for the control panel
        $this->EE->cp->add_to_foot('<script type="text/javascript" src="?D=cp&C=addons_modules&M=show_module_cp&module='.$this->module_name.'&method=load_module_js&rand='.md5(time()).'"></script>');

        // load libraries
        $this->EE->load->library('abprefs', array('module_name' => $this->module_name));

        // init if we do not have preferences for this site
        if(!$this->EE->abprefs->has_preferences())
        {
            $this->EE->abprefs->init_site(Const_Simple_Registration::$default_preferences); // init preferences for this site
            $this->EE->abprefs->fetch_preferences();
        }

	}


    /**
     * This function will load the javascript required for the module
     */
    public function load_module_js()
    {
        $js = file_get_contents(PATH_THIRD.'/'.$this->module_name.'/js/simple_registration.js');

        $disable_arr = array(
                'username_equals_screen_name' => 'screen_name',
                'do_not_require_email_confirmation' => 'confirm_email',
                'do_not_require_accept_terms' => 'terms',
                'do_not_require_password_confirmation' => 'confirm_password',
                'use_email_for_username' => 'username',
                'auto_generate_password' => 'password',
        );

        $add_js = '';
        foreach($disable_arr as $setting => $input_element)
        {
            if($this->EE->abprefs->get($setting))
            {
                $add_js .= "\ndisableOption('".$input_element."');";
            }
        }

        $js = str_replace('{{magick}}', $add_js, $js);
        die($js);
    }

    public function index()
    {
        $vars = array();
        $vars['preferences'] = $this->EE->abprefs->get_preferences();
        return $this->content_wrapper('welcome', 'welcome', $vars);
    }

    public function settings()
    {
        $vars = array();
        $vars['preferences'] = $this->EE->abprefs->get_preferences();
        return $this->content_wrapper('registration_form', 'title_registration_form', $vars);
    }

    public function form_code()
    {
        $vars = array();
        $vars['preferences'] = $this->EE->abprefs->get_preferences();
        $vars['use_membership_captcha'] = ($this->EE->config->item('use_membership_captcha') == 'y');

        return $this->content_wrapper('form_code', 'title_form_code', $vars);
    }

    public function email_settings()
    {
        $vars['preferences'] = $this->EE->abprefs->get_preferences();
        return $this->content_wrapper('email_settings', 'email_settings', $vars);
    }

    public function add_signup_key()
    {
        $member_group_id = $this->EE->input->post('member_group_id');
        if($member_group_id)
        {
            $signup_key = md5(rand().time().$member_group_id);
            $this->EE->db->insert('simple_registration_signup_keys', array(
                'site_id' => $this->EE->config->item('site_id'),
                'signup_key' => $signup_key,
                'trigger_event' => (($this->EE->config->item('req_mbr_activation') == 'email') ? 'on_activation' : 'on_signup'),    // trigger on_activation default if activation is enabled
                'member_group_id' => $member_group_id));
        }
        $this->EE->session->set_flashdata('message_success', lang('signup_key_added'));
        $this->EE->functions->redirect($this->base.AMP.'method=signup_keys');
    }

    public function delete_signup_key()
    {
        $key = $this->EE->input->get('key');
        if($key)
        {
            $this->EE->db->where('signup_key', $key);
            $this->EE->db->delete('simple_registration_signup_keys');
            $this->EE->session->set_flashdata('message_success', lang('signup_key_deleted'));
        }
        $this->EE->functions->redirect($this->base.AMP.'method=signup_keys');
    }

    public function update_signup_keys()
    {
        $this->EE->load->library('srlib');
        $keys = $this->EE->srlib->get_signup_keys();
        foreach($keys as $signup_key => $signup_key_arr)
        {
            $new_member_group = $this->EE->input->post('key_'.$signup_key_arr['signup_key_id']);
            $new_trigger = $this->EE->input->post('trigger_'.$signup_key_arr['signup_key_id']);
            if($new_member_group)
            {
                $this->EE->db->where('signup_key', $signup_key);
                $this->EE->db->update('simple_registration_signup_keys', array('member_group_id' => $new_member_group, 'trigger_event' => $new_trigger));
            }
        }
        $this->EE->session->set_flashdata('message_success', lang('signup_keys_saved'));
        $this->EE->functions->redirect($this->base.AMP.'method=signup_keys');
    }

    public function signup_keys()
    {
        $this->EE->load->library('table');
        $this->EE->load->library('srlib');
        $vars['preferences'] = $this->EE->abprefs->get_preferences();
        $vars['signup_keys'] = $this->EE->srlib->get_signup_keys();
        $vars['triggers'] = $this->triggers;

        $this->EE->db->order_by('group_id', 'desc');
        $mg = $this->EE->db->get_where('member_groups', array('site_id' => $this->EE->config->item('site_id')));
        $member_groups = array();
        foreach($mg->result() as $member_group)
        {
            $member_groups[$member_group->group_id] = $member_group->group_title;
        }
        $vars['member_groups'] = $member_groups;

        $this->EE->jquery->tablesorter('.mainTable', '{
            widgets: ["zebra"]
        }');

        $this->EE->javascript->compile();

        return $this->content_wrapper('signup_keys', 'signup_keys', $vars);
    }

    public function save_email_settings()
    {
        $this->EE->abprefs->set('email_password_on_registration', ($this->EE->input->post('email_password_on_registration') == '1'));
        $this->EE->abprefs->set('password_email', $this->EE->input->post('password_email'));
        $this->EE->abprefs->set('password_subject', $this->EE->input->post('password_subject'));
        $this->EE->abprefs->save_preferences();

        $this->EE->functions->redirect($this->base.AMP.'method=email_settings');
    }

    function save_settings()
    {
        $save_arr = array(
                'username_equals_screen_name',
                'do_not_require_email_confirmation',
                'do_not_require_accept_terms',
                'do_not_require_password_confirmation',
                'use_email_for_username',
                'auto_generate_password',
        );

        foreach($save_arr as $setting_name)
        {
            $value = ($this->EE->input->post($setting_name) != 1);
            $this->EE->abprefs->set($setting_name, $value );
        }
                
        // updating preference settings
        $this->EE->abprefs->save_preferences();
        $this->EE->functions->redirect($this->base.AMP.'method=form_code');
    }
	
	function content_wrapper($content_view, $lang_key, $vars = array())
	{
		$vars['content_view'] = $content_view;
		$vars['_base'] = $this->base;
		$vars['_form_base'] = $this->form_base;
        $vars['cp_theme_path'] = $this->cp_theme_path;
		$this->EE->cp->set_variable('cp_page_title', lang($lang_key));
		$this->EE->cp->set_breadcrumb($this->base, lang('simple_registration_module_name'));

		return $this->EE->load->view('_wrapper', $vars, TRUE);
	}
	
}

/* End of file mcp.simple_registration.php */ 
/* Location: ./system/expressionengine/third_party/simple_registration/mcp.simple_registration.php */ 