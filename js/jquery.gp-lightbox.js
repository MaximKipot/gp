/**
 * gp Lightbox
 *
 * Uses:
 *     jquery
 *     jquery.transit
 *     underscore
 */

(function ($, $window, isUndefined) {

    var namespace_index = 0,
        loadedImages = {};

    $.gpLightbox = function($links, options) {

        namespace_index++;
        this.namespace = 'gp-lightbox-' + namespace_index;

        var that = this;

        this.options = $.extend({}, $.gpLightbox.defaults, options);
        this.allImagesLoaded = false;

        // Support for <a/> and <iframe/> tags.
        this.$media = $links.filter('a,iframe');
        this.$html = $('html');

        if (!this.$media.length) {
            return false;
        }

        this.visible = false;
        this._init();

    };

    $.gpLightbox.defaults = {
        openAnimation: 'fade', // fade or slide
        close: 'Close',
        resize: '',
        previous: '',
        next: '',
        loading: 'Please wait...',
        error: 'Unable to load.',
        loadAll: true,
        mode: 'fit'     // fit or full
    };

    $.gpLightbox.prototype = {

        open: function (callback) {

            // Don't move a finger if it's already visible.
            if (this.visible) {
                return;
            }

            var that = this,
                $controls;

            this.originalScrollPosition = {
                x: $window.scrollLeft(),
                y: $window.scrollTop()
            };

            this.$html.addClass('gp-lightbox-visible');

            if (this.options.openAnimation == 'fade') {
                // Animate background
                this.$lightbox.css({
                    opacity: 0,
                    display: 'block'
                }).transition({
                    opacity: 1
                }, 600, 'ease-out', callback);
            } else {
                $controls = this.$next.add(this.$previous).add(this.$resize).add(this.$close);
                $controls.css('opacity', 0);
                this.$lightbox.css({
                    y: '-100%',
                    display: 'block'
                }).transition({
                    y: 0
                }, 600, 'ease-out', function () {
                    that.$lightbox.css('transform', 'none');
                    $controls.transition({ opacity: 1 }, 400);
                    callback && callback.call(that);
                });
            }

            this.visible = true;

            // bind events
            $window.on('keydown.gp-lightbox', function (e) {

                if (39 == e.which) {
                    that.next.call(that);
                    return false;
                }

                if (37 == e.which) {
                    that.previous.call(that);
                    return false;
                }

                if (27 == e.which) {
                    that.close.call(that);
                    return false;
                }

            });

            // Requires a touchwipe jQuery plugin
            if (typeof $.fn.touchwipe == 'function') {

                this.$lightbox.touchwipe( {
                    wipeLeft: function() {
                        that.next(300);
                    },
                    wipeRight: function() {
                        that.previous(300);
                    },
                    min_move_x: 20,
                    min_move_y: 20,
                    preventDefaultEvents: true
                });

            }

        },


        close: function () {

            if (!this.visible) {
                return false;
            }

            this.$lightbox.fadeOut(500, this.options.onHide);

            this.$contents.html('');

            this.$html.removeClass('gp-lightbox-visible');

            $(window).off('keydown.gp-lightbox')
                     .off('resize.gp-lightbox');

            window.scrollTo(this.originalScrollPosition.x, this.originalScrollPosition.y);

            this.visible = false;

        },


        _placeElementInCenter: function ($element) {

            $element.css('visibility', 'hidden').show();

            // Set a tiny delay, so that browser can calculate element size
            setTimeout(function () {

                var windowHeight = $(window).height(),
                    windowWidth = $(window).width(),
                    width = $element.width(),
                    height = $element.height();

                $element.css({
                    top: (windowHeight - height) / 2,
                    left: (windowWidth - width) / 2
                });

            }, 10);

            return $element.hide().css('visibility', 'visible');

        },


        showIframe: function ($iframe, fadeInTime) {

            // Show loading
            this.$error.hide();
            this._placeElementInCenter(this.$loading).show();

            // don't show resize button
            this.$resize.hide();

            var that             = this,
                $iframeContainer = $('<div class="iframe-container" />'),
                $iframeClone     = $iframe.clone(),
                resizeDebounce   = 0;

            $iframeContainer.append($iframeClone).hide();

            $iframeClone.error(function () {

                // iframe loading error
                that.$loading.hide();
                that._placeElementInCenter(that.$error).show();

            }).load(function () {

                // put iframe in position
                that.resizeIframe($iframeClone);

                // on resize, put iframe in position
                $(window).off('resize.gp-lightbox')
                         .on('resize.gp-lightbox', function () {

                            clearTimeout(resizeDebounce);
                            resizeDebounce = setTimeout(function () {
                                that.resizeIframe($iframeClone);
                            }, 100);

                    });

                that.$loading.hide();

                $iframeContainer.fadeIn(300);

            });

            this.$media.removeClass('lightbox-active');
            $iframe.addClass('lightbox-active');

            // Append contents with iFrame
            this.$contents.html('').append($iframeContainer);

        },


        resizeIframe: function ($iframe) {

            if (!$iframe.length) {
                return false;
            }

            var aspectRatio = false,
                windowWidth = $window.width(),
                windowHeight = $window.height(),
                windowRatio = windowHeight ? windowWidth / windowHeight : 1,
                height, width;

            if ($iframe.attr('width') && $iframe.attr('height')) {
                aspectRatio = $iframe.attr('width') / $iframe.attr('height');
            }

            if (false === /^\d+(\.\d+)?$/.test(String(aspectRatio))) {
                aspectRatio = false;
            }

            if (aspectRatio) {

                if (windowRatio > aspectRatio) {

                    height = $iframe.parent().innerHeight();

                    if (height > 1080) {
                        height = 1080;
                    }

                    height -= 100;
                    width = height * aspectRatio;

                    $iframe.css({
                        height: height,
                        width: width
                    });

                } else {

                    width = $iframe.parent().innerWidth(),
                    height = width / aspectRatio;

                    if (width > 1920) {
                        width = 1920;
                        height = 1920 / aspectRatio;
                    }

                    $iframe.css({
                        width: width,
                        height: height
                    });

                }

                if (windowHeight > height) {
                    $iframe.css('top', (windowHeight - height) / 2);
                } else {
                    $iframe.css('top', 0);
                }

            } else {

                height = $iframe.height();

                if (height < windowHeight) {
                    $iframe.css('top', (windowHeight - height) / 2);
                } else {
                    $iframe.css('top', 0);
                }

            }

        },


        /**
         * Show image in the Lightbox.
         */
        showImage: function ($image, fadeInTime) {

            this.$error.hide();

            this.$contents.children('img').fadeOut(400);

            var that = this,
                src = $image.attr('href'),
                resizeAndShow;

            resizeAndShow = function (src) {
                var $newImage = $('<img />');

                $newImage.attr('src', src).css('opacity', 0);

                $window.off('resize.gp-lightbox').on('resize.gp-lightbox', _.debounce(function () {
                    that.resizeImage($newImage);
                }));

                that.resizeImage($newImage, function () {

                    that.$contents.html('').append($newImage);

                    that.$loading.fadeOut(100);
                    $newImage.transition({
                        opacity: 1
                    }, fadeInTime || 400);

                });
            };

            this.$resize.show();

            // If image is not yet loaded
            if (!loadedImages[src]) {

                var img = new Image();

                this._placeElementInCenter(this.$loading).show();

                $(img).error(function () {

                    that.$loading.hide();
                    that._placeElementInCenter(that.$error).show();

                }).load(function () {

                    loadedImages[src] = {
                            width: img.width,
                            height: img.height
                        };

                    resizeAndShow(src);

                } );

                img.src = src;

            } else {
                resizeAndShow(src);
            }

            this.$media.removeClass('lightbox-active');
            $image.addClass('lightbox-active');


        },

        resizeImage: function ($image, callback) {

            if (!$image.length) {
                return false;
            }

            var that         = this,
                img          = new Image(),
                src          = $image.attr('src'),
                resizer;

            resizer = function (src) {
                var width = loadedImages[src].width,
                    height = loadedImages[src].height,
                    ratio = width / height,
                    resizedHeight = height,
                    windowHeight = $window.height(),
                    windowWidth  = $window.width(),
                    windowRatio  = windowWidth / windowHeight;

                if ('fit' === that.mode) {

                    if (windowRatio > ratio) {

                        // Fit height
                        $image.css({
                            width: 'auto',
                            height: '100%',
                            maxHeight: height,
                            maxWidth: width
                        });

                    } else {

                        // Fit width
                        $image.css({
                            width: '100%',
                            height: 'auto',
                            maxHeight: height,
                            maxWidth: width
                        });

                        resizedHeight = (windowWidth / width) * height;

                    }

                    if (resizedHeight <= windowHeight) {
                        $image.css('top', (windowHeight - resizedHeight) / 2);
                    }

                } else {

                    $image.css({
                            width: '100%',
                            height: 'auto',
                            top: '0'
                        });

                }

                callback && callback.call(that);

            };

            if (loadedImages[src]) {

                resizer(src);

            } else {

                $(img).load(function () {
                    loadedImages[src] = {
                        width: img.width,
                        height: img.height
                    };
                    resizer(src);
                });
                img.src = src;

            }

        },


        next: function () {

            var $active     = this.$media.filter('.lightbox-active'),
                activeIndex = this.$media.index($active),
                count       = this.$media.length,
                newIndex    = 0;

            if ((activeIndex != -1) && (activeIndex + 1 != count)) {
                newIndex = activeIndex + 1;
            }

            var $nextItem = this.$media.eq(newIndex);

            if ($nextItem.is('a')) {
                this.showImage($nextItem, 400);
            } else {
                this.showIframe($nextItem, 400);
            }

        },


        previous: function () {

            var $active = this.$media.filter('.lightbox-active'),
                activeIndex = this.$media.index($active),
                count = this.$media.length,
                newIndex = count - 1;

            if ((activeIndex !== -1) && (activeIndex !== 0)) {
                newIndex = activeIndex - 1;
            }

            var $previousItem = this.$media.eq(newIndex);

            if ($previousItem.is('a')) {
                this.showImage($previousItem, 400);
            } else {
                this.showIframe($previousItem, 400);
            }

        },


        bindClick: function () {

            var that = this;

            this.$media.filter('a').on('click.gp-lightbox', function (e) {
                var $t = $(this);

                that.showImage.call(that, $t, 0);
                that.open.call(that, that.options.onShow);

                e.preventDefault();
            });

        },


        /**
         * Initialize Lightbox.
         */
        _init: function () {

            this.bindClick();

            var that = this,
                template = '' +
                    '<div class="gp-lightbox ' + this.namespace + '">' +
                        '<div class="lightbox-content"></div>' +
                        '<span class="lightbox-close icon-cancel">%close</span>' +
                        '<span class="lightbox-resize icon-resize-small">%resize</span>' +
                        '<span class="lightbox-prev icon-left-open-big">%previous</span>' +
                        '<span class="lightbox-next icon-right-open-big">%next</span>' +
                        '<div class="lightbox-loading">%loading</div>' +
                        '<div class="lightbox-error">%error</div>' +
                    '</div>';

            template = template.replace('%close', this.options.close);
            template = template.replace('%resize', this.options.resize);
            template = template.replace('%previous', this.options.previous);
            template = template.replace('%next', this.options.next);
            template = template.replace('%loading', this.options.loading);
            template = template.replace('%error', this.options.error);

            this.$lightbox = $(template);
            this.$contents = this.$lightbox.find('.lightbox-content');

            this.$loading = this.$lightbox.find('.lightbox-loading');
            this.$error   = this.$lightbox.find('.lightbox-error');

            /**
             * Assign DOM events
             */
            this.$next = this.$lightbox.on('click', '.lightbox-next', function (e) {
                that.next.call(that);
                e && e.preventDefault();
            }).find('.lightbox-next');

            this.$previous = this.$lightbox.on('click', '.lightbox-prev', function (e) {
                that.previous.call(that);
                e && e.preventDefault();
            }).find('.lightbox-prev');

            this.$close = this.$lightbox.on('click', '.lightbox-close', function (e) {
                that.close.call(that);
                e && e.preventDefault();
            }).find('.lightbox-close');

            this.$resize = this.$lightbox.on('click', '.lightbox-resize', function (e) {
                that.mode == 'fit' ? that.fullScreen.call(that) : that.fitToScreen.call(that);
                e && e.preventDefault();
            }).find('.lightbox-resize');

            if (1 === this.$media.length) {
                this.$next.hide();
                this.$previous.hide();
                this.$resize.css('right', 0);
            }

            /**
             * Do not show resize on iPhone/iPad.
             */
            if (this.isIOS()) {
                this.$resize.hide();
                this.$resize = $('<div />');
                this.options.mode = 'fit';
            }

            if (this.options.mode == 'fit') {
                this.options.mode = '';
                this.fitToScreen();
            } else {
                this.options.mode = '';
                this.fullScreen();
            }

            $('body').append(this.$lightbox);

        },


        fitToScreen: function () {

            if (this.$resize.is('.icon-resize-small')) {
                this.$resize.removeClass('icon-resize-small').addClass('icon-resize-full');
            }

            if (this.mode != 'fit') {
                this.$lightbox.removeClass('mode-full').addClass('mode-fit');
                this.mode = 'fit';
                this.resizeImage(this.$lightbox.find('img'));
            }

        },


        fullScreen: function () {

            if (this.$resize.is('.icon-resize-full')) {
                this.$resize.removeClass('icon-resize-full').addClass('icon-resize-small');
            }

            if (this.mode != 'full') {
                this.$lightbox.removeClass('mode-fit').addClass('mode-full');
                this.mode = 'full';
                this.resizeImage(this.$lightbox.find('img'));
            }

        },


        /**
         * Checks for iOS.
         */
        isIOS: function () {
          return (/(iPad|iPhone)/i).test(navigator.userAgent);
        }

    };

    $.fn.gpLightbox = function (options) {
        return this.data('gp-lightbox', new $.gpLightbox(this, options));
    };

})( jQuery, jQuery(window) );