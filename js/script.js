$(document).ready(function() 
{
	
	
	/* Homepage Image fit screen
	-------------------------------------------------------------- */
	$('#todays-post, #carousel, #carousel li').css('width', $(window).width()+'px').css('height', $(window).height()+'px');
	
	
	/* ScrollTo
	-------------------------------------------------------------- */
	$.localScroll();//all divs w/class pane
	
	
	/* Lock navigation on scroll past fist screen
	-------------------------------------------------------------- */
	var top = $('#navigation').offset().top;
    $(window).scroll(function (event) {
      var y = $(this).scrollTop();
      if (y >= top) { 
      	$('#navigation').addClass('fixed'); 
      	$('#filter').css('margin-top', '58px');
      }
      else { 
      	$('#navigation').removeClass('fixed');
      	$('#filter').css('margin-top', '15px'); 
      }
	});
	
	
	/* Homepage Image rollover
	-------------------------------------------------------------- */
	$('.back').hover(
		function(){
			$(this).stop(true, false).animate({ opacity: 1 });
		},
		function(){
			$(this).stop(true, false).animate({ opacity: 0 });
		}
	);
	
	
	/* Homepage Carousel
	-------------------------------------------------------------- */
	$('#carousel') 
	.cycle({ 
	    fx:     'scrollHorz', 
	    speed:  'slow', 
	    timeout: 0, 
	    rev:	1,
	    nowrap: 1,
	    next: '.previous',
	    prev: '.next' 
	});
	
	
	/* Carousel
	-------------------------------------------------------------- */
	$('.carousel') 
	.cycle({ 
	    fx:     'fade', 
	    speed:  'fast', 
	    timeout: 0, 
	    pager:  '.carousel-navigation' 
	});
	
	
	/* Filter Expandables
	-------------------------------------------------------------- */
	$('.expand').click(function() {
		$("#tags").slideToggle();
	});
	

	
	/* Feed Infinite Scroll 
    -------------------------------------------------------------- */
	// infinitescroll() is called on the element that
    // surrounds the items you will be loading more of
    $('#categories').infinitescroll({
        navSelector : ".navigation",
        nextSelector : "a.next",
        itemSelector : "article",
        loadingImg : "/images/loader.gif",
        bufferPx : 100   
    });  
    
    
});

$(window).resize(function() {
  $('#todays-post, #carousel, #carousel li').css('width', $(window).width()+'px').css('height', $(window).height()+'px');
});