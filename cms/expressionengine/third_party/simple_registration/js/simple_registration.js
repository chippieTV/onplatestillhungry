$(document).ready(function()
{

    var openlinktext = '';

    $('.infobox-link').click(function(e) {
        var idarr = $(this).attr('id').split('-');
        var infobox_selector = '#'+idarr[1];

        if($(infobox_selector).is(':visible'))
        {
            $(infobox_selector).slideUp();
            $(this).html(openlinktext);
        }
        else
        {
            openlinktext = $(this).html();
            $(this).html('Close');
            $(infobox_selector).slideDown();
        }
    });

    $('.formcode').click(function(e)
    {
        $(this).select();
    });

    function disableOption(element_id)
    {
        $('#toggle-'+element_id).attr('checked', false);
        $('#'+element_id).hide();
    }


    {{magick}}


    $('.activate_check').click(function(e)
    {
        var idarr = $(this).attr('id').split('-');
        var input_id = idarr[1];
        
        if($(this).is(':checked'))
        {
            $('#'+input_id).slideDown('fast');
        }
        else
        {
            $('#'+input_id).slideUp('fast');

            // special case for confirm password
            if(input_id == 'password')
            {
                $('#toggle-confirm_password').attr('checked', false);
                $('#confirm_password').hide();
            }
        }

    });
});