/**
 * --------------------------------------------------------------------
 * gp Full Page Slider jQuery plugin.
 * --------------------------------------------------------------------
 */

(function ($, $window, window) {

    var dummyStyle = document.createElement('div').style,
        vendor = (function () {
            var vendors = 't,webkitT,MozT,msT,OT'.split(','),
                t,
                i = 0,
                l = vendors.length;

                for ( ; i < l; i++ ) {
                    t = vendors[i] + 'ransform';
                    if ( t in dummyStyle ) {
                        return vendors[i].substr(0, vendors[i].length - 1);
                    }
                }

                return false;
        })(),
        cssVendor = vendor ? '-' + vendor.toLowerCase() + '-' : '';

    $.gpSlider = function (options, el, callback) {

        this.$el               = $(el);
        this.$slides           = this.$el.find('.slide');
        this.slideCount        = this.$slides.length;
        this.slidesLoaded      = 0;
        this.options           = $.extend({}, $.gpSlider.settings, options);
        this.isActivating      = false;
        this.slideshowTimeout  = 0;
        this.slideshowRunning  = false;

        if (0 !== this.$slides.length) {
            this.$slides.data('loaded', false);
            this._init();
        }

    };

    $.gpSlider.settings = {
        onNextSlide: null,
        onPreviousSlide: null,
        slideshow: false,
        slideshowDuration: 7,
        animation: 'slide',
        animationOptions: {
            fade: {
                speed: 500,
                easing: 'ease-in'
            },
            slide: {
                speed: 1000,
                easing: 'ease-in'
            },
            fadeAndSlide: {
                speed: 500,
                easing: 'ease-in'
            }
        }
    };

    $.gpSlider.prototype = {

        _init: function () {

            var that = this,
                $firstSlide = this.$slides.first();

            /**
             * Setup infoboxes.
             */
            this.$el.find('.info').each(function () {
                var $infobox = $(this),
                    t = 0;

                if (!window.oldie) {
                    iscroll = new iScroll($infobox.get(0), {
                                hideScrollbar: false,
                                scrollbarClass: 'iscrollbar'
                            }
                        );

                    iscroll.disable();
                    $infobox.data('iscroll', iscroll);
                }

                $window.on('resize.infobox.gp', _.debounce(function () {
                    that.setInfoboxPosition.call(that, $infobox);
                }));
                that.setInfoboxPosition($infobox);
            });

            /**
             * Load first image.
             */
            this.load($firstSlide, function () {

                /**
                 * Setup navigation items.
                 */
                if (this.slideCount > 1) {

                    /**
                     * Navigation arrows.
                     */
                    $('.slider-arrow-left,.slider-arrow-right').show().click(function (e) {
                        $(this).is('.slider-arrow-right') ? that.next() : that.previous();
                        e && e.preventDefault();
                    });

                    this.enableNavigationGestures();

                    /**
                     * Navigation bullets.
                     */
                    var $nav = $('<nav class="slider-navigation">'),
                        $ul = $('<ul />');

                    this.$slides.each(function () {
                        var $a = $('<a href="#"><b></b></a>'),
                            title = $(this).find('.slide-title').html();

                        if (title) {
                            $a.append($('<span />').html(title));
                        }

                        $ul.append($('<li />').append($a));
                    });

                    $nav.append($ul);

                    $nav.find('a').click(function (e) {
                        var $t = $(this);

                        if (!$t.is('.active')) {
                            $nav.find('.active').removeClass('active');
                            $t.addClass('active');

                            var index = $t.parent().prevAll().length;
                            that.activate(that.$slides.eq(index));
                        }
                        e && e.preventDefault();
                    }).first().addClass('active');

                    this.$nav = $nav;
                    $nav.appendTo(this.$el)

                    // Slideshow
                    this.options.slideshow && this.startSlideshow();

                } else {

                    this.$nav = $('<div />');

                }

                /**
                 * Show first slide.
                 */
                $firstSlide.addClass('active')
                           .css('visibility', 'visible')
                           .delay(100)
                           .transition({
                                opacity: 1
                           }, 1500, function () {
                                that.loadAll();
                           });

                /**
                 * Bind keyboard events.
                 */
                $window.on('keydown.slider.gp', function (e) {

                    if (that.slideCount > 1) {
                        // right arrow down
                        if (e.which == 39) {
                            that.next();
                            return false;
                        }

                        // left arrow down
                        if (e.which == 37) {
                            that.previous();
                            return false;
                        }
                    }

                });

            });

        },

        startSlideshow: function () {

            var that = this,
                duration = this.options.slideshowDuration;

            this.slideshowRunning = true;

            $('html').addClass('slideshow-active');

            if (! /\d+/.test(duration)) {
                duration = $.gpSlider.settings.slideshowDuration;
            }

            this.$nav.addClass('auto-slideshow')
                     .find('b').css(cssVendor + 'animation-duration', duration + 's');

            this.slideshowTimeout = setTimeout(function () {

                that.next(null, true);

            }, parseInt(duration, 10) * 1000);

        },

        pauseSlideshow: function () {

            this.$nav.removeClass('auto-slideshow');
            clearTimeout(this.slideshowTimeout);

        },

        stopSlideshow: function () {

            this.slideshowRunning = false;
            this.pauseSlideshow();
            $('html').removeClass('slideshow-active');

        },

        _isCallable: function ( variable ) {

            return variable && ( typeof variable === 'function' );

        },

        _getSelectedText: function () {

            var t = '';
            if ( window.getSelection && this._isCallable( window.getSelection ) ) {
                t = window.getSelection();
            } else if ( document.getSelection && this._isCallable( document.getSelection ) ) {
                t = document.getSelection();
            }else if ( document.selection ){
                t = document.selection.createRange().text;
            }
            return t;

        },

        enableNavigationGestures: function () {

            var isDragging = false,
                downPosition = false,
                that = this;

            this.$slides.on( 'mousedown.slider.gp', function ( e ) {

                downPosition = e.screenX;

                $window.on( 'mousemove.slider.gp', function () {

                    isDragging = true;
                    $window.off( 'mousemove.slider.gp' );

                } );

            } ).on( 'mouseup', function ( e ) {

                var wasDragging = isDragging;
                isDragging = false;

                $window.off( 'mousemove.slider.gp' );

                if ( wasDragging ) {

                    var selectedText = that._getSelectedText();

                    if (selectedText && selectedText.length) {

                        var delta = downPosition - e.screenX;

                        if ( Math.abs(delta) > 150 ) {

                            delta > 0 ? that.next() : that.previous();

                        }

                    }

                }

            } );

            // Requires a touchwipe jQuery plugin
            if ( typeof $.fn.touchwipe === 'function' ) {

                this.$el.touchwipe( {
                    wipeLeft: function() {
                        that.next(250);
                    },
                    wipeRight: function() {
                        that.previous(250);
                    },
                    min_move_x: 20,
                    min_move_y: 20,
                    preventDefaultEvents: true
                });

            }

        },

        setCustomPosition: function ($infobox) {

            var width       = this.$el.width(),
                height      = this.$el.height(),
                infoHeight  = $infobox.outerHeight(),
                infoWidth   = $infobox.outerWidth(),
                top         = $infobox.data('top'),
                left        = $infobox.data('left');

            // Prevents infobox going out of bounds.
            if ( /%$/.test( top ) && /%$/.test( left ) ) {

                var topPx = Math.round( parseInt(top, 10) * height / 100 ),
                    leftPx = Math.round( parseInt(left, 10) * width / 100 );

                if ( leftPx + infoWidth > width ) {
                    left = width - infoWidth;
                }

                if ( topPx + infoHeight > height ) {
                    top = height - infoHeight;
                    top = top < 0 ? 0 : top;
                }

            }

            $infobox.css({
                top: top,
                left: left
            });

        },

        setCenterPosition: function ( $infobox ) {

            var width       = this.$el.width(),
                height      = this.$el.height(),
                infoHeight  = $infobox.outerHeight(),
                infoWidth   = $infobox.outerWidth(),
                top         = Math.round( ( height - infoHeight ) / 2 ),
                left        = Math.round( ( width - infoWidth ) / 2 );

            if ( left + infoWidth > width ) {
                left = width - infoWidth;
            }

            if ( top + infoHeight > height ) {
                top = height - infoHeight;
                top = top < 0 ? 0 : top;
            }

            $infobox.css({
                        top: top,
                        left: left
                    });

        },

        setInfoboxPosition: function ( $infobox ) {

            var iscroll     = $infobox.data('iscroll'),
                width       = this.$el.width(),
                height      = this.$el.height(),
                infoHeight  = $infobox.outerHeight();


            if ( $infobox.data( 'position' ) == 'custom' ) {

                /**
                 * We use custom position only when screen is wider than 480px,
                 * otherwise we try to center it in the middle.
                 */
                if ( width > 480 ) {

                    this.setCustomPosition( $infobox );

                } else {

                    this.setCenterPosition( $infobox );

                }

            } else {

                this.setCenterPosition( $infobox );

            }

            if (infoHeight > height) {

                $infobox.css({
                    height: '100%'
                });

                if (iscroll && !iscroll.enabled) {
                    iscroll.enable();
                    iscroll.refresh();
                }

            } else {

                $infobox.css({
                    height: 'auto'
                });

                if (iscroll && iscroll.enabled) {
                    iscroll.disable();
                }

            }

        },

        animateTransition: function ($newSlide, $currentSlide, options, callback) {

            var that = this;

            options = options || { direction: 1 };

            if (this.options.animation == 'fadeAndSlide') {

                options = $.extend(this.options.animationOptions.fadeAndSlide, options);
                options.direction = options.direction || 1;

                $currentSlide.css('z-index', 50);

                $newSlide.css({
                    opacity: 0,
                    x: 0
                });

                $newSlide.css({
                    zIndex: 60,
                    x: -20 * options.direction,
                    visibility: 'visible'
                });

                $currentSlide.css('z-index'); // Required to force reflow.

                $newSlide.transition({
                    opacity: 1,
                    x: 0
                }, options.speed, options.easing, function () {

                    $currentSlide.css('visibility', 'hidden');
                    callback && callback.call(that);

                });

            } else if (this.options.animation == 'fade') {

                options = $.extend(this.options.animationOptions.fade, options);

                $currentSlide.css('z-index', 50);

                $newSlide.css({
                    opacity: 0
                });

                $newSlide.css({
                    zIndex: 60,
                    visibility: 'visible'
                });

                $currentSlide.css('z-index'); // Required to force reflow.

                $newSlide.transition({
                    opacity: 1
                }, options.speed, options.easing, function () {

                    $currentSlide.css('visibility', 'hidden');
                    callback && callback.call(that);

                });

            } else { // Default animation is slide

                options = $.extend(this.options.animationOptions.slide, options);
                options.direction = options.direction || 1;

                $currentSlide.css('z-index', 50);

                // Put new slide under the current one
                $newSlide.css({
                    zIndex: 30,
                    visibility: 'visible',
                    x: 0,
                    opacity: 1
                });

                // Move current slide out of the way
                $currentSlide.transition({
                    x: $window.width() * options.direction
                }, options.speed, options.easing, function () {

                    $currentSlide.css('visibility', 'hidden');
                    callback && callback.call(that);

                });

            }

        },

        activate: function ($slide, animationOptions, initiatedBySlideshow) {

            if (this.isActivating || $slide.is('.active')) {
                return false;
            }

            animationOptions = animationOptions || {};

            this.isActivating = true;

            if (this.options.slideshow) {
                this.pauseSlideshow();
            }

            var that = this,
                $active = this.$slides.filter('.active'),
                index = this.$slides.index($slide),
                activeIndex = this.$slides.index($active),
                $infoboxParts = $slide.find('.animate-1, .animate-2').css('opacity', 0);

            /**
             * Set CSS .active classes
             */
            $active.removeClass('active');
            $slide.addClass('active');

            this.$nav.find('.active').removeClass('active');
            this.$nav.find('a:eq(' + index + ')').addClass('active');

            if (!animationOptions.direction) {
                animationOptions.direction = activeIndex > index ? 1 : -1;
            }

            this.animateTransition($slide, $active, animationOptions, function () {

                that.isActivating = false;

                if ($infoboxParts.length) {

                    $infoboxParts.eq(0).css({
                        x: -100
                    }).delay(200).transition({
                        x: 0,
                        opacity: 1
                    }, 500);

                    $infoboxParts.eq(1).css({
                        x: 100
                    }).delay(500).transition({
                        x: 0,
                        opacity: 1
                    }, 500);

                }

                if (that.options.slideshow) {
                    if (initiatedBySlideshow) {
                        that.startSlideshow();
                    } else if (that.slideshowRunning) {
                        that.stopSlideshow();
                    }
                }

            });

        },

        next: function (speed, initiatedBySlideshow ) {

            var index = this.$slides.filter('.active').prevAll().length,
                animationOptions = {
                    direction: -1
                };

            index = this.slideCount - 1 == index ? 0 : index + 1;

            if (speed) {
                animationOptions.speed = speed;
            }

            this.activate(
                    this.$slides.eq(index),
                    animationOptions,
                    initiatedBySlideshow
                );

            this.options.onNextSlide && this.options.onNextSlide.call(this);

        },

        previous: function (speed, initiatedBySlideshow) {

            var index = this.$slides.filter('.active').prevAll().length,
                animationOptions = {
                    direction: 1
                };

            index = 0 === index ? this.slideCount - 1 : index - 1;

            if (speed) {
                animationOptions.speed = speed;
            }

            this.activate(
                    this.$slides.eq(index),
                    animationOptions,
                    initiatedBySlideshow
                );

            this.options.onPreviousSlide && this.options.onPreviousSlide.call(this);

        },

        load: function ( $slideToLoad, onFinish ) {

            if ( true === $slideToLoad.data( 'loaded' ) ) {
                onFinish.call( this );
                return;
            }

            var that = this,
                img = new Image();

            $(img).on('load error', function () {

                $slideToLoad.data('loaded', true)
                            .css('background-image', 'url(' + img.src + ')');

                that.slidesLoaded++;
                onFinish.call( that );

            });

            img.src = $slideToLoad.data( 'image' );

        },

        loadAll: function (callback) {

            var that = this;

            this.slidesLoaded = 0;

            this.$slides.each(function () {

                var $t = $(this);

                if (false === $t.data('loaded')) {

                    that.load($t, function () {
                        that.slidesLoaded++;
                        that.slidesLoaded == that.slideCount && callback && callback.call(that);
                    });

                } else {

                    that.slidesLoaded++;
                    that.slidesLoaded == that.slideCount && callback && callback.call(that);

                }

            });

        }

    };

    $.fn.gpSlider = function (options, callback) {

        options = options || {};

        return $(this).each(function () {
            var instanceOptions = $(this).data() || {};
            instanceOptions = $.extend({}, options, instanceOptions);

            $(this).data('slider', new $.gpSlider(instanceOptions, this, callback));
        });

    };

}(jQuery, jQuery(window), window));