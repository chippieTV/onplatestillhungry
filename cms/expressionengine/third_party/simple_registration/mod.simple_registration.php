<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('Ab_ModBase')) {
    require PATH_THIRD.'simple_registration/libraries/ab/ab_modbase.php';
}


/**
 * Let's you put a registration form anywhere
 *
 * @package		Simple_registration
 * @subpackage	ThirdParty
 * @category	Modules
 * @author		bjorn
 * @link		http://ee.bybjorn.com/
 */
class Simple_registration extends Ab_ModBase
{

    public function __construct()
    {
        parent::__construct();
        $this->EE->lang->loadfile('simple_registration');
    }

    public function forgot_password()
    {
        $return_to = $this->get_param('return');
        $action = $this->get_param('action');
        $exact_return_url = $this->get_param('exact_return_url');
        $data = array();
        $data['hidden_fields'] = array(
            'ACT'	=> $this->EE->functions->fetch_action_id('Member', 'retrieve_password'),
        );

        if($action)
        {
            $data['action'] = $action;
        }

        if($return_to && !$exact_return_url)
        {
            $data['hidden_fields']['RET'] = $this->EE->functions->create_url($return_to);
        }
        else if($exact_return_url)
        {
            $data['hidden_fields']['RET'] = $exact_return_url;
        }

        $data['id']	= $this->get_param('id', 'forgot_password_form');

        $form_name = $this->get_param('name');
        if($form_name)
        {
            $data['name'] = $form_name;
        }

        $form_class = $this->get_param('class');
        if($form_class)
        {
            $data['class'] = $form_class;
        }


        $form_data = $this->EE->TMPL->tagdata;

        $this->return_data = $this->EE->functions->form_declaration($data)."\n" . $form_data ."</form>";
        return $this->return_data;
    }

	/**
	 * Generate a registration form
	 */
	public function form()
	{
        if ($this->EE->config->item('allow_member_registration') == 'n')
        {
            $data = array(	'title' 	=> $this->EE->lang->line('simple_registration'),
                            'heading'	=> $this->EE->lang->line('error'),
                            'content'	=> $this->EE->lang->line('sr_registration_not_allowed'),
                            'link'		=> array($this->EE->functions->fetch_site_index(), stripslashes($this->EE->config->item('site_name')))
                         );

            $this->EE->output->show_message($data);
        }

        $action = $this->get_param('action');
        $exact_return_url = $this->get_param('exact_return_url');
		$return_to = $this->get_param('return');
        $ajax = $this->get_param('ajax') == 'yes';

		$skip_success_message = $this->get_param('skip_success_message');
		
		$data = array();
		$data['hidden_fields'] = array(
            'ACT'	=> $this->EE->functions->fetch_action_id('Member', 'register_member'),
            'simple_registration' => 'y',
        );

        if($action)
        {
            $data['action'] = $action;
        }

        if($ajax)
        {
            $data['hidden_fields']['simple_registration_ajax'] = 'y';
        }

        if($return_to && !$exact_return_url)
        {
            $data['hidden_fields']['RET'] = $this->EE->functions->create_url($return_to);
        }
        else if($exact_return_url)
        {
            $data['hidden_fields']['RET'] = $exact_return_url;
        }



        if($skip_success_message == 'y' || $skip_success_message == 'yes')
        {
        	$data['hidden_fields']['skip_success_message'] = 'y';
        }

		$data['id']	= $this->get_param('id', 'register_member_form');

        /**
         * Do we have a honeypot field?
         */
        $honeypot = $this->get_param('honeypot');
        if($honeypot)
        {
            $data['hidden_fields']['simple_registration_hp'] = $honeypot;
        }

        $form_name = $this->get_param('name');
        if($form_name)
        {
            $data['name'] = $form_name;
        }

        $form_class = $this->get_param('class');
        if($form_class)
        {
            $data['class'] = $form_class;
        }
	
		$reg_form = $this->EE->TMPL->tagdata;

        if ($this->EE->config->item('use_membership_captcha') == 'y')
        {
            $captcha_image = '';
            if($this->EE->session->userdata('member_id') != 0)
            {
                $captcha_image = '(CAPTCHA only generated for logged out users)';
            }
            else
            {
                $captcha_image = $this->EE->functions->create_captcha();
            }
            $reg_form = preg_replace("/{captcha}/", $captcha_image, $reg_form);
		}

		$this->return_data = $this->EE->functions->form_declaration($data)."\n" . $reg_form ."</form>";
		return $this->return_data;		
	}
    
}

/* End of file mod.simple_registration.php */ 
/* Location: ./system/expressionengine/third_party/simple_registration/mod.simple_registration.php */ 