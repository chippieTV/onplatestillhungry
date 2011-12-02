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