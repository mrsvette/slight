/*-----------------------------------------------------------------------------------*/
/*	RETINA.JS
/*-----------------------------------------------------------------------------------*/
(function () {
    function t(e) {
        this.path = e;
        var t = this.path.split("."),
            n = t.slice(0, t.length - 1).join("."),
            r = t[t.length - 1];
        this.at_2x_path = n + "@2x." + r
    }
    function n(e) {
        this.el = e, this.path = new t(this.el.getAttribute("src"));
        var n = this;
        this.path.check_2x_variant(function (e) {
            e && n.swap()
        })
    }
    var e = typeof exports == "undefined" ? window : exports;
    e.RetinaImagePath = t, t.confirmed_paths = [], t.prototype.is_external = function () {
        return !!this.path.match(/^https?\:/i) && !this.path.match("//" + document.domain)
    }, t.prototype.check_2x_variant = function (e) {
        var n, r = this;
        if (this.is_external()) return e(!1);
        if (this.at_2x_path in t.confirmed_paths) return e(!0);
        n = new XMLHttpRequest, n.open("HEAD", this.at_2x_path), n.onreadystatechange = function () {
            return n.readyState != 4 ? e(!1) : n.status >= 200 && n.status <= 399 ? (t.confirmed_paths.push(r.at_2x_path), e(!0)) : e(!1)
        }, n.send()
    }, e.RetinaImage = n, n.prototype.swap = function (e) {
        function n() {
            t.el.complete ? (t.el.setAttribute("width", t.el.offsetWidth), t.el.setAttribute("height", t.el.offsetHeight), t.el.setAttribute("src", e)) : setTimeout(n, 5)
        }
        typeof e == "undefined" && (e = this.path.at_2x_path);
        var t = this;
        n()
    }, e.devicePixelRatio > 1 && (window.onload = function () {
        var e = document.getElementsByTagName("img"),
            t = [],
            r, i;
        for (r = 0; r < e.length; r++) i = e[r], t.push(new n(i))
    })
})();
/*-----------------------------------------------------------------------------------*/
/*	MENU
/*-----------------------------------------------------------------------------------*/
$(document).ready(function() {
      $('.js-activated').dropdownHover({
	      instantlyCloseOthers: false,
	      delay: 0
      }).dropdown();


      $('.dropdown-menu a').click(function (e)
{
    e.stopPropagation();
});

    });
/*-----------------------------------------------------------------------------------*/
/*  HEADER
/*-----------------------------------------------------------------------------------*/

var stickOnMobile = true; // whether or not the header should stick to top on mobile phones ( with width less than 768px )

var navbarMinimumPadding = 5;

var navbarLogoMinimumHeight = 18;
var navbarMinimumHeightTopMargin = -6;

var $navbar, $navbarLogo, $offset;

$(document).ready(function() {

    // setup header
    $navbar = $('.navbar');
    $navbar.defaultPadding = ( $navbar.outerHeight() - $navbar.height() );
    $navbar.defaultMarginBottom = $navbar.css('margin-bottom');

    $navbar.imagesLoaded(function() {
        $navbarLogo = $('.navbar a.brand img');
        $navbarLogo.defaultHeight = $navbarLogo.height();
        $navbarLogo.aspectRatio = $navbarLogo.width() / $navbarLogo.height();
        $navbarLogo.defaultMarginTop = Number( $navbarLogo.css('margin-top').replace('px', '') );
    });

    $offset = $('.offset');
    $offset.defaultPaddingTop = $offset.css('padding-top'); // save the padding top

    $(window).smartresize(function() {
        if ($(window).width() < 768 && !stickOnMobile) {
            $(window).unbind('scroll'); // stop listening to scroll event
            $offset.css('padding-top', 0); // set the content gap to 0
            $('#header.navbar').css('position', 'relative'); // unstick the header
            $navbar.css('margin-bottom', 0); // remove the margin
            $navbar.css( 'padding', ($navbar.defaultPadding / 2) + 'px 0px' ); // revert the navbar padding

            if ($navbarLogo) {
                $navbarLogo.height( $navbarLogo.defaultHeight ); // revert the navbar logo height
                $navbarLogo.width( $navbarLogo.height() * $navbarLogo.aspectRatio ); // revert the navbar logo width
                $navbarLogo.css( 'margin-top', $navbarLogo.defaultMarginTop ); // revert the navbar logo margin top
            }
        } else {
            $(window).scroll(onWindowScroll); // start listening to scroll event
            $offset.css('padding-top', $offset.defaultPaddingTop ); // reset the content gap
            $('#header.navbar').css('position', 'fixed'); // unstick the header
            $navbar.css('margin-bottom', $navbar.defaultMarginBottom); // reset the margin

            setTimeout(function() { $(window).trigger('scroll'); }, 200);
        }
    });
});

function onWindowScroll() {
    $navbar.css( 'padding', String( Math.max( ($navbar.defaultPadding - $(window).scrollTop()) / 2, navbarMinimumPadding) ) + 'px 0px' );

    if ($navbarLogo) {
        $navbarLogo.height( $navbarLogo.defaultHeight + ( ($navbar.outerHeight() - $navbar.height() - $navbar.defaultPadding) / 2 ) / ( $navbar.defaultPadding / 2 - navbarMinimumPadding ) * ( $navbarLogo.defaultHeight - navbarLogoMinimumHeight ) );
        $navbarLogo.width( $navbarLogo.height() * $navbarLogo.aspectRatio );

        // fix margin
        $navbarLogo.css( 'margin-top', (($navbar.outerHeight() - $navbar.height() - $navbar.defaultPadding) / 2 ) / ( $navbar.defaultPadding / 2 - navbarMinimumPadding ) * ( navbarMinimumHeightTopMargin - $navbarLogo.defaultMarginTop ) );
    }
}
/*-----------------------------------------------------------------------------------*/
/*	LIGHTBOX
/*-----------------------------------------------------------------------------------*/
$(document).ready(function () {
    var $container = $('.fix-portfolio .items');
    $container.imagesLoaded(function () {
        $container.isotope({
            itemSelector: '.fix-portfolio .item',
            layoutMode: 'fitRows'
        });
    });

    $('.fix-portfolio .filter li a').click(function () {

        $('.fix-portfolio .filter li a').removeClass('active');
        $(this).addClass('active');

        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector
        });

        return false;
    });
});
/*-----------------------------------------------------------------------------------*/
/*	ISOTOPE PORTFOLIO
/*-----------------------------------------------------------------------------------*/

var isotopeBreakpoints = [
                            { min_width: 1680, columns: 6 }, // Desktop
                            { min_width: 1140, max_width: 1680, columns: 5 }, // iPad Landscape
                            { min_width: 1024, max_width: 1440, columns: 4 }, // iPad Portrait
                            { min_width: 768, max_width: 1024, columns: 3 }, // iPhone Landscape
                            { max_width: 768, columns: 1 } // iPhone Portrait
                            
                         ];

$(document).ready(function () {
    var $container = $('.full-portfolio .items');

    $container.imagesLoaded(function () {
        $container.isotope({
            itemSelector: '.item',
            layoutMode: 'fitRows',
            onLayout: realignCaptions
        });
    });

    // hook to window resize to resize the portfolio items for fluidity / responsiveness
    $(window).smartresize(function() {
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();

        for ( var i = 0; i < isotopeBreakpoints.length; i++ ) {
            if (windowWidth >= isotopeBreakpoints[i].min_width || !isotopeBreakpoints[i].min_width) {
                if (windowWidth < isotopeBreakpoints[i].max_width || !isotopeBreakpoints[i].max_width) {
                    $container.find('.item').each(function() {
                        $(this).width( Math.floor( $container.width() / isotopeBreakpoints[i].columns ) );
                    });

                    break;
                }
            }
        }
    });

    $(window).trigger( 'smartresize' );


    $('.filter li a').click(function () {

        $('.filter li a').removeClass('active');
        $(this).addClass('active');

        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector
        });

        return false;
    });
});

// Vertically realign the captions inside portfolio items
function realignCaptions($container) {
  $container.each(function() {
      	 var $h5 = $(this).find('a h5');
      	 $h5.css( 'margin-top', $(this).height() / 2 - $h5.get()[0].clientHeight / 2 );
    });
}

var $wrapper = $('.fix-portfolio .items .item');
$wrapper.find('a h5').css('visibility', 'hidden');

$(document).imagesLoaded(function() {
    $wrapper.find('a h5').css('visibility', 'visible');
    realignCaptions($wrapper);
});

/*-----------------------------------------------------------------------------------*/
/*	DIRECTION AWARE PORTFOLIO HOVER
/*-----------------------------------------------------------------------------------*/
$(document).ready(function () {
   $('.overlay').hoverdir();
});

/*-----------------------------------------------------------------------------------*/
/*	CALL PORTFOLIO SCRIPTS
/*-----------------------------------------------------------------------------------*/

function callPortfolioScripts() {

    jQuery('.player').fitVids();

    $(document).ready(function() {
	if ($.fn.cssOriginal != undefined) $.fn.css = $.fn.cssOriginal;
	$('.portfolio-wide').revolution({
							delay:9000,
							startwidth:1170,
							startheight:600,

							onHoverStop:"on",						// Stop Banner Timet at Hover on Slide on/off

							thumbWidth:100,							// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
							thumbHeight:50,
							thumbAmount:3,

							hideThumbs:200,
							navigationType:"bullet",				// bullet, thumb, none
							navigationArrows:"solo",				// nexttobullets, solo (old name verticalcentered), none

							navigationStyle:"round",				// round,square,navbar,round-old,square-old,navbar-old, or any from the list in the docu (choose between 50+ different item), custom


							navigationHAlign:"center",				// Vertical Align top,center,bottom
							navigationVAlign:"bottom",					// Horizontal Align left,center,right
							navigationHOffset:0,
							navigationVOffset:40,

							soloArrowLeftHalign:"left",
							soloArrowLeftValign:"center",
							soloArrowLeftHOffset:20,
							soloArrowLeftVOffset:0,

							soloArrowRightHalign:"right",
							soloArrowRightValign:"center",
							soloArrowRightHOffset:20,
							soloArrowRightVOffset:0,

							touchenabled:"off",						// Enable Swipe Function : on/off



							stopAtSlide:-1,							// Stop Timer if Slide "x" has been Reached. If stopAfterLoops set to 0, then it stops already in the first Loop at slide X which defined. -1 means do not stop at any slide. stopAfterLoops has no sinn in this case.
							stopAfterLoops:-1,						// Stop Timer if All slides has been played "x" times. IT will stop at THe slide which is defined via stopAtSlide:x, if set to -1 slide never stop automatic

							hideCaptionAtLimit:0,					// It Defines if a caption should be shown under a Screen Resolution ( Basod on The Width of Browser)
							hideAllCaptionAtLilmit:0,				// Hide all The Captions if Width of Browser is less then this value
							hideSliderAtLimit:0,					// Hide the whole slider, and stop also functions if Width of Browser is less than this value


							fullWidth:"on",

							shadow:0

	});
});


};

/*-----------------------------------------------------------------------------------*/
/*	PRETTIFY
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function () {
window.prettyPrint && prettyPrint()
});
/*-----------------------------------------------------------------------------------*/
/*	TABS
/*-----------------------------------------------------------------------------------*/
$(document).ready(function () {
    $('.tabs').easytabs({
        animationSpeed: 300,
        updateHash: false
    });
});
/*-----------------------------------------------------------------------------------*/
/*	DATA REL
/*-----------------------------------------------------------------------------------*/
$('a[data-rel]').each(function() {
    $(this).attr('rel', $(this).data('rel'));
});
/*-----------------------------------------------------------------------------------*/
/*	TESTIMONIALS
/*-----------------------------------------------------------------------------------*/
 $(document).ready( function() {
      $('#testimonials').easytabs({
	      animationSpeed: 500,
	      updateHash: false,
	      cycle: 5000
      });

    });


 $(document).ready( function() {

    $('.accordion').on('show', function (e) {
         $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('active');
    });

    $('.accordion').on('hide', function (e) {
        $(this).find('.accordion-toggle').not($(e.target)).removeClass('active');
    });

});
/*-----------------------------------------------------------------------------------*/
/*	SHOWBIZ POSTS
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
          jQuery('.showbiz-container.posts').showbizpro({
            dragAndScroll:"off",
            visibleElementsArray:[3,3,3,1],
            mediaMaxHeight:[0,0,0,0],
            carousel:"off",
            heightOffsetBottom:0,
            rewindFromEnd:"off",
            autoPlay:"off",
            delay:2000,
            speed:250
          });
        });
/*-----------------------------------------------------------------------------------*/
/*	SHOWBIZ CLIENTS
/*-----------------------------------------------------------------------------------*/
jQuery(document).ready(function() {
          jQuery('.showbiz-container.clients').showbizpro({
            dragAndScroll:"off",
            visibleElementsArray:[6,4,3,2],
            mediaMaxHeight:[0,0,0,0],
            carousel:"on",
            heightOffsetBottom:0,
            rewindFromEnd:"off",
            autoPlay:"on",
            delay:5000,
            speed:200
          });
        });