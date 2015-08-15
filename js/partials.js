
//$(window).load(function(){
	
jQuery(document).ready(function($){
	
	     var _left_bar_scroll_step = 60;

    	// Login hide or show and load iframe content if needed
    	$('#login-btn').click(function(){
    	  if( $('#top_iframe').attr('src') == 'about:blank' || $('#top_iframe').attr('src') == '') {
            $('#top_iframe').attr('src', 'https://www.germanpersonnel.de/persy/account/user/login/type/gp-homepage');
            setTimeout(function(){
              $('#top_iframe').fadeIn(300);
            },1000)
            // $('#top_iframe').fadeIn(1300);
        }
          $('#login').toggleClass('login_open');
    	});

      $('.iphorm-fancybox-link').click(function(){
        if($(this).find('.kontakt_button').length > 0) {
          // track this button as Kontakt Button
          __gaTracker('send', {
            'hitType': 'event',
            'eventCategory': 'Kontakt',
            'eventAction': 'click',
            'eventLabel': ''+$(document).find("title").text()+'',
            'eventValue': 4
          });
        }
      });

      // function subnavigation_show_partials() {
      //   setTimeout(
      //       function(){
      //           $('#subnavigation').animate({
      //               opacity: 1
      //             }, 400, 'swing', function() {
      //             // Animation complete.
      //         });
      //     }, 100
      //   );
      // }

      // subnavigation_show_partials();


/* discover */
      

  // // Show the Subnavigation on the left
  // setTimeout(
  //     function(){
  //         $('#subnavigation').animate({
  //             opacity: 1
  //           }, 400, 'swing', function() {
  //           // Animation complete.
  //       });
  //   }, 50
  // );
	
  //   $("#footer").mCustomScrollbar({
		// mouseWheelPixels: _left_bar_scroll_step*2,
		// scrollInertia: 300,
  //   	horizontalScroll: true
  //   });


      /*
      * Horizontal Columns recalculate
      * 
      */

      
      

	     $('#header-push').height($(header).outerHeight());

	     /**
         * --------------------------------------------------------------------------------
         * Shortcodes.
         * --------------------------------------------------------------------------------
         */

        /**
         * Shortcode: Tabs
         */
        $('.tabs').each(function () {

            var $t = $(this);

            $t.find('.tabs-menu a').click(function () {

                var $t = $(this ),
                    $p = $t.parent(),
                    index = $p.prevAll().length;

                if ( $p.is('.active') ) {
                    return false;
                }

                $p.parent().find('.active').removeClass('active');
                $p.addClass('active');

                $p.closest('.tabs').find('.tab').hide().end().find('.tab:eq(' + index + ')').show();

                return false;

            }).each(function (index) {

                $(this).wrapInner($('<span />'))
                       .append($('<b>' + (index + 1) + '</b class="index">'));

            });

        });


        /**
         * Shortcode: Accordion
         */
        $('.accordion').each(function () {

            var $accordion = $(this);

            $accordion.find('.panel-title a').click(function () {
                var $t = $(this);

                /**
                 * This is the active panel. Let's collapse it.
                 */
                if ($t.closest('.panel-active').length) {
                    $t.closest('.panel-active').find('.panel-content').slideUp(500, function () {
                        $(this).closest('.panel-active').removeClass('panel-active');
                    });
                    return false;
                }

                var $newPanel = $t.closest('.panel'),
                    index = $newPanel.prevAll().length;

                $panelActive = $accordion.find('.panel-active');

                if ($panelActive.length) {

                    $panelActive.find('.panel-content').slideUp(500, function () {
                        $(this).closest('.panel').removeClass('panel-active');
                        $accordion.find('.panel:eq(' + index + ') .panel-content').slideDown(300)
                                  .closest('.panel').addClass('panel-active');

                    });

                } else {

                    $accordion.find('.panel:eq(' + index + ') .panel-content').slideDown(300)
                              .closest('.panel').addClass('panel-active');

                }

                return false;
            });

        });


        /**
         * Shortcode: Gallery
         */
        $('.gallery-link-file').each(function () {
            $(this).find('a').gpLightbox();
        });

        if (window.oldie) {
            $('.portfolio-navigation .other-projects a').each(function () {
                var $t = $(this),
                    image_url = $t.css('background-image').slice(5, -2);

                $t.css({
                    'filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + image_url + "', sizingMethod='scale')",
                    '-ms-filter': "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + image_url + "', sizingMethod='scale')"
                });
            });
        }
});