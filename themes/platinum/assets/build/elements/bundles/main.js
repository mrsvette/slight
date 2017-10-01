$(document).ready(function() {
/* portfolio */
$(function() {
var selectedClass = "";
$(".btn").click(function(){ 
	selectedClass = $(this).attr("data-rel"); 
    $("#portfolio").fadeTo(100, 0.1);
	$("#portfolio div").not("."+selectedClass).fadeOut().removeClass('scale-anm');
			
    setTimeout(function() {
      $("."+selectedClass).fadeIn().addClass('scale-anm');
      $("#portfolio").fadeTo(300, 1);
    }, 300); 
});
});

/* content10 */  
$("#partners-slider").owlCarousel({
    	autoplay: true,
    	autoplayHoverPause: true,
    	items: 6,
        loop: true,
        responsive: {
            0 : {
                items: 1
            },
            
            480 : {
                items: 1
            },
            
            768 : {
                items: 3
            },
            
            1024 : {
                items: 4
            },
            
            1170 : {
                items: 5
            }
        }
});
    	
/* content22 */ 
$(function() {
$("#testimonials").owlCarousel({
    	autoplay: true,
    	autoplayHoverPause: true,
    	items: 2,
        loop: true,
        responsive: {
            0 : {
                items: 1
            },
            
            480 : {
                items: 1
            },
            
            768 : {
                items: 1
            },
            
            1024 : {
                items: 2
            },
            
            1170 : {
                items: 2
            }
        }
	});
});
	
/* navigation2 */
$(function() {
	$('#toggle').click(function() {
   		$(this).toggleClass('active');
   		$('#overlay').toggleClass('open');
	});
});

/* content32 */  
$(function() {
$("#phone-slider").owlCarousel({
    	autoplay: true,
    	autoplayHoverPause: true,
    	items: 1,
        loop: true,
        responsive: {
            0 : {
                items: 1
            },
            
            480 : {
                items: 1
            },
            
            768 : {
                items: 1
            },
            
            1024 : {
                items: 1
            },
            
            1170 : {
                items: 1
            }
        }
	});
});
	
/* navigation8 */
$(".mobile-menu").click(function() {
    $(".menu").slideToggle();
});

/* content40 */
$('.testimonials').owlCarousel({
    	autoplay: true,
    	autoplayHoverPause: true,
    	items: 2,
        loop: true,
        responsive: {
            0 : {
                items: 1
            },
            
            480 : {
                items: 1
            },
            
            768 : {
                items: 1
            },
            
            1024 : {
                items: 2
            },
            
            1170 : {
                items: 2
            }
        }
});
    	
}); /* Document Ready */