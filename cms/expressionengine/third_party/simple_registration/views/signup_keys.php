<p><strong>Signup keys</strong> can be used to assign a user to a specific member group on signup. <a href='#' class="infobox-link" id="infobox-infobox_body">More info &#x25B6;</a></p>
<div class="infobox" id="infobox_body" style="display:none">
<p>Signup keys are used by adding an element named "<em>signup_key</em>" to your form.</p>
<p><H4>A couple of example use cases:</h4>
<br/>
<strong>You want to automatically assign all registered users to a "Beta user group":</strong>
<br/>
Add a signup key for the "Beta user group" and add this code to your signup form:<br/>
<textarea>&lt;input type=&quot;hidden&quot; name=&quot;signup_key&quot; value=&quot;GENERATED_SIGNUP_KEY_GOES_HERE&quot;/&gt;</textarea>
<br/><br/>
    <strong>You want the user to be able to select his group when registering:</strong>
<br/>
Add a signup key for each user group you want the user to be able to select between. Then use these values in a select dropdown in your form, like so:<br/>
<textarea>
&lt;select name=&quot;signup_key&quot;&gt;
 &lt;option value=&quot;GENERATED_SIGNUP_KEY_1&quot;&gt;User group #1&lt;/option&gt;
 &lt;option value=&quot;GENERATED_SIGNUP_KEY_2&quot;&gt;User group #2&lt;/option&gt;
&lt;/select&gt;
</textarea>
<br/><br/>
    <strong>You want to use the signup key as a "Beta Invite":</strong>
    <br/>
    Add a signup key for the "Members" group and add an input field in your signup form:<br/>
    <textarea>Invite code: &lt;input type=&quot;text&quot; name=&quot;signup_key&quot;/&gt;</textarea>
    <br/>
    Then in EE's Members -> Preferences set "Default Member Group Assigned to New Members" to "Pending" for instance and generate one or more keys that you hand out to people you would like to invite.
    <br/><br/>


    </p>

<p><H4>A note about the "Trigger" event:</h4>
<br/>
This option decides <em>when</em> the member group assignment should happen. As a general rule; if you have <em>activation enabled</em> you should use the trigger "Activation". This means that members will be assinged to the selected member group once they activate their membership. If you do not use activation, it should be set to "Signup". Note that Simple Registration will default to the value you should use when you add a new key.
</p>

</div>

<p>&nbsp;</p>
<p>
<?php
echo form_open($_form_base.AMP."method=add_signup_key", '' );
?>
 Generate a new key for member group: <?php print form_dropdown('member_group_id', $member_groups);?>
 <?php print form_submit('submit',lang('add_signup_key'),'class="submit"')?>
<?php print form_close()?>
</p>

<?php
echo form_open($_form_base.AMP."method=update_signup_keys", '' );
?>

<?php
        $this->table->set_template($cp_table_template);
        $header_arr = array(
            lang('signup_key'),
            lang('membergroup'),
            lang('trigger'),
            lang('delete'),
        );

        $this->table->set_heading($header_arr);

        if (count($signup_keys) > 0)
        {
            foreach($signup_keys as $signup_key => $signup_key_arr)
            {
                $row_array = array(
                    '<strong>'.$signup_key.'</strong>',
                    form_dropdown('key_'.$signup_key_arr['signup_key_id'], $member_groups, $signup_key_arr['member_group_id'] ),
                    form_dropdown('trigger_'.$signup_key_arr['signup_key_id'], $triggers, $signup_key_arr['trigger_event']),
                    '<a href="'.$_base.'&method=delete_signup_key&key='.$signup_key.'">Delete</a>'
                );

                $this->table->add_row(
                    $row_array
                );
            }
        }
        else
        {
            $this->table->add_row(array('data' => lang('no_signup_keys_added'), 'colspan' => count($header_arr)));
        }

        echo $this->table->generate();
?>




 <?php print form_submit('submit',lang('btn_save_changes'),'class="submit"')?>

<?php print form_close()?>

