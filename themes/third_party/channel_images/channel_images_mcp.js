// ********************************************************************************* //
var ChannelImages = ChannelImages ? ChannelImages : new Object();
ChannelImages.prototype = {}; // Get Outline Going
//********************************************************************************* //

$(document).ready(function() {

	$('.CI_FIELDS .ci_grab_images').click(ChannelImages.GrabImages);
	$('.CI_IMAGES .ci_start_resize').live('click', ChannelImages.StartResize);

});

//********************************************************************************* //

ChannelImages.GrabImages = function(Event){
	
	$.fancybox.showActivity();
	
	$.post(ChannelImages.AJAX_URL, {ajax_method:'grab_image_ids', field_id:$(Event.target).attr('rel')}, function(rData){
		$('.CI_IMAGES').html(rData);
		
		$.fancybox.hideActivity();
		
		$(Event.target).hide();
	});
	
	return false;
};

//********************************************************************************* //

ChannelImages.StartResize = function(Event){
	
	// Get the first in queue
	var Current = $('.CI_IMAGES .Queued:first');
	
	Params = {};
	Params.XID = EE.XID;
	Params.ajax_method = 'regenerate_image_size';
	Params.field_id = $('.CI_IMAGES .ci_start_resize').attr('rel');
	Params.image_id = Current.attr('rel');
	
	Current.removeClass('Queued').addClass('Uploading');
	
	$.ajax({
		type: "POST",
		url: ChannelImages.AJAX_URL,
		data: Params,
		success: function(rData){
			if (rData.success == 'yes')	{
				Current.removeClass('Uploading').addClass('Done');
				ChannelImages.StartResize(); // Shoot the next one!
			}
			else{
				Current.removeClass('Uploading').addClass('Error');
			}
		},
		dataType: 'json',
		error: function(XMLHttpRequest, textStatus, errorThrown){
			Current.removeClass('Uploading').addClass('Error');
		}
	});

	
	return false;
};

//********************************************************************************* //

ChannelImages.Debug = function(msg){
	try {
		console.log(msg);
	} 
	catch (e) {	}
};

//********************************************************************************* //