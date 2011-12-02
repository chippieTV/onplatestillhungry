<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Channel Images Control Panel Class
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2010 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#control_panel_file
 */
class Channel_images_mcp
{
	/**
	 * Views Data
	 * @var array
	 * @access private
	 */
	private $vData = array();

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		// Creat EE Instance
		$this->EE =& get_instance();

		// Load Models & Libraries & Helpers
		$this->EE->load->library('image_helper');

		// Some Globals
		$this->base = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=channel_images';
		$this->vData = array('base_url'	=> $this->base); // Global Views Data Array

		$this->EE->image_helper->define_theme_url();

		$this->mcp_globals();

		// Add Right Top Menu
		$this->EE->cp->set_right_nav(array(
			'ci:regenerate_sizes' 			=> $this->base.'&method=regenerate_sizes',
			'ci:legacy_settings' 			=> $this->base.'&method=legacy_settings',
			'ci:docs' 				=> $this->EE->cp->masked_url('http://www.devdemon.com/channel_images/docs/'),
		));

		$this->site_id = $this->EE->config->item('site_id');

		// Debug
		//$this->EE->db->save_queries = TRUE;
		//$this->EE->output->enable_profiler(TRUE);
	}

	// ********************************************************************************* //

	public function index()
	{
		// Page Title & BreadCumbs
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ci:home'));

		return $this->EE->load->view('mcp_index', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function regenerate_sizes()
	{
		// Page Title & BreadCumbs
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ci:regenerate_sizes'));

		// Grab all fieldtypes
		$this->vData['fields'] = array();

		$this->EE->db->select('field_id, field_label');
		$this->EE->db->where('site_id', $this->site_id);
		$this->EE->db->where('field_type', 'channel_images');
		$query = $this->EE->db->get('exp_channel_fields');
		foreach ($query->result() as $row) $this->vData['fields'][$row->field_id] = $row->field_label;

		return $this->EE->load->view('mcp_regenerate_sizes', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function legacy_settings()
	{
		// Page Title & BreadCumbs
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('ci:legacy_settings'));

		$this->EE->load->helper('path');

		// Channels
		$this->vData['channels'] = array();
		$this->EE->db->select('channel_id, channel_title');
		$this->EE->db->where('site_id', $this->site_id);
		$query = $this->EE->db->get('exp_channels');
		foreach ($query->result() as $row) $this->vData['channels'][$row->channel_id] = $row->channel_title;

		// Settings
		$this->EE->db->select('settings');
		$this->EE->db->where('module_name', 'Channel_images');
		$query = $this->EE->db->get('exp_modules');
		$this->vData['settings'] = unserialize( $query->row('settings') );
		$this->vData['settings'] = (isset($this->vData['settings']['site_id:'.$this->site_id]) == TRUE) ? $this->vData['settings']['site_id:'.$this->site_id] : array( 'channels' => array() );

		return $this->EE->load->view('mcp_legacy_settings', $this->vData, TRUE);
	}

	// ********************************************************************************* //

	public function mcp_globals()
	{
		$this->EE->cp->set_breadcrumb($this->base, $this->EE->lang->line('channel_images'));

		$this->EE->cp->add_js_script(array('plugin' => 'fancybox'));
		$this->EE->cp->add_to_head('<link type="text/css" rel="stylesheet" href="'.BASE.AMP.'C=css'.AMP.'M=fancybox" />');


		// Add Global JS & CSS & JS Scripts
		$this->EE->image_helper->mcp_meta_parser('gjs', '', 'ChannelImages');
		$this->EE->image_helper->mcp_meta_parser('css', CHANNELIMAGES_THEME_URL . 'channel_images_mcp.css', 'ci-pbf');
		//$this->EE->image_helper->mcp_meta_parser('js', CHANNELIMAGES_THEME_URL . 'jquery.editable.js', 'jquery.editable', 'jquery');
		$this->EE->image_helper->mcp_meta_parser('js',  CHANNELIMAGES_THEME_URL . 'channel_images_mcp.js', 'ci-pbf');

	}

	// ********************************************************************************* //

} // END CLASS

/* End of file mcp.channel_images.php */
/* Location: ./system/expressionengine/third_party/channel_images/mcp.channel_images.php */