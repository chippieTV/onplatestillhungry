<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Plugin information
**/
$plugin_info = array(
	'pi_name' => 'SM Links',
    'pi_version' => '2.0',
    'pi_author' => 'Martin Malloy',
    'pi_author_url' => 'http://supermcfly.com',
    'pi_description' => 'Channel entry previous / next links anywhere.',
    'pi_usage' => Sm_links::usage(),
);

/**
 * SM_links class
 *
 * @package		ExpressionEngine 2
 * @category	Plugin
 * @version		2.0.0
 * @author		Martin Malloy
 * @link		http://supermcfly.com
 * @license		http://creativecommons.org/licenses/by-sa/3.0/
 **/
class Sm_links
{
	
	/**
	*
	* Ensure no prior return data exists.
	*
	* @var string
	*/
	var $return_data = '';
	
	/**
	 * Constructor
	 *
	 * @return	string
	 **/
	function Sm_links()
	{
		// Make a local reference of the ExpressionEngine super object
		$this->EE =& get_instance();
		
		// Get the parameters
		$type = $this->EE->TMPL->fetch_param('type');
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		$category = $this->EE->TMPL->fetch_param('category');
		$category_group = $this->EE->TMPL->fetch_param('category_group');
		$show_expired = $this->EE->TMPL->fetch_param('show_expired');
		$show_future_entries = $this->EE->TMPL->fetch_param('show_future_entries');
		$status = $this->EE->TMPL->fetch_param('status');
		$channel_name = $this->EE->TMPL->fetch_param('channel');

		// It's not worth continiuing if there is no entry_id or type
		if ($entry_id == "" OR $type == "")
		{
			return;
		}

		// Get what's between the tags
		$return_data = $this->EE->TMPL->tagdata;
		
		// Sort order depending whether it's a previous or next link
		$sort = ($type == 'next') ? $type = 'ASC' : $type = 'DESC';

		// Find the next / previous entry
		$sql = 'SELECT t.entry_id, t.title, t.url_title, w.channel_title FROM (exp_channel_titles AS t) LEFT JOIN exp_channels AS w ON w.channel_id = t.channel_id ';

		// We use LEFT JOIN when there is a 'not' so that we get entries that are not assigned to a category.
		if (substr($category_group, 0, 3) == 'not' OR substr($category, 0, 3) == 'not')
		{
			$sql .= 'LEFT JOIN exp_category_posts ON t.entry_id = exp_category_posts.entry_id
					 LEFT JOIN exp_categories ON exp_category_posts.cat_id = exp_categories.cat_id ';
		}
		elseif($category_group OR $category)
		{
			$sql .= 'INNER JOIN exp_category_posts ON t.entry_id = exp_category_posts.entry_id
					 INNER JOIN exp_categories ON exp_category_posts.cat_id = exp_categories.cat_id ';
		}
		
		// Exclude the current entry
		$sql .= ' WHERE t.entry_id != '.$entry_id.' ';

		// Set the timestamp
		$timestamp = ($this->EE->TMPL->cache_timestamp != '') ? $this->EE->localize->set_gmt($this->EE->TMPL->cache_timestamp) : $this->EE->localize->now;

		// Allow future entries?
	    if ($show_future_entries != 'yes')
	    {
	    	$sql .= " AND t.entry_date < {$timestamp} ";
	    }

		// Show expired entries?
	    if ($show_expired != 'yes')
	    {
			$sql .= " AND (t.expiration_date = 0 OR t.expiration_date > {$timestamp}) ";
	    }

		$sql .= " AND w.site_id IN ('".implode("','", $this->EE->TMPL->site_ids)."') ";

		// Limit by channels if supplied
		if ($channel_name)
		{
			$sql .= $this->EE->functions->sql_andor_string($channel_name, 'channel_name', 'w')." ";
		}

		// Limit by status if given or set to only open
		if ($status)
	    {
			$status = str_replace('Open',   'open',   $status);
			$status = str_replace('Closed', 'closed', $status);

			$sql .= $this->EE->functions->sql_andor_string($status, 't.status')." ";
		}
		else
		{
			$sql .= "AND t.status = 'open' ";
		}

	    // Limit query by category
	    if ($category)
	    {
	    	if (stristr($category, '&'))
	    	{
	    		// First, we find all entries with these categories
	    		$for_sql = (substr($category, 0, 3) == 'not') ? trim(substr($category, 3)) : $category;

	    		$csql = "SELECT exp_category_posts.entry_id, exp_category_posts.cat_id, ".
						str_replace('SELECT', '', $sql).						
						$this->EE->functions->sql_andor_string(str_replace('&', '|', $for_sql), 'exp_categories.cat_id');

				// Get the categories fro mthe database
	    		$results = $this->EE->db->query($csql);

				// Don't continue if there are no matching catagories
	    		if ($results->num_rows() == 0)
	    		{
					return;
	    		}

	    		$type = 'IN';
	    		$categories	 = explode('&', $category);
	    		$entry_array = array();

	    		if (substr($categories[0], 0, 3) == 'not')
	    		{
	    			$type = 'NOT IN';

	    			$categories[0] = trim(substr($categories[0], 3));
	    		}

	    		foreach($results->result as $row)
	    		{
	    			$entry_array[$row['cat_id']][] = $row['entry_id'];
	    		}

	    		if (count($entry_array) < 2 OR count(array_diff($categories, array_keys($entry_array))) > 0)
	    		{
					return;
	    		}

	    		$chosen = call_user_func_array('array_intersect', $entry_array);

	    		if (count($chosen) == 0)
	    		{
					return;
	    		}

	    		$sql .= "AND t.entry_id ".$type." ('".implode("','", $chosen)."') ";
	    	}
	    	else
	    	{
	    		if (substr($category, 0, 3) == 'not')
	    		{
	    			$sql .= $this->EE->functions->sql_andor_string($category, 'exp_categories.cat_id', '', TRUE)." ";
	    		}
	    		else
	    		{
	    			$sql .= $this->EE->functions->sql_andor_string($category, 'exp_categories.cat_id')." ";
	    		}
	    	}
	    }

		// Limit query by category group
	    if ($category_group)
	    {
	        if (substr($category_group, 0, 3) == 'not')
			{
				$sql .= $this->EE->functions->sql_andor_string($category_group, 'exp_categories.group_id', '', TRUE)." ";
			}
			else
			{
				$sql .= $this->EE->functions->sql_andor_string($category_group, 'exp_categories.group_id')." ";
			}
	    }

		$sql .= " ORDER BY t.entry_date {$sort}, t.entry_id {$sort} LIMIT 1";

		$query = $this->EE->db->query($sql);

		if ($query->num_rows() == 0)
		{
			return;
		}

		// Replace the variable between the tags
		if (strpos($return_data, LD.'l_path=') !== FALSE)
		{
			$l_path  = (preg_match("#".LD."l_path=(.+?)".RD."#", $return_data, $match)) ? $this->EE->functions->create_url($match[1]) : $this->EE->functions->create_url("SITE_INDEX");
			$l_path .= '/'.$query->row('url_title');
			$return_data = preg_replace("#".LD."l_path=.+?".RD."#", $l_path, $return_data);
		}

		// Replace the variable between the tags
		if (strpos($return_data, LD.'l_id_path=') !== FALSE)
		{
			$l_id_path  = (preg_match("#".LD."l_id_path=(.+?)".RD."#", $return_data, $match)) ? $this->EE->functions->create_url($match[1]) : $this->EE->functions->create_url("SITE_INDEX");
			$l_id_path .= '/'.$query->row('entry_id');
			$return_data = preg_replace("#".LD."l_id_path=.+?".RD."#", $l_id_path, $return_data);
		}

		// Replace the variable between the tags
		if (strpos($return_data, LD.'l_url_title') !== FALSE)
		{
			$return_data = str_replace(LD.'l_url_title'.RD, $query->row('url_title'), $return_data);
		}

		// Replace the variable between the tags
		if (strpos($return_data, LD.'l_entry_id') !== FALSE)
		{
			$return_data = str_replace(LD.'l_entry_id'.RD, $query->row('entry_id'), $return_data);
		}

		// Replace the variable between the tags
		if (strpos($return_data, LD.'l_title') !== FALSE)
		{
			$return_data = str_replace(LD.'l_title'.RD, $query->row('title'), $return_data);
		}

		// Replace the variable between the tags		
		if (strpos($return_data, LD.'l_channel') !== FALSE)
		{
			$return_data = str_replace(LD.'l_channel'.RD, $query->row('channel_title'), $return_data);
		}

		// Finally! Stick that in your pipe and smoke it.
		$this->return_data = $this->EE->functions->remove_double_slashes(stripslashes($return_data));
	}
	
	/**
	 * Usage
	 *
	 * How the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		ob_start();
		?>
		Introduction
		--------------------------------------------------------------------------------
		
		With this ExpressionEngine 2 plugin you can now have next / previous links anywhere you want, including between the {exp:channels} tags.
		
		Just supply the plugin with an entry ID and a type (next or previous) and you're set. It works like the first-party next/previous entry linking.
		
		
		Examples
		--------------------------------------------------------------------------------
		
		{exp:sm_links type="previous" entry_id="3"}
			<p>Previous entry: <a href="{l_path='content/blogs'}">{l_title}</a></p>
		{/exp:sm_links}
		
		{exp:sm_links type="next" entry_id="3"}
			<p>Next entry: <a href="{l_path='content/blogs'}">{l_title}</a></p>
		{/exp:sm_links}


		Parameters:
		--------------------------------------------------------------------------------

		type="previous" 
		Defaults to "". Required. The type of link to create.
		
		entry_id="23" 
		Defaults to "". Required. The entry from which the results are generated.
		
		category="17"
		Defaults to "". Limit by specific categories.

		category_group="2"
		Defaults to "". Limit by category groups.

		show_expired="no"
		Defaults to "no". Allow expired entried to be included.
		
		show_future_entries="no"
		Defaults to "no". Allow future entries to be included.
		
		status="open"
		Defaults to "open". Limit by entry status.
		
		channel="blogs"
		Defaults to "". Limit by entry channels.
		
		
		Variables:
		--------------------------------------------------------------------------------
		
		{l_entry_id}
		The ID number of the entry.
		
		{l_title}
		The title of the entry.
		
		{l_channel}
		The channel title of the entry. Useful for conditional template paths.
		
		{l_url_title}	
		The human readable title used in the URL as a permalink.
		
		{l_id_path='site/index'}
		The path (template_group/template) where you want to show the entry. The entry_id of the entry will be automatically added.
		
		{l_path='site/index'}
		The path (template_group/template) where you want to show the entry. The url_title of the entry will be automatically added.

		<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}
// END Sm_links class

/* End of file pi.sm_links.php */ 
/* Location: ./system/expressionengine/third_party/sm_lorem_ipsum/pi.sm_links.php */