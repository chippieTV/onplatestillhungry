$(document).ready(function() 
{
	/* Home Rollover Effect
    -------------------------------------------------------------- */

	$('.featured-item').hover(
		function(){
			$(this).children('.featured-item-body').children('div').show();
			$(this).children('.featured-item-body').children('div').stop(true, false).animate({
				bottom: 3
			}, 200);
			
		},
		function(){
			$(this).children('.featured-item-body').children('div').hide();
			$(this).children('.featured-item-body').children('div').stop(true, false).animate({
				bottom: 0
			}, 500);
		}
	);
	
	
	/* Feed Infinite Scroll 
    -------------------------------------------------------------- */
	// infinitescroll() is called on the element that
    // surrounds the items you will be loading more of
    $('.scroll').infinitescroll({
        navSelector : ".navigation",
        nextSelector : "a.next",
        itemSelector : ".item",
        loadingImg : "/images/loader.gif",
        bufferPx : 100   
    });  
    
    
});