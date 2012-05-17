$(document).ready(function() 
{
	
	/* Variables
	-------------------------------------------------------------- */
	$browser_width = $(window).width();
	$browser_height = $(window).height();
	
	
	/* Homepage logo fade in
	-------------------------------------------------------------- */
	setTimeout(function(){ $("#todays-post #branding").fadeIn(500) }, 100)
	
	/* Homepage Image fit screen
	-------------------------------------------------------------- */
	$('#todays-post, #carousel, #carousel li').css('width', $browser_width+'px').css('height', $browser_height+10+'px');
	
	
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
      	$('#filter').css('margin-top', '78px');
      	$('#scroll-down').fadeOut();
      }
      else { 
      	$('#navigation').removeClass('fixed');
      	$('#filter').css('margin-top', '35px');
      	$('#scroll-down').fadeIn();
 
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
	$('.carousel').cycle({ 
	    fx:     'scrollHorz', 
	    speed:  'slow', 
	    timeout: 0, 
	    rev:	1,
	    nowrap: 1,
	    next: '.previous-item',
	    prev: '.next-item' 
	});
	
	
	/* Carousel
	-------------------------------------------------------------- */
	$('#related-carousel, #post-carousel li, #contributor-carousel').cycle({ 
	    fx:     'fade', 
	    speed:  'fast'
	});
	
	
	/* Infinite Scroll 
    -------------------------------------------------------------- */
	// infinitescroll() is called on the element that
    // surrounds the items you will be loading more of
    $('.infinite_scroll').infinitescroll({
        navSelector : ".navigation",
        nextSelector : "a.next",
        itemSelector : "article",
        loadingImg : "/images/loader.gif",
        bufferPx : 100   
    });  
    
    //Get browser size and apply to images to make fullscreen on browser resize
	$(window).bind("debouncedresize", function() {
	
		/* Variables
		-------------------------------------------------------------- */
		$browser_width = $(window).width();
		$browser_height = $(window).height();
		
		
		/* Homepage Image fit screen
		-------------------------------------------------------------- */
		$('#todays-post, #carousel, #carousel li').css('width', $browser_width+'px').css('height', $browser_height+10+'px');
		
		/* Lock navigation on scroll past fist screen
		-------------------------------------------------------------- */
		var top = $('#navigation').offset().top;
	    $(window).scroll(function (event) {
	      var y = $(this).scrollTop();
	      if (y >= top) { 
	      	$('#navigation').addClass('fixed'); 
	      	$('#filter').css('margin-top', '58px');
	      	$('#scroll-down').fadeOut();
	      }
	      else { 
	      	$('#navigation').removeClass('fixed');
	      	$('#filter').css('margin-top', '15px');
	      	$('#scroll-down').fadeIn();
	 
	      }
		});
		    
    });
    
});