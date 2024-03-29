<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * Channel Images Module Tags
 *
 * @package			DevDemon_ChannelImages
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2011 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#core_module_file
 */
class Channel_images
{

	/**
	 * Constructor
	 *
	 * @access public
	 *
	 * Calls the parent constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->site_id = $this->EE->config->item('site_id');
		$this->EE->load->library('image_helper');
		$this->EE->load->model('channel_images_model');
		$this->EE->config->load('ci_config');
	}

	// ********************************************************************************* //

	public function images()
	{
		// Variable prefix
		$this->prefix = $this->EE->TMPL->fetch_param('prefix', 'image') . ':';

		// Group By Category? Whole other parsing scheme
		if ($this->EE->TMPL->fetch_param('group_by_category') != FALSE) return $this->grouped_images(TRUE);

		//----------------------------------------
		// Limit Results?
		//----------------------------------------
		$limit = ($this->EE->image_helper->is_natural_number($this->EE->TMPL->fetch_param('limit')) != FALSE) ? $this->EE->TMPL->fetch_param('limit') : 30;
		if (strpos($this->EE->TMPL->tagdata, LD.'/'."{$this->prefix}paginate".RD) === FALSE) $this->EE->db->limit($limit);

		//----------------------------------------
		// Sort
		//----------------------------------------
		$sort = ($this->EE->TMPL->fetch_param('sort') == 'desc' ) ? 'DESC': 'ASC';

		//----------------------------------------
		// Order by?
		// (only if primary_only is false, since this would override our orderby)
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('cover_only') == FALSE)
		{
			if ($this->EE->TMPL->fetch_param('orderby') == 'title') $this->EE->db->order_by('title', $sort);
			if ($this->EE->TMPL->fetch_param('orderby') == 'random') $this->EE->db->order_by('RAND()', FALSE);
			else $this->EE->db->order_by('image_order', $sort);
		}

		//----------------------------------------
		// Category
		//----------------------------------------
		$cat = $this->EE->TMPL->fetch_param('category');

		if ($cat != FALSE)
		{
			// Multiple Categories?
			if (strpos($cat, '|') !== FALSE)
			{
				$cats = explode('|', $cat);
				$this->EE->db->where_in('category', $cats);
			}
			else
			{
				$this->EE->db->where('category', $cat);
			}
		}

		//----------------------------------------
		// Offset
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('offset') != FALSE && $this->EE->image_helper->is_natural_number($this->EE->TMPL->fetch_param('offset')) != FALSE)
		{
			$this->EE->db->limit($limit, $this->EE->TMPL->fetch_param('offset'));
		}

		//----------------------------------------
		// Do we need to skip the cover image?
		//----------------------------------------
        if ($this->EE->TMPL->fetch_param('skip_cover') != FALSE)
        {
        	$this->EE->db->where('cover', 0);
        }

        //----------------------------------------
		// Cover Image
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('cover_only') != FALSE && $this->EE->TMPL->fetch_param('force_cover') != 'yes')
		{
			$this->EE->db->limit(1);
			$this->EE->db->order_by('cover DESC, image_order ASC');
		}
		else if ($this->EE->TMPL->fetch_param('force_cover') == 'yes')
		{
			$this->EE->db->where('cover', 1);
		}

		//----------------------------------------
		// Image ID?
		//----------------------------------------
		$image_id = $this->EE->TMPL->fetch_param('image_id');

		if ($image_id != FALSE)
		{
			// Multiple File ID?
			if (strpos($image_id, '|') !== FALSE)
			{
				$ids = explode('|', $image_id);
				$this->EE->db->where_in('image_id', $ids);
				$image_id = TRUE;
			}
			else
			{
				$this->EE->db->limit(1);
				$this->EE->db->where('image_id', $this->EE->TMPL->fetch_param('image_id'));
				$image_id = TRUE;
			}
		}

		//----------------------------------------
		// URL Title
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('image_url_title') != FALSE)
		{
			$this->EE->db->limit(1);
			$this->EE->db->where('url_title', $this->EE->TMPL->fetch_param('image_url_title'));
		}

		//----------------------------------------
		// Entry ID ?
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('entry_id') != FALSE)
		{
			$this->EE->db->where('entry_id', $this->EE->TMPL->fetch_param('entry_id'));
		}

		//----------------------------------------
		// URL Title
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('url_title') != FALSE)
		{
			$entry_id = 9999999;
			$query = $this->EE->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".$this->EE->TMPL->fetch_param('url_title')."' LIMIT 1");
			if ($query->num_rows() > 0) $entry_id = $query->row('entry_id');
			$this->EE->db->where('entry_id', $entry_id);
		}

		//----------------------------------------
		// Channel ID?
		//----------------------------------------
		$channel_id = $this->EE->image_helper->get_channel_id_from_param();

		if (is_array($channel_id) == TRUE)
		{
			$this->EE->db->where_in('channel_id', $channel_id);
		}
		elseif ($channel_id != FALSE)
		{
			$this->EE->db->where('channel_id', $channel_id);
		}

		// Temp vars
		$OUT = '';

		//----------------------------------------
		// Shoot the Query
		//----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_channel_images');
		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No images found.");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_images', $this->EE->TMPL->tagdata);
		}

		$images = $query->result();
		$query->free_result();

		$total_images = count($images);

		//----------------------------------------
		// Pagination
		//----------------------------------------
		if (preg_match('/'.LD."{$this->prefix}paginate(.*?)".RD."(.+?)".LD.'\/'."{$this->prefix}paginate".RD."/s", $this->EE->TMPL->tagdata, $match))
		{
			// Pagination variables
			$paginate		= TRUE;
			$paginate_data	= $match['2'];
			$current_page	= 0;
			$total_pages	= 1;
			$qstring		= $this->EE->uri->query_string;
			$uristr			= $this->EE->uri->uri_string;
			$pagination_links = '';
			$page_previous = '';
			$page_next = '';

			// We need to strip the page number from the URL for two reasons:
			// 1. So we can create pagination links
			// 2. So it won't confuse the query with an improper proper ID

			if (preg_match("#(^|/)CI(\d+)(/|$)#", $qstring, $match))
			{
				$current_page = $match['2'];
				$uristr  = $this->EE->functions->remove_double_slashes(str_replace($match['0'], '/', $uristr));
				$qstring = trim($this->EE->functions->remove_double_slashes(str_replace($match['0'], '/', $qstring)), '/');
			}

			// Remove the {paginate}
			$this->EE->TMPL->tagdata = preg_replace("/".LD."{$this->prefix}paginate.*?".RD.".+?".LD.'\/'."{$this->prefix}paginate".RD."/s", "", $this->EE->TMPL->tagdata);

			// What is the current page?

			$current_page = ($current_page == '' OR ($limit > 1 AND $current_page == 1)) ? 0 : $current_page;

			if ($current_page > $total_images)
			{
				$current_page = 0;
			}

			$t_current_page = floor(($current_page / $limit) + 1);
			$total_pages	= intval(floor($total_images / $limit));

			if ($total_images % $limit) $total_pages++;

			if ($total_images > $limit)
			{
				$this->EE->load->library('pagination');

				$deft_tmpl = '';

				if ($uristr == '')
				{
					if ($this->EE->config->item('template_group') == '')
					{
						$this->EE->db->select('group_name');
						$query = $this->EE->db->get_where('template_groups', array('is_site_default' => 'y'));

						$deft_tmpl = $query->row('group_name') .'/index';
					}
					else
					{
						$deft_tmpl  = $this->EE->config->item('template_group').'/';
						$deft_tmpl .= ($this->EE->config->item('template') == '') ? 'index' : $this->EE->config->item('template');
					}
				}

				$basepath = $this->EE->functions->remove_double_slashes($this->EE->functions->create_url($uristr, FALSE).'/'.$deft_tmpl);

				if ($this->EE->TMPL->fetch_param('paginate_base'))
				{
					// Load the string helper
					$this->EE->load->helper('string');

					$pbase = trim_slashes($this->EE->TMPL->fetch_param('paginate_base'));

					$pbase = str_replace("/index", "/", $pbase);

					if ( ! strstr($basepath, $pbase))
					{
						$basepath = $this->EE->functions->remove_double_slashes($basepath.'/'.$pbase);
					}
				}

				// Load Language
				$this->EE->lang->loadfile('channel_images');

				$config['first_url'] 	= rtrim($basepath, '/');
				$config['base_url']		= $basepath;
				$config['prefix']		= 'CI';
				$config['total_rows'] 	= $total_images;
				$config['per_page']		= $limit;
				$config['cur_page']		= $current_page;
				$config['suffix']		= '';
				$config['first_link'] 	= $this->EE->lang->line('ci:pag_first_link');
				$config['last_link'] 	= $this->EE->lang->line('ci:pag_last_link');
				$config['full_tag_open']		= '<span class="ci_paginate_links">';
				$config['full_tag_close']		= '</span>';
				$config['first_tag_open']		= '<span class="ci_paginate_first">';
				$config['first_tag_close']		= '</span>&nbsp;';
				$config['last_tag_open']		= '&nbsp;<span class="ci_paginate_last">';
				$config['last_tag_close']		= '</span>';
				$config['cur_tag_open']			= '&nbsp;<strong class="ci_paginate_current">';
				$config['cur_tag_close']		= '</strong>';
				$config['next_tag_open']		= '&nbsp;<span class="ci_paginate_next">';
				$config['next_tag_close']		= '</span>';
				$config['prev_tag_open']		= '&nbsp;<span class="ci_paginate_prev">';
				$config['prev_tag_close']		= '</span>';
				$config['num_tag_open']			= '&nbsp;<span class="ci_paginate_num">';
				$config['num_tag_close']		= '</span>';

				// Allows $config['cur_page'] to override
				$config['uri_segment'] = 0;

				$this->EE->pagination->initialize($config);
				$pagination_links = $this->EE->pagination->create_links();

				if ((($total_pages * $limit) - $limit) > $current_page)
				{
					$page_next = $basepath.$config['prefix'].($current_page + $limit).'/';
				}

				if (($current_page - $limit ) >= 0)
				{
					$page_previous = $basepath.$config['prefix'].($current_page - $limit).'/';
				}
			}
			else
			{
				$current_page = 0;
			}

			$images = array_slice($images, $current_page, $limit);
		}
		else
		{
			$paginate	= FALSE;
		}

		//----------------------------------------
		// Check for filesize
		// (only for Local) Since it's an expensive operation
		//----------------------------------------
		$this->parse_filesize = FALSE;
		if (strpos($this->EE->TMPL->tagdata, LD.$this->prefix.'filesize') !== FALSE)
		{
			$this->parse_filesize = TRUE;
		}

		//----------------------------------------
		// Check for image_dimensions
		// (only for Local) Since it's an expensive operation
		//----------------------------------------
		$this->parse_dimensions = FALSE;
		if (strpos($this->EE->TMPL->tagdata, LD.$this->prefix.'width') !== FALSE OR strpos($this->EE->TMPL->tagdata, LD.$this->prefix.'height') !== FALSE)
		{
			$this->parse_dimensions = TRUE;
		}

		//----------------------------------------
		// Switch=""
		//----------------------------------------
		$parse_switch = FALSE;
		$switch_matches = array();
		if ( preg_match_all( "/".LD."({$this->prefix}switch\s*=.+?)".RD."/is", $this->EE->TMPL->tagdata, $switch_matches ) > 0 )
		{
			$parse_switch = TRUE;

			// Loop over all matches
			foreach($switch_matches[0] as $key => $match)
			{
				$switch_vars[$key] = $this->EE->functions->assign_parameters($switch_matches[1][$key]);
				$switch_vars[$key]['original'] = $switch_matches[0][$key];
			}
		}

		//----------------------------------------
		// Locked URL?
		//----------------------------------------
		$this->locked_url = FALSE;
		if ( strpos($this->EE->TMPL->tagdata, $this->prefix.'locked_url') !== FALSE)
		{
			$this->locked_url = TRUE;

			// IP
			$this->IP = $this->EE->input->ip_address();

			// Grab Router URL
			$this->locked_act_url = $this->EE->image_helper->get_router_url('url', 'locked_image_url');
		}

		//----------------------------------------
		// SSL?
		//----------------------------------------
		$this->IS_SSL = $this->EE->image_helper->is_ssl();

		//----------------------------------------
		// Performance :)
		//----------------------------------------
		if (isset($this->EE->session->cache['ChannelImages']['Location']) == FALSE)
		{
			$this->EE->session->cache['ChannelImages']['Location'] = array();
		}

		$this->LOCS &= $this->EE->session->cache['ChannelImages']['Location'];

		// Another Check, just to be sure
		if (is_array($this->LOCS) == FALSE) $this->LOCS = array();

		//----------------------------------------
		// Loop over all Images
		//----------------------------------------
		foreach ($images as $count => $image)
		{
			$temp = '';

			// Check for linked image!
			if ($image->link_entry_id > 0)
			{
				$image->entry_id = $image->link_entry_id;
				$image->field_id = $image->link_field_id;
			}

			// Get Field Settings!
			$settings = $this->EE->channel_images_model->get_field_settings($image->field_id);
			$settings = $settings['channel_images'];

			//----------------------------------------
			// Load Location
			//----------------------------------------
			if (isset($this->LOCS[$image->field_id]) == FALSE)
			{
				$location_type = $settings['upload_location'];
				$location_class = 'CI_Location_'.$location_type;
				$location_settings = $settings['locations'][$location_type];

				// Load Main Class
				if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

				// Try to load Location Class
				if (class_exists($location_class) == FALSE)
				{
					$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
					require $location_file;
				}

				// Init!
				$this->LOCS[$image->field_id] = new $location_class($location_settings);
			}

			//----------------------------------------
			// Check for Mime Type
			//----------------------------------------
			if ($image->mime == FALSE)
			{
				// Mime type
				$image->mime = 'image/jpeg';
				if ($image->extension == 'png') $filemime = 'image/png';
				elseif ($image->extension == 'gif') $filemime = 'image/gif';
			}

			//----------------------------------------
			// Image URL
			//----------------------------------------
			$image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

			// Did something go wrong?
			if ($image_url == FALSE)
			{
				$this->EE->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
				continue;
			}

			// SSL?
			if ($this->IS_SSL == TRUE)
			{
				$image_url = str_replace('http://', 'https://', $image_url);
			}

			//----------------------------------------
			// Filedir (local only)
			//----------------------------------------
			$filedir = '';
			if ($settings['upload_location'] == 'local')
			{
				$filedir = str_replace($image->entry_id.'/'.$image->filename, '', $image_url);
			}

			$vars = array();
			$vars[$this->prefix.'count'] = $count + 1;
			$vars[$this->prefix.'total'] = $total_images;
			$vars[$this->prefix.'entry_id'] = $image->entry_id;
			$vars[$this->prefix.'channel_id'] = $image->channel_id;
			$vars[$this->prefix.'title'] = $image->title;
			$vars[$this->prefix.'url_title'] = $image->url_title;
			$vars[$this->prefix.'description'] = $image->description;
			$vars[$this->prefix.'category'] = $image->category;
			$vars[$this->prefix.'filename'] = $image->filename;
			$vars[$this->prefix.'id'] = $image->image_id;
			$vars[$this->prefix.'upload_date'] = $image->upload_date;
			$vars[$this->prefix.'url'] = $image_url;
			$vars[$this->prefix.'secure_url'] = $image_url;
			$vars[$this->prefix.'file_path'] = $filedir;
			$vars[$this->prefix.'file_path_secure'] = str_replace('http://', 'https://', $filedir);
			$vars[$this->prefix.'mimetype'] = $image->mime;
			$vars[$this->prefix.'field:1'] = $image->cifield_1;
			$vars[$this->prefix.'field:2'] = $image->cifield_2;
			$vars[$this->prefix.'field:3'] = $image->cifield_3;
			$vars[$this->prefix.'field:4'] = $image->cifield_4;
			$vars[$this->prefix.'field:5'] = $image->cifield_5;


			//----------------------------------------
			// Check for filesize, Since it's an expensive operation
			//----------------------------------------
			if ($this->parse_filesize == TRUE)
			{
				// If filesize is not defined, lets find it (only for local files)
				if ($image->filesize == FALSE && $settings['upload_location'] == 'local')
				{
					$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
					$filepath = $filepath['path']  . $image->entry_id . '/' . $image->filename;
					$image->filesize = @filesize($filepath);
				}
				elseif ($image->filesize == FALSE)
				{
					$image->filesize = 0;
				}

				$vars[$this->prefix.'filesize'] = $this->EE->image_helper->format_bytes($image->filesize);
				$vars[$this->prefix.'filesize_bytes'] = $image->filesize;
			}

			//----------------------------------------
			// Check for image_dimensions, Since it's an expensive operation
			//----------------------------------------
			if ($this->parse_dimensions == TRUE)
			{
				// If filesize is not defined, lets find it (only for local files)
				if ($image->width == FALSE && $settings['upload_location'] == 'local')
				{
					$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
					$filepath = $filepath['path']  . $image->entry_id . '/' . $image->filename;
					$imginfo = @getimagesize($filepath);
					$image->width = $imginfo[0];
					$image->height = $imginfo[1];
				}
				elseif ($image->width == FALSE)
				{
					$image->width = '';
					$image->height = '';
				}

				$vars[$this->prefix.'width'] = $image->width;
				$vars[$this->prefix.'height'] = $image->height;
			}

			// -----------------------------------------
			// Locked URL
			// -----------------------------------------
			if ($this->locked_url == TRUE)
			{
				$locked = array('image_id' => $image->image_id, 'size'=>'', 'time' => $this->EE->localize->now + 600, 'ip' => $this->IP);
				$vars[$this->prefix.'locked_url'] = $this->locked_act_url . '&key=' . base64_encode(serialize($locked));
			}


			$temp = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $vars);
			$temp = $this->parse_size_vars($temp, $settings, $image);

			// -----------------------------------------
			// Parse Switch {switch="one|twoo"}
			// -----------------------------------------
			if ($parse_switch)
			{
				// Loop over all switch variables
				foreach($switch_vars as $switch)
				{
					$sw = '';

					// Does it exist? Just to be sure
					if ( isset( $switch[$this->prefix.'switch'] ) !== FALSE )
					{
						$sopt = explode("|", $switch[$this->prefix.'switch']);
						$sw = $sopt[(($count) + count($sopt)) % count($sopt)];
					}

					$temp = str_replace($switch['original'], $sw, $temp);
				}
			}

			$OUT .= $temp;
		}

		//----------------------------------------
		// Add pagination to result
		//----------------------------------------
		if ($paginate == TRUE)
		{
			$paginate_data = str_replace(LD.$this->prefix.'current_page'.RD, 	$t_current_page, 	$paginate_data);
			$paginate_data = str_replace(LD.$this->prefix.'total_pages'.RD,		$total_pages,  		$paginate_data);
			$paginate_data = str_replace(LD.$this->prefix.'pagination_links'.RD,	$pagination_links,	$paginate_data);

			if (preg_match("/".LD."if {$this->prefix}previous_page".RD."(.+?)".LD.'\/'."if".RD."/s", $paginate_data, $match))
			{
				if ($page_previous == '')
				{
					 $paginate_data = preg_replace("/".LD."if {$this->prefix}previous_page".RD.".+?".LD.'\/'."if".RD."/s", '', $paginate_data);
				}
				else
				{
					$match['1'] = str_replace(array(LD."{$this->prefix}path".RD, LD."{$this->prefix}auto_path".RD), $page_previous, $match['1']);

					$paginate_data = str_replace($match['0'], $match['1'], $paginate_data);
				}
			}

			if (preg_match("/".LD."if {$this->prefix}next_page".RD."(.+?)".LD.'\/'."if".RD."/s", $paginate_data, $match))
			{
				if ($page_next == '')
				{
					 $paginate_data = preg_replace("/".LD."if {$this->prefix}next_page".RD.".+?".LD.'\/'."if".RD."/s", '', $paginate_data);
				}
				else
				{
					$match['1'] = str_replace(array(LD."{$this->prefix}path".RD, LD."{$this->prefix}auto_path".RD), $page_next, $match['1']);

					$paginate_data = str_replace($match['0'], $match['1'], $paginate_data);
				}
			}

			$position = ( ! $this->EE->TMPL->fetch_param('paginate')) ? '' : $this->EE->TMPL->fetch_param('paginate');

			switch ($position)
			{
				case "top"	: $OUT  = $paginate_data.$OUT;
					break;
				case "both"	: $OUT  = $paginate_data.$OUT.$paginate_data;
					break;
				default		: $OUT .= $paginate_data;
					break;
			}
		}

		//$final = $this->_file_parse($images, $this->EE->TMPL->tagdata);

		// Apply Backspace
		$backspace = ($this->EE->TMPL->fetch_param('backspace') != FALSE) ? $this->EE->TMPL->fetch_param('backspace') : 0;
		$OUT = ($backspace > 0) ? substr($OUT, 0, - $backspace): $OUT;

		return $OUT;

	}

	// ********************************************************************************* //

	function images_static()
	{
		// Variable prefix
		$this->prefix = $this->EE->TMPL->fetch_param('prefix', 'image') . ':';

		// Entry ID
		$this->entry_id = $this->EE->image_helper->get_entry_id_from_param();

		// We need an entry_id
		if ($this->entry_id == FALSE)
		{
			$this->EE->TMPL->log_item('CHANNEL IMAGES: Entry ID could not be resolved');
			return $this->EE->TMPL->tagdata;
		}

		// Temp vars
		$final = '';

		// IMG Tag Prefix
		$img_prefix = $this->EE->TMPL->fetch_param('img_prefix', '');

		// IMG Tag Suffix
		$img_suffix = $this->EE->TMPL->fetch_param('img_suffix', '');

		// Do we have an category?
		if ($this->EE->TMPL->fetch_param('category') != FALSE) $this->EE->db->where('category', $this->EE->TMPL->fetch_param('category'));

		// Do we need to offset?
		if ($this->EE->TMPL->fetch_param('offset') != FALSE && $this->EE->image_helper->is_natural_number($this->EE->TMPL->fetch_param('offset')) != FALSE)
		{
			$this->EE->db->limit(9999, $this->EE->TMPL->fetch_param('offset'));
		}

		// Do we need to skip the cover image?
        if ($this->EE->TMPL->fetch_param('skip_cover') != FALSE)
        {
        	$this->EE->db->where('cover', 0);
        }

		// Shoot the query
		$this->EE->db->select('*');
		$this->EE->db->from('exp_channel_images');
		$this->EE->db->where('entry_id', $this->entry_id);
		$this->EE->db->order_by('image_order');
		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No images found. (Entry_ID:{$this->entry_id})");
			return $this->EE->TMPL->tagdata;
		}
		$images = $query->result();

		//----------------------------------------
		// SSL?
		//----------------------------------------
		$this->IS_SSL = $this->EE->image_helper->is_ssl();

		//----------------------------------------
		// Performance :)
		//----------------------------------------
		if (isset($this->EE->session->cache['ChannelImages']['Location']) == FALSE)
		{
			$this->EE->session->cache['ChannelImages']['Location'] = array();
		}

		$this->LOCS &= $this->EE->session->cache['ChannelImages']['Location'];

		// Another Check, just to be sure
		if (is_array($this->LOCS) == FALSE) $this->LOCS = array();

		// Count
		$count = 1;

		// Loop over all images
		foreach ($images as $image)
		{
			// Check for linked image!
			if ($image->link_entry_id > 0)
			{
				$image->entry_id = $image->link_entry_id;
				$image->field_id = $image->link_field_id;
			}

			// Get Field Settings!
			$settings = $this->EE->channel_images_model->get_field_settings($image->field_id);
			$settings = $settings['channel_images'];

			//----------------------------------------
			// Load Location
			//----------------------------------------
			if (isset($this->LOCS[$image->field_id]) == FALSE)
			{
				$location_type = $settings['upload_location'];
				$location_class = 'CI_Location_'.$location_type;
				$location_settings = $settings['locations'][$location_type];

				// Load Main Class
				if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

				// Try to load Location Class
				if (class_exists($location_class) == FALSE)
				{
					$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
					require $location_file;
				}

				// Init!
				$this->LOCS[$image->field_id] = new $location_class($location_settings);
			}

			//----------------------------------------
			// Check for Mime Type
			//----------------------------------------
			if ($image->mime == FALSE)
			{
				// Mime type
				$image->mime = 'image/jpeg';
				if ($image->extension == 'png') $filemime = 'image/png';
				elseif ($image->extension == 'gif') $filemime = 'image/gif';
			}

			//----------------------------------------
			// Image URL
			//----------------------------------------
			$image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

			// Did something go wrong?
			if ($image_url == FALSE)
			{
				$this->EE->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
				continue;
			}

			// SSL?
			if ($this->IS_SSL == TRUE)
			{
				$image_url = str_replace('http://', 'https://', $image_url);
			}

			// Lets parse Description and Title once again in suffix/prefix
			$imgprefix = str_replace(array('{IMG_DESC}', '{IMG_TITLE}', '{IMG_CATEGORY}'), array($image->description, $image->title, $image->category), $img_prefix);
			$imgsuffix = str_replace(array('{IMG_DESC}', '{IMG_TITLE}', '{IMG_CATEGORY}'), array($image->description, $image->title, $image->category), $img_suffix);

			$this->EE->TMPL->tagdata = str_replace(
						array(	LD.$this->prefix.$count.':id'.RD,
								LD.$this->prefix.$count.':title'.RD,
								LD.$this->prefix.$count.':description'.RD,
								LD.$this->prefix.$count.':filename'.RD,
								LD.$this->prefix.$count.':url'.RD,
								LD.$this->prefix.$count.':secure_url'.RD,

								LD.$this->prefix.$count.RD,
							),
						array(	$image->image_id,
								$image->title,
								$image->description,
								$image->filename,
								$image_url,
								$image_url,

								$imgprefix.'<img src="'.$image_url.'" alt="'.$image->description.'">'.$imgsuffix,
							),
					$this->EE->TMPL->tagdata);

			// get the extensions
			$extension = '.' . substr( strrchr($image->filename, '.'), 1);

			// Generate size names
			if (isset($settings['action_groups']) == TRUE AND empty($settings['action_groups']) == FALSE)
			{
				foreach ($settings['action_groups'] as $group)
				{
					$name = strtolower($group['group_name']);
					$newname = str_replace($extension, "__{$name}{$extension}", $image->filename);
					$size_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $newname);
					$this->EE->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':'.$name.RD, $imgprefix.'<img src="'.$size_url.'" alt="'.$image->description.'">'.$imgsuffix, $this->EE->TMPL->tagdata);
					$this->EE->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':url:'.$name.RD, $size_url, $this->EE->TMPL->tagdata);
					$this->EE->TMPL->tagdata = str_replace(LD.$this->prefix.$count.':secure_url:'.$name.RD, $size_url, $this->EE->TMPL->tagdata);
				}
			}

			$count++;
		}

		return $this->EE->TMPL->tagdata;
	}

	// ********************************************************************************* //

	public function grouped_images($legacy=FALSE)
	{
		if ($legacy == FALSE)
		{
			// Variable prefix
			$this->prefix = $this->EE->TMPL->fetch_param('prefix', 'image') . ':';

			// Entry ID
			$this->entry_id = $this->EE->image_helper->get_entry_id_from_param();

			// We need an entry_id
			if ($this->entry_id == FALSE)
			{
				$this->EE->TMPL->log_item('CHANNEL IMAGES: Entry ID could not be resolved');
				return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_images', $this->EE->TMPL->tagdata);
			}
		}


		//----------------------------------------
		// Shoot the Query
		//----------------------------------------
		$this->EE->db->select('*');
		$this->EE->db->from('exp_channel_images');
		$this->EE->db->where('entry_id', $this->entry_id);
		$this->EE->db->order_by('category', 'ASC');
		$this->EE->db->order_by('image_order', 'ASC');
		$query = $this->EE->db->get();

		if ($query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No images found. (Entry_ID:{$this->entry_id})");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_images', $this->EE->TMPL->tagdata);
		}

		//----------------------------------------
		// Make the Images Var
		//----------------------------------------
		$images = $query->result();
		$query->free_result();

		//----------------------------------------
		// Grab the {images} var pair
		//----------------------------------------
		if (isset($this->EE->TMPL->var_pair['images']) == FALSE)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No {images} var pair found.");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_images', $this->EE->TMPL->tagdata);
		}

		$pair_data = $this->EE->image_helper->fetch_data_between_var_pairs('images', $this->EE->TMPL->tagdata);

		//----------------------------------------
		// Loop over all images and make a new arr
		//----------------------------------------
		$categories = array();
		foreach($images as $image)
		{
			if (trim($image->category) == FALSE) continue;
			$categories[ $image->category ][] = $image;
		}
		unset($images);

		//----------------------------------------
		// No Categories?
		//----------------------------------------
		if (empty($categories) == TRUE)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: Found images but no categories.");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_images', $this->EE->TMPL->tagdata);
		}

		//----------------------------------------
		// Sort by Category?
		//----------------------------------------
		if (strtolower($this->EE->TMPL->fetch_param('category_sort')) != 'desc')
			ksort($categories);
		else krsort($categories);

		//----------------------------------------
		// Check for filesize
		// (only for Local) Since it's an expensive operation
		//----------------------------------------
		$this->parse_filesize = FALSE;
		if (strpos($pair_data, LD.$this->prefix.'filesize') !== FALSE)
		{
			$this->parse_filesize = TRUE;
		}

		//----------------------------------------
		// Check for image_dimensions
		// (only for Local) Since it's an expensive operation
		//----------------------------------------
		$this->parse_dimensions = FALSE;
		if (strpos($pair_data, LD.$this->prefix.'width') !== FALSE OR strpos($pair_data, LD.$this->prefix.'height') !== FALSE)
		{
			$this->parse_dimensions = TRUE;
		}

		//----------------------------------------
		// Switch=""
		//----------------------------------------
		$parse_switch = FALSE;
		if ( preg_match( "/".LD."({$this->prefix}switch\s*=.+?)".RD."/is", $pair_data, $switch_matches ) > 0 )
		{
			$parse_switch = TRUE;
			$switch_param = $this->EE->functions->assign_parameters($switch_matches['1']);
		}

		//----------------------------------------
		// Locked URL?
		//----------------------------------------
		$this->locked_url = FALSE;
		if ( strpos($this->EE->TMPL->tagdata, $this->prefix.'locked_url') !== FALSE)
		{
			$this->locked_url = TRUE;

			// IP
			$this->IP = $this->EE->input->ip_address();

			// Grab Router URL
			$this->locked_act_url = $this->EE->image_helper->get_router_url('url', 'locked_image_url');
		}

		//----------------------------------------
		// SSL?
		//----------------------------------------
		$this->IS_SSL = $this->EE->image_helper->is_ssl();

		//----------------------------------------
		// Performance :)
		//----------------------------------------
		if (isset($this->EE->session->cache['ChannelImages']['Location']) == FALSE)
		{
			$this->EE->session->cache['ChannelImages']['Location'] = array();
		}

		$this->LOCS &= $this->EE->session->cache['ChannelImages']['Location'];

		// Another Check, just to be sure
		if (is_array($this->LOCS) == FALSE) $this->LOCS = array();


		$OUT = '';
		//----------------------------------------
		// Loop over the new array and parse
		//----------------------------------------
		foreach ($categories as $cat => $images)
		{
			$CATOUT = str_replace(LD.$this->prefix.'category'.RD, $cat, $this->EE->TMPL->tagdata);
			$CATIMG = '';

			$total_images = count($images);

			//----------------------------------------
			// Loop over all Images
			//----------------------------------------
			foreach ($images as $count => $image)
			{
				$temp = '';

				// Check for linked image!
				if ($image->link_entry_id > 0)
				{
					$image->entry_id = $image->link_entry_id;
					$image->field_id = $image->link_field_id;
				}

				// Get Field Settings!
				$settings = $this->EE->channel_images_model->get_field_settings($image->field_id);
				$settings = $settings['channel_images'];

				//----------------------------------------
				// Load Location
				//----------------------------------------
				if (isset($this->LOCS[$image->field_id]) == FALSE)
				{
					$location_type = $settings['upload_location'];
					$location_class = 'CI_Location_'.$location_type;
					$location_settings = $settings['locations'][$location_type];

					// Load Main Class
					if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

					// Try to load Location Class
					if (class_exists($location_class) == FALSE)
					{
						$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
						require $location_file;
					}

					// Init!
					$this->LOCS[$image->field_id] = new $location_class($location_settings);
				}

				//----------------------------------------
				// Check for Mime Type
				//----------------------------------------
				if ($image->mime == FALSE)
				{
					// Mime type
					$image->mime = 'image/jpeg';
					if ($image->extension == 'png') $filemime = 'image/png';
					elseif ($image->extension == 'gif') $filemime = 'image/gif';
				}

				//----------------------------------------
				// Image URL
				//----------------------------------------
				$image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

				// Did something go wrong?
				if ($image_url == FALSE)
				{
					$this->EE->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
					continue;
				}

				// SSL?
				if ($this->IS_SSL == TRUE)
				{
					$image_url = str_replace('http://', 'https://', $image_url);
				}

				//----------------------------------------
				// Filedir (local only)
				//----------------------------------------
				$filedir = '';
				if ($settings['upload_location'] == 'local')
				{
					$filedir = str_replace($image->entry_id.'/'.$image->filename, '', $image_url);
				}

				$vars = array();
				$vars[$this->prefix.'count'] = $count + 1;
				$vars[$this->prefix.'total'] = $total_images;
				$vars[$this->prefix.'entry_id'] = $image->entry_id;
				$vars[$this->prefix.'channel_id'] = $image->channel_id;
				$vars[$this->prefix.'title'] = $image->title;
				$vars[$this->prefix.'url_title'] = $image->url_title;
				$vars[$this->prefix.'description'] = $image->description;
				$vars[$this->prefix.'category'] = $image->category;
				$vars[$this->prefix.'filename'] = $image->filename;
				$vars[$this->prefix.'id'] = $image->image_id;
				$vars[$this->prefix.'url'] = $image_url;
				$vars[$this->prefix.'secure_url'] = $image_url;
				$vars[$this->prefix.'file_path'] = $filedir;
				$vars[$this->prefix.'file_path_secure'] = str_replace('http://', 'https://', $filedir);
				$vars[$this->prefix.'mimetype'] = $image->mime;
				$vars[$this->prefix.'cover'] = $image->cover;
				$vars[$this->prefix.'field:1'] = $image->cifield_1;
				$vars[$this->prefix.'field:2'] = $image->cifield_2;
				$vars[$this->prefix.'field:3'] = $image->cifield_3;
				$vars[$this->prefix.'field:4'] = $image->cifield_4;
				$vars[$this->prefix.'field:5'] = $image->cifield_5;

				//----------------------------------------
				// Check for filesize, Since it's an expensive operation
				//----------------------------------------
				if ($this->parse_filesize == TRUE)
				{
					// If filesize is not defined, lets find it (only for local files)
					if ($image->filesize == FALSE && $settings['upload_location'] == 'local')
					{
						$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
						$filepath = $filepath['path']  . $image->entry_id . '/' . $image->filename;
						$image->filesize = @filesize($filepath);
					}
					elseif ($image->filesize == FALSE)
					{
						$image->filesize = 0;
					}

					$vars[$this->prefix.'filesize'] = $this->EE->image_helper->format_bytes($image->filesize);
					$vars[$this->prefix.'filesize_bytes'] = $image->filesize;
				}

				//----------------------------------------
				// Check for image_dimensions, Since it's an expensive operation
				//----------------------------------------
				if ($this->parse_dimensions == TRUE)
				{
					// If filesize is not defined, lets find it (only for local files)
					if ($image->width == FALSE && $settings['upload_location'] == 'local')
					{
						$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
						$filepath = $filepath['path']  . $image->entry_id . '/' . $image->filename;
						$imginfo = @getimagesize($filepath);
						$image->width = $imginfo[0];
						$image->height = $imginfo[1];
					}
					elseif ($image->width == FALSE)
					{
						$image->width = '';
						$image->height = '';
					}

					$vars[$this->prefix.'width'] = $image->width;
					$vars[$this->prefix.'height'] = $image->height;
				}

				// -----------------------------------------
				// Locked URL
				// -----------------------------------------
				if ($this->locked_url == TRUE)
				{
					$locked = array('image_id' => $image->image_id, 'size'=>'', 'time' => $this->EE->localize->now + 600, 'ip' => $this->IP);
					$vars[$this->prefix.'locked_url'] = $this->locked_act_url . '&key=' . base64_encode(serialize($locked));
				}

				$temp = $this->EE->TMPL->parse_variables_row($pair_data, $vars);
				$temp = $this->parse_size_vars($temp, $settings, $image);

				// -----------------------------------------
				// Parse Switch {switch="one|twoo"}
				// -----------------------------------------
				if ($parse_switch)
				{
					$sw = '';

					if ( isset( $switch_param[$this->prefix.'switch'] ) !== FALSE )
					{
						$sopt = explode("|", $switch_param[$this->prefix.'switch']);

						$sw = $sopt[($count + count($sopt)) % count($sopt)];
					}

					$temp = $this->EE->TMPL->swap_var_single($switch_matches['1'], $sw, $temp);
				}

				$CATIMG .= $temp;
			}

			$CATOUT = $this->EE->image_helper->swap_var_pairs('images', $CATIMG, $CATOUT);

			$OUT .= $CATOUT;
		}

		return $OUT;
	}

	// ********************************************************************************* //

	public function category_list()
	{
		// Variable prefix
		$this->prefix = $this->EE->TMPL->fetch_param('prefix', 'image') . ':';

		$this->EE->db->select('DISTINCT (category)', FALSE);
		$this->EE->db->from('exp_channel_images');

		//----------------------------------------
		// Entry ID ?
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('entry_id') != FALSE)
		{
			$this->EE->db->where('entry_id', $this->EE->TMPL->fetch_param('entry_id'));
		}

		//----------------------------------------
		// URL Title
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('url_title') != FALSE)
		{
			$entry_id = 9999999;
			$query = $this->EE->db->query("SELECT entry_id FROM exp_channel_titles WHERE url_title = '".$this->EE->TMPL->fetch_param('url_title')."' LIMIT 1");
			if ($query->num_rows() > 0) $entry_id = $query->row('entry_id');
			$this->EE->db->where('url_title', $entry_id);
		}

		//----------------------------------------
		// Channel ID?
		//----------------------------------------
		$channel_id = $this->EE->image_helper->get_channel_id_from_param();

		if (is_array($channel_id) == TRUE)
		{
			$this->EE->db->where_in('channel_id', $channel_id);
		}
		elseif ($channel_id != FALSE)
		{
			$this->EE->db->where('channel_id', $channel_id);
		}

		// Order by
		$this->EE->db->order_by('category', 'ASC');

		$query = $this->EE->db->get();
		//----------------------------------------
		// Parse
		//----------------------------------------
		$OUT = '';

		foreach ($query->result() as $count => $row)
		{
			if ($row->category == FALSE) continue;

			$temp = '';

			$vars = array();
			$vars[$this->prefix.'category_label'] = ucfirst($row->category);
			$vars[$this->prefix.'category'] = $row->category;

			$temp = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $vars);

			$OUT .= $temp;
		}

		return $OUT;
	}

	// ********************************************************************************* //

	public function prev_image()
	{
		return $this->prev_next_image('prev');
	}

	// ********************************************************************************* //

	public function next_image()
	{
		return $this->prev_next_image('next');
	}

	// ********************************************************************************* //

	public function prev_next_image($which='next')
	{
		// Variable prefix
		$this->prefix = $this->EE->TMPL->fetch_param('prefix', 'image') . ':';

		// We need Image ID or Url_title
		if ( $this->EE->TMPL->fetch_param('image_id') == FALSE && $this->EE->TMPL->fetch_param('url_title') == FALSE)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No Image ID or URL Title ({$which})");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_image', $this->EE->TMPL->tagdata);
		}

		$this->EE->db->select('image_id, entry_id, field_id');
		$this->EE->db->from('exp_channel_images');
		if ($this->EE->TMPL->fetch_param('image_id') != FALSE) $this->EE->db->where('image_id', $this->EE->TMPL->fetch_param('image_id'));
		if ($this->EE->TMPL->fetch_param('url_title') != FALSE) $this->EE->db->where('url_title', $this->EE->TMPL->fetch_param('url_title'));
		$this->EE->db->limit(1);
		$query = $this->EE->db->get();

		if ( $query->num_rows() == 0)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No Image found ({$which})");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_image', $this->EE->TMPL->tagdata);
		}

		$image_id = $query->row('image_id');
		$entry_id = $query->row('entry_id');
		$field_id = $query->row('field_id');
		$images = array();

		// Did we cache it?
		if (isset($this->EE->session->cache['ChannelImages']['NextPrev'][$entry_id]) != TRUE)
		{
			// Grab the whole thing
			$this->EE->db->select('*')->from('exp_channel_images')->where('entry_id', $entry_id)->where('field_id', $field_id);
			$this->EE->db->order_by('image_order', 'ASC');
			$query = $this->EE->db->get();

			$this->EE->session->cache['ChannelImages']['NextPrev'][$entry_id] = $query->result();
		}

		$images = $this->EE->session->cache['ChannelImages']['NextPrev'][$entry_id];

		//----------------------------------------
		// Loop over all images
		//----------------------------------------
		$prev = array();
		$next = array();

		foreach($images as $key => $img)
		{
			// Is this it?
			if ($img->image_id == $image_id)
			{
				// Is there a Prev?
				if (isset($images[($key-1)]))
				{
					$prev = $images[($key-1)];
				}

				// Is there a Next?
				if (isset($images[($key+1)]))
				{
					$next = $images[($key+1)];
				}
			}
		}

		//----------------------------------------
		// Parse Image
		//----------------------------------------

		// Which one?
		$image = ($which == 'next') ? $next : $prev;

		if (empty($image) === TRUE)
		{
			$this->EE->TMPL->log_item("CHANNEL IMAGES: No {$which} Image found ({$which})");
			return $this->EE->image_helper->custom_no_results_conditional($this->prefix.'no_image', $this->EE->TMPL->tagdata);
		}

		//----------------------------------------
		// Performance :)
		//----------------------------------------
		if (isset($this->EE->session->cache['ChannelImages']['Location']) == FALSE)
		{
			$this->EE->session->cache['ChannelImages']['Location'] = array();
		}

		$this->LOCS &= $this->EE->session->cache['ChannelImages']['Location'];

		// Another Check, just to be sure
		if (is_array($this->LOCS) == FALSE) $this->LOCS = array();

		// Check for linked image!
		if ($image->link_entry_id > 0)
		{
			$image->entry_id = $image->link_entry_id;
			$image->field_id = $image->link_field_id;
		}

		// Get Field Settings!
		$settings = $this->EE->channel_images_model->get_field_settings($image->field_id);
		$settings = $settings['channel_images'];

		//----------------------------------------
		// Load Location
		//----------------------------------------
		if (isset($this->LOCS[$image->field_id]) == FALSE)
		{
			$location_type = $settings['upload_location'];
			$location_class = 'CI_Location_'.$location_type;
			$location_settings = $settings['locations'][$location_type];

			// Load Main Class
			if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

			// Try to load Location Class
			if (class_exists($location_class) == FALSE)
			{
				$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
				require $location_file;
			}

			// Init!
			$this->LOCS[$image->field_id] = new $location_class($location_settings);
		}

		//----------------------------------------
		// Check for Mime Type
		//----------------------------------------
		if ($image->mime == FALSE)
		{
			// Mime type
			$image->mime = 'image/jpeg';
			if ($image->extension == 'png') $filemime = 'image/png';
			elseif ($image->extension == 'gif') $filemime = 'image/gif';
		}

		//----------------------------------------
		// Image URL
		//----------------------------------------
		$image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $image->filename);

		$vars = array();
		$vars[$this->prefix.'entry_id'] = $image->entry_id;
		$vars[$this->prefix.'channel_id'] = $image->channel_id;
		$vars[$this->prefix.'title'] = $image->title;
		$vars[$this->prefix.'url_title'] = $image->url_title;
		$vars[$this->prefix.'description'] = $image->description;
		$vars[$this->prefix.'category'] = $image->category;
		$vars[$this->prefix.'filename'] = $image->filename;
		$vars[$this->prefix.'id'] = $image->image_id;
		$vars[$this->prefix.'upload_date'] = $image->upload_date;
		$vars[$this->prefix.'url'] = $image_url;
		$vars[$this->prefix.'secure_url'] = $image_url;
		$vars[$this->prefix.'mimetype'] = $image->mime;
		$vars[$this->prefix.'cover'] = $image->cover;
		$vars[$this->prefix.'field:1'] = $image->cifield_1;
		$vars[$this->prefix.'field:2'] = $image->cifield_2;
		$vars[$this->prefix.'field:3'] = $image->cifield_3;
		$vars[$this->prefix.'field:4'] = $image->cifield_4;
		$vars[$this->prefix.'field:5'] = $image->cifield_5;

		// Misc
		$this->IS_SSL = $this->EE->image_helper->is_ssl();
		$this->parse_filesize = FALSE;

		//----------------------------------------
		// Check for image_dimensions, Since it's an expensive operation
		//----------------------------------------
		$this->parse_dimensions = FALSE;
		if (strpos($this->EE->TMPL->tagdata, LD.$this->prefix.'width') !== FALSE OR strpos($this->EE->TMPL->tagdata, LD.$this->prefix.'height') !== FALSE)
		{
			$this->parse_dimensions = TRUE;

			// If filesize is not defined, lets find it (only for local files)
			if ($image->width == FALSE && $settings['upload_location'] == 'local')
			{
				$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
				$filepath = $filepath['path']  . $image->entry_id . '/' . $image->filename;
				$imginfo = @getimagesize($filepath);
				$image->width = $imginfo[0];
				$image->height = $imginfo[1];
			}
			elseif ($image->width == FALSE)
			{
				$image->width = '';
				$image->height = '';
			}

			$vars[$this->prefix.'width'] = $image->width;
			$vars[$this->prefix.'height'] = $image->height;
		}


		// -----------------------------------------
		// Locked URL
		// -----------------------------------------
		$this->locked_url = FALSE;
		if ( strpos($this->EE->TMPL->tagdata, $this->prefix.'locked_url') !== FALSE)
		{
			$this->locked_url = TRUE;
			$this->IP = $this->EE->input->ip_address();
			$this->locked_act_url = $this->EE->image_helper->get_router_url('url', 'locked_image_url');

			$locked = array('image_id' => $image->image_id, 'size'=>'', 'time' => $this->EE->localize->now + 600, 'ip' => $this->IP);
			$vars[$this->prefix.'locked_url'] = $this->locked_act_url . '&key=' . base64_encode(serialize($locked));
		}


		$this->EE->TMPL->tagdata = $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $vars);
		$this->EE->TMPL->tagdata = $this->parse_size_vars($this->EE->TMPL->tagdata, $settings, $image);

		return $this->EE->TMPL->tagdata;
	}

	// ********************************************************************************* //

	public function zip()
	{
		// -----------------------------------------
		// Increase all types of limits!
		// -----------------------------------------
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');
		@ini_set('memory_limit', '96M');
		@ini_set('memory_limit', '128M');
		@ini_set('memory_limit', '160M');
		@ini_set('memory_limit', '192M');

		error_reporting(E_ALL);
		@ini_set('display_errors', 1);

		// What Entry?
		$entry_id = $this->EE->image_helper->get_entry_id_from_param();

		// Filename
		if ($this->EE->TMPL->fetch_param('filename') != FALSE)
		{
			$filename = strtolower($this->EE->security->sanitize_filename(str_replace(' ', '_', $this->EE->TMPL->fetch_param('filename'))));
		}
		else
		{
			$query = $this->EE->db->select('url_title')->from('exp_channel_titles')->where('entry_id', $entry_id)->get();
			$filename = substr($query->row('url_title'), 0 , 50);
		}

		// We need an entry_id
		if ($entry_id == FALSE)
		{
			return $this->return_error('missing_data', 'No entry found! Unable to generate ZIP');
		}

		$this->EE->db->select('*');
		$this->EE->db->from('exp_channel_images');
		$this->EE->db->where('entry_id', $entry_id);

		//----------------------------------------
		// Field ID
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('field_id') != FALSE)
		{
			$this->EE->db->where('field_id', $this->EE->TMPL->fetch_param('field_id'));
		}

		//----------------------------------------
		// Field
		//----------------------------------------
		if ($this->EE->TMPL->fetch_param('field') != FALSE)
		{
			$group = $this->EE->TMPL->fetch_param('field');

			// Multiple Fields
			if (strpos($group, '|') !== FALSE)
			{
				$group = explode('|', $group);
				$groups = array();

				foreach ($group as $name)
				{
					$groups[] = $name;
				}
			}
			else
			{
				$groups = $this->EE->TMPL->fetch_param('field');
			}

			$this->EE->db->join('exp_channel_fields cf', 'cf.field_id = exp_channel_files.field_id', 'left');
			$this->EE->db->where_in('cf.field_name', $groups);
		}

		$query = $this->EE->db->get();

		//----------------------------------------
		// Shoot the query
		//----------------------------------------
		if ($query->num_rows() == 0)
		{
			return $this->return_error('missing_data', 'No Files found! Unable to generate ZIP');
		}

		$files = $query->result();

		//----------------------------------------
		// Harvest Field ID!
		//----------------------------------------
		$tfields = array();
		foreach ($files as $file)
		{
			if ($file->link_image_id > 0) $tfields[] = $file->link_field_id;
			$tfields[] = $file->field_id;
		}

		$tfields = array_unique($tfields);

		//----------------------------------------
		// Load Location
		//----------------------------------------
		if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';
		if (class_exists('CI_Location_local') == FALSE) require PATH_THIRD.'channel_images/locations/local/local.php';
		$LOCAL = new CI_Location_local();

		//----------------------------------------
		// Check Each Field
		//----------------------------------------
		$fields = array();
		foreach ($tfields as $field_id)
		{
			// Get Field Settings!
			$settings = $this->EE->image_helper->grab_field_settings($field_id);
			$settings = $settings['channel_images'];

			if ($settings['upload_location'] != 'local') continue;

			$settings = $LOCAL->get_location_prefs($settings['locations']['local']['location']);
			$fields[$field_id] = $settings;
		}

		//print_r($fields);

		if (empty($fields) == TRUE)
		{
			return $this->return_error('missing_data', 'No suitable fields found! Unable to generate ZIP');
		}

		//----------------------------------------
		// Create .ZIP
		//----------------------------------------
		$zip = new ZipArchive();
		$zip_path = APPPATH."cache/channel_images/{$filename}.zip";
		if ($zip->open($zip_path, ZIPARCHIVE::OVERWRITE) !== true)
		{
			return $this->return_error('missing_data', 'Unable to Create ZIP. ZIP Open ERROR');
		}

		//----------------------------------------
		// Add Files!
		//----------------------------------------
		foreach ($files as $file)
		{
			$entry_id = $file->entry_id;
			$field_id = $file->field_id;

			if ($file->link_image_id > 0)
			{
				$field_id = $file->link_field_id;
				$entry_id = $file->link_entry_id;
			}

			// Good Field?
			if (isset($fields[$field_id]) == FALSE) continue;

			$zip->addFile($fields[$field_id]['path'] . $entry_id . '/' . $file->filename, $file->filename);
		}

		$zip->close();

		//----------------------------------------
		// Output to browser!
		//----------------------------------------
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: public', FALSE);
		header('Content-Description: File Transfer');
		header('Content-Type: application/zip');
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename="' . $filename . '.zip";');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . @filesize($zip_path));

		if (! $fh = fopen($zip_path, 'rb'))
		{
			exit('COULD NOT OPEN FILE.');
		}

		while (!feof($fh))
		{
			@set_time_limit(0);
		  	print(fread($fh, 8192));
		  	flush();
		}
		fclose($fh);

		@unlink($zip_path);

	}

	// ********************************************************************************* //

	private function parse_size_vars($OUT, $settings, $image)
	{
		// Get Extension
		$extension = '.' . $image->extension;

		if (isset($settings['action_groups']) == FALSE OR empty($settings['action_groups']) == TRUE) return $OUT;

		//----------------------------------------
		// Size Metadata!
		//----------------------------------------
		$metadata = array();
		if ($image->sizes_metadata != FALSE)
		{
			$temp = explode('/', $image->sizes_metadata);
			foreach($temp as $row)
			{
				if ($row == FALSE) continue;
				$temp2 = explode('|', $row);

				$metadata[$temp2[0]] = array('width' => $temp2[1], 'height'=>$temp2[2], 'size'=>$temp2[3]);
			}
		}

		// -----------------------------------------
		// Loop over all sizes!
		// -----------------------------------------
		foreach ($settings['action_groups'] as $group)
		{
			$name = strtolower($group['group_name']);
			$newname = str_replace($extension, "__{$name}{$extension}", $image->filename);

			// -----------------------------------------
			// Image URL (Size)
			// -----------------------------------------
			$image_url = $this->LOCS[$image->field_id]->parse_image_url($image->entry_id, $newname);

			// Did something go wrong?
			if ($image_url == FALSE)
			{
				$this->EE->TMPL->log_item('CHANNEL IMAGES: Image URL Failed for: ' . $image->entry_id.'/'.$image->filename);
				continue;
			}

			// SSL?
			if ($this->IS_SSL == TRUE) $image_url = str_replace('http://', 'https://', $image_url);

			$OUT = str_replace(LD.$this->prefix.'filename:'.$name.RD, $newname, $OUT);
			$OUT = str_replace(LD.$this->prefix.'url:'.$name.RD, $image_url, $OUT);
			$OUT = str_replace(LD.$this->prefix.'secure_url:'.$name.RD, $image_url, $OUT);

			// -----------------------------------------
			// Locked URLS (Size)
			// -----------------------------------------
			if ($this->locked_url == TRUE)
			{
				$locked = array('image_id' => $image->image_id, 'size'=>$name, 'time' => $this->EE->localize->now + 3600, 'ip' => $this->IP);
				$OUT = str_replace(LD.$this->prefix.'locked_url:'.$name.RD, ($this->locked_act_url . '&key=' . base64_encode(serialize($locked))), $OUT);
			}

			//----------------------------------------
			// Check for filesize, Since it's an expensive operation
			//----------------------------------------
			if ($this->parse_filesize == TRUE)
			{
				// If filesize is not defined, lets find it (only for local files)
				if (isset($metadata[$name]) == FALSE && $settings['upload_location'] == 'local')
				{
					$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
					$filepath = $filepath['path']  . $image->entry_id . '/' . $newname;
					$metadata[$name]['size'] = @filesize($filepath);
				}
				elseif (isset($metadata[$name]) == FALSE)
				{
					$metadata[$name]['size'] = 0;
				}

				$OUT = str_replace(LD.$this->prefix.'filesize:'.$name.RD, $this->EE->image_helper->format_bytes($metadata[$name]['size']), $OUT);
				$OUT = str_replace(LD.$this->prefix.'filesize_bytes:'.$name.RD, $metadata[$name]['size'], $OUT);
			}

			//----------------------------------------
			// Check for image_dimensions, Since it's an expensive operation
			//----------------------------------------
			if ($this->parse_dimensions == TRUE)
			{
				// If filesize is not defined, lets find it (only for local files)
				if (isset($metadata[$name]) == FALSE && $settings['upload_location'] == 'local')
				{
					$filepath = $this->LOCS[$image->field_id]->get_location_prefs($settings['locations']['local']['location']);
					$filepath = $filepath['path']  . $image->entry_id . '/' . $newname;
					$imginfo = @getimagesize($filepath);
					$metadata[$name]['width'] = $imginfo[0];
					$metadata[$name]['height'] = $imginfo[1];
				}
				elseif (isset($metadata[$name]) == FALSE)
				{
					$metadata[$name]['width'] = '';
					$metadata[$name]['height'] = '';
				}

				$OUT = str_replace(LD.$this->prefix.'width:'.$name.RD, $metadata[$name]['width'], $OUT);
				$OUT = str_replace(LD.$this->prefix.'height:'.$name.RD, $metadata[$name]['height'], $OUT);
			}
		}

		return $OUT;
	}

	// ********************************************************************************* //

	public function channel_images_router()
	{

		// -----------------------------------------
		// Ajax Request?
		// -----------------------------------------
		if ( $this->EE->input->get_post('ajax_method') != FALSE OR (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') )
		{
			// Load Library
			if (class_exists('Channel_Images_AJAX') != TRUE) include 'ajax.channel_images.php';

			$AJAX = new Channel_Images_AJAX();

			// Shoot the requested method
			$method = $this->EE->input->get_post('ajax_method');
			echo $AJAX->$method();
			exit();
		}


		// If nothing of the above is true...
		exit('This is the ACT URL for Channel Images');
	}

	// ********************************************************************************* //

	public function locked_image_url()
	{
		// -----------------------------------------
		// We need our Key
		// -----------------------------------------
		$key = $this->EE->input->get('key');

		if ($key == FALSE) exit();

		try	{ $data = unserialize(base64_decode($this->EE->input->get_post('key'))); }
		catch (Exception $e) { exit(); }

		// -----------------------------------------
		// Get Image
		// -----------------------------------------
		$image = $this->EE->db->select('*')->from('exp_channel_images')->where('image_id', $data['image_id'])->limit(1)->get();

		if ($image->num_rows() == 0) exit();
		$image = $image->row();

		// -----------------------------------------
		// Within Time?
		// -----------------------------------------
		if ($data['time'] < $this->EE->localize->now)
		{
			exit();
		}

		// -----------------------------------------
		// And Same IP?
		// -----------------------------------------
		if ($data['ip'] != $this->EE->input->ip_address())
		{
			exit();
		}

		// -----------------------------------------
		// Check for linked image!
		// -----------------------------------------
		if ($image->link_entry_id > 0)
		{
			$image->entry_id = $image->link_entry_id;
			$image->field_id = $image->link_field_id;
		}

		// -----------------------------------------
		// Which Filename
		// -----------------------------------------
		$filename = $image->filename;
		if ($data['size'] != FALSE)
		{
			$extension = '.' . $image->extension;
			$name = strtolower($data['size']);
			$filename = str_replace($extension, "__{$name}{$extension}", $image->filename);
		}


		// -----------------------------------------
		// Get Field Settings
		// -----------------------------------------
		$settings = $this->EE->channel_images_model->get_field_settings($image->field_id);
		$settings = $settings['channel_images'];

		//----------------------------------------
		// Load Location
		//----------------------------------------
		$location_type = $settings['upload_location'];
		$location_class = 'CI_Location_'.$location_type;
		$location_settings = $settings['locations'][$location_type];

		// Load Main Class
		if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

		// Try to load Location Class
		if (class_exists($location_class) == FALSE)
		{
			$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';
			require $location_file;
		}

		// Init!
		$LOC = new $location_class($location_settings);


		// -----------------------------------------
		// Not Local?
		// -----------------------------------------
		if ($settings['upload_location'] != 'local')
		{
			$file_url = $LOC->parse_image_url($image->entry_id, $filename);
			$this->EE->load->helper('url');
			redirect($file_url);
		}
		else
		{
			// -----------------------------------------
			// Local Location!
			// -----------------------------------------
			$location = $LOC->get_location_prefs($settings['locations']['local']['location']);
			$filepath = $location['path']  . $image->entry_id . '/' . $filename;

			// -----------------------------------------
			// Mime Type
			// -----------------------------------------
			if ($image->mime == FALSE)
			{
				// Mime type
				$image->mime = 'image/jpeg';
				if ($image->extension == 'png') $filemime = 'image/png';
				elseif ($image->extension == 'gif') $filemime = 'image/gif';
			}

			// -----------------------------------------
			// Send to Browser
			// -----------------------------------------
			header('HTTP/1.1 200 OK');
			header('Pragma: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: public', FALSE);
			header('Content-Type: ' . $image->mime);
			header('Expires: Sat, 12 Dec 1990 11:00:00 GMT'); // Date in the past
			header('X-Robots-Tag: noindex'); // Tell google not to index

			if (! $fh = fopen($filepath, 'rb'))
			{
				exit();
			}

			fpassthru($fh);
			flush();

			/*
			while (!feof($fh))
			{
				@set_time_limit(0);
			  	print(fread($fh, 8192));
			  	flush();
			}
			*/

			fclose($fh);


			exit();
		}


	}

	// ********************************************************************************* //

	public function simple_image_url()
	{
		$field_id = $this->EE->input->get('fid');
		$dir = $this->EE->input->get('d');
		$file = $this->EE->input->get('f');

		// -----------------------------------------
		// Temp DIR?
		// -----------------------------------------
		if ($field_id == 0)
		{
			//Extension
			$extension = substr( strrchr($file, '.'), 1);

			// Mime type
			$filemime = 'image/jpeg';
			if ($extension == 'png') $filemime = 'image/png';
			elseif ($extension == 'gif') $filemime = 'image/gif';

			/** ----------------------------------------
			/**  For Local Files we STREAM
			/** ----------------------------------------*/
			header('HTTP/1.1 200 OK');
			header('Pragma: public');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: public', FALSE);
			header('Content-Type: ' . $filemime);
			header('Expires: Sat, 12 Dec 1990 11:00:00 GMT'); // Date in the past
			header('X-Robots-Tag: noindex'); // Tell google not to index


			if (! $fh = fopen(APPPATH.'cache/channel_images/'.$dir.'/'.$file, 'rb'))
			{
				exit();
			}

			fpassthru($fh);
			flush();

			/*
			while (!feof($fh))
			{
				@set_time_limit(0);
			  	print(fread($fh, 8192));
			  	flush();
			}
			*/

			fclose($fh);
			exit();
		}

		// -----------------------------------------
		// Load Settings
		// -----------------------------------------
		$settings = $this->EE->channel_images_model->get_field_settings($field_id);

		if (isset($settings['channel_images']) == FALSE) exit();
		$settings = $settings['channel_images'];

		// -----------------------------------------
		// Load Location
		// -----------------------------------------
		$location_type = $settings['upload_location'];
		$location_class = 'CI_Location_'.$location_type;

		// Load Settings
		if (isset($settings['locations'][$location_type]) == FALSE)
		{
			exit();
		}

		$location_settings = $settings['locations'][$location_type];

		// Load Main Class
		if (class_exists('Image_Location') == FALSE) require PATH_THIRD.'channel_images/locations/image_location.php';

		// Try to load Location Class
		if (class_exists($location_class) == FALSE)
		{
			$location_file = PATH_THIRD.'channel_images/locations/'.$location_type.'/'.$location_type.'.php';

			if (file_exists($location_file) == FALSE)
			{
				exit();
			}

			require $location_file;
		}

		// Init!
		$LOC = new $location_class($location_settings);

		$url = $LOC->parse_image_url($dir, $file);

		$this->EE->load->helper('url');

		redirect($url);
	}

	// ********************************************************************************* //

} // END CLASS

/* End of file mod.channel_images.php */
/* Location: ./system/expressionengine/third_party/channel_images/mod.channel_images.php */