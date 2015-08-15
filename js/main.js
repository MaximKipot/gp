;(function ($, window, $window, isUndefined) {

    $(function () {

        var $html       = $('html'),
            $main       = $('#main'),
            $header     = $('#header'),
            $footer     = $('#footer'),

            // Components
            $sidebar             = $('.sidebar'),
            $keyRight            = $('#key-right'),
            $keyLeft             = $('#key-left'),
            $navTip              = $('.nav-tip'),
            $scrollContainer     = $('.scroll-container'),
            mobileNav,

            // Functions
            getHorizontalPageHeight,
            pageResizer,

            // Gloal variables
            allowUpscale = $html.is('.upscale');

        if (iPadWithIOS4()) {
            $html.addClass('ipad-ios4');
        }

        getHorizontalPageHeight = function () {
            return $window.height() - $header.outerHeight() - $header.offset().top - $footer.outerHeight();
        };

        // $('.horizontal-page').each(function () {

        //     var resizer = function () {

        //         var windowWidth = $window.width(),
        //             windowHeight = $window.height(),
        //             // The header is position:fixed we have to calculate the offset for main page dynamically.
        //             headerHeight = $header.outerHeight() + $header.offset().top;
        //             footerHeight = $footer.outerHeight();

        //         // If we are on a small screen
        //         if (windowWidth <= 480) {

        //             if ($html.is('.no-scroll')) {
        //                 $main.css({
        //                     height: windowHeight - headerHeight,
        //                     top: 0
        //                 });
        //             } else {
        //                 $main.css({
        //                     height: 'auto',
        //                     top: 0
        //                 });
        //             }

        //         } else {

        //             $main.css({
        //                 height: getHorizontalPageHeight(),
        //                 top: headerHeight
        //             });

        //         }

        //     };

        //     // MMM $window.on('resize.horizontal-page.gp', _.debounce(resizer));
        //     resizer();

        //     $main.transition({
        //         opacity: 1
        //     }, 100);

        // });

        /**
         * General size adjustments on window resize.
         */
        pageResizer = function () {

            /**
             * Update tinyscrollbar values.
             */
            $scrollContainer.each(function () {
                var $t = $(this),
                    tsb = $t.data('tsb');

                $t.find('.scrollbar, .track').css('height', $t.height());
                tsb && tsb.update();
            });


            if ($window.width() <= 768) {

                // Initialize mobile menu only if we have a small sceen size.
                if (!mobileNav) {

                    // Make mobile menu item array.
                    var $siteNavigation = $('.site-navigation'),
                        $mobileNavItems = $siteNavigation.find('a').filter(function () {

                            var $t = $(this),
                                level = $t.parents('ul').length;

                            $t.data('level', level);

                            if (level == 1) {
                                return true;
                            } else {
                                if ($t.closest('.current-menu-item, .current_page_ancestor').length) {
                                    return true;
                                }
                            }
                            return false;

                        });

                    /**
                     * Initialize mobile menu.
                     */
                    mobileNav = new MobileNav($mobileNavItems, {
                        openButtonTitle: $siteNavigation.data('menu'),
                        active: $siteNavigation.find('.current-menu-item > a')
                    });

                }

            }

            /**
             * Trigger vertical center plugin.
             */
            setTimeout(function () {
                $('.js-vertical-center').verticalCenter();
            }, 100);

        };

        // MMM $window.on('resize.page-resizer.gp', _.debounce(pageResizer));
        // MMM pageResizer();

        /**
         * Arrows and mousewheel navigation plugin.
         */
        globalNav = new Navigation({
            onSetItems: function () {
                this.$items.length && $navTip.show();
            }
        });

        /**
         * Full page slider
         */
        $('.slider').each(function () {

            var $slider = $(this),
                slider;

            $slider.gpSlider({
                    onNextSlide: function () {
                        globalNav.options.onNextItem();
                    },
                    onPreviousSlide: function () {
                        globalNav.options.onPreviousItem();
                    }
                });

            slider = $slider.data('slider');

            slider.slideCount > 1 && $navTip.show();

            globalNav.disableKeyboard();

            $keyRight.click(function (e) {
                slider.next();
                e && e.preventDefault();
            });

            $keyLeft.click(function (e) {
                slider.previous();
                e && e.preventDefault();
            });

        });


        /**
         * Appreciate plugin
         */
        var $appreciate = $('.btn-appreciate');
        $appreciate.appreciate();


        /**
         * Sharrre plugin
         */
        $('#sharrre-footer').each(function () {

            var $el = $(this),
                services = {},
                buttonsTitle = $el.data('buttons-title');

            if (!$el.data('services')) {
                return;
            }

            // retrieve social networks from DOM element.
            $.each($el.data('services').split(','), function () {
                services[this] = true;
            });

            $el.sharrre({
                share: services,
                buttonsTemplate: buttonsTitle ? '<b>' + buttonsTitle + '<a href="#" class="close"></a></b>' : '',
                urlCurl: $el.data('curl'),
                template: '<b class="share">{title}</b>' +
                          '<span class="counts">' +
                            (services.facebook ? '<b class="count-facebook">{facebook}</b>' : '') +
                            (services.twitter ?'<b class="count-twitter">{twitter}</b>' : '') +
                            (services.googlePlus ?'<b class="count-plus">{plus}</b>' : '') +
                          '</span>',
                render: function(self, options) {
                    var html = this.template.replace('{title}', options.title);
                    html = html.replace('{facebook}', options.count.facebook);
                    html = html.replace('{twitter}', options.count.twitter);
                    html = html.replace('{plus}', options.count.googlePlus);
                    $(self.element).html(html);
                    $el.show();
                }
            });

        });

        $('#sharrre-project').each(function () {

            var $el = $(this),
                services = {},
                buttonsTitle = $el.data('buttons-title');

            if (!$el.data('services')) {
                return;
            }

            // retrieve social networks from DOM element.
            $.each($el.data('services').split(','), function () {
                services[this] = true;
            });

            $el.sharrre({
                share: services,
                buttonsTemplate: buttonsTitle ? '<div class="arrow"></div><b>' + buttonsTitle + '<a href="#" class="close"></a></b>' : '',
                urlCurl: $el.data('curl'),
                template: '<span class="icon"></span><div class="box">' +
                            '<a class="share" href="#">{title}</a>' +
                            '<b class="count-total">{total}</b>' +
                          '</div>',
                render: function(self, options) {
                    var total = options.shorterTotal ? self.shorterTotal(options.total) : options.total,
                        html = this.template.replace('{title}', options.title).replace('{total}', total);
                    $(self.element).html(html);
                    $el.css('display', 'inline-block');
                },
                afterLoadButtons: function () {
                    var index = 0,
                        $buttons = this.$el.find('.button'),
                        count = $buttons.each( function () {
                                    index++;
                                    $(this).addClass('button-' + index);
                                }).length;
                    this.$el.addClass('social-services-' + count);
                }
            });

        });



        /**
         * Fixes menu issue, when popup is outside the screen.
         */
        $('.site-navigation .has-children').hover(function () {

            var $submenu = $(this).children('.sub-menu');
            if ($submenu.length) {

                // if popup is outside the screen, then align it by the right side of the screen.
                if ($submenu.offset().left + $submenu.outerWidth() - $(document).scrollLeft() > $window.width()) {
                    $submenu.addClass('sub-menu-right');
                }

            }

        }, function () {

            $(this).children('.sub-menu').removeClass('sub-menu-right');

        });


        /**
         * If our page has a horizontal layout.
         */
        if ($html.is('.horizontal-page')) {

            /**
             * Enable tinyscrollbar plugin.
             */
            $('.scroll-container').tinyscrollbar({
                axis: 'y'
            });

            /**
             * Enable keyboard navigation.
             */
            globalNav.options.onNextItem = function () {
                $keyRight.addClass('flash');
                setTimeout(function () {
                    $keyRight.removeClass('flash');
                }, 200);
            };

            globalNav.options.onPreviousItem = function () {
                $keyLeft.addClass('flash');
                setTimeout(function () {
                    $keyLeft.removeClass('flash');
                }, 200);
            };

            $keyRight.click(function (e) {
                globalNav.nextItem();
                e && e.preventDefault();
            });

            $keyLeft.click(function (e) {
                globalNav.previousItem();
                e && e.preventDefault();
            });

        }


        /**
         * --------------------------------------------------------------------------------
         * Specific pages
         * --------------------------------------------------------------------------------
         */


        /**
         * Page: Grid portfolio
         */
        $('.portfolio-grid').each(function () {

            var $grid = $(this),
                grid,
                getActiveItem;

            /**
             * Enable Grid plugin.
             */
            $grid.grid({
                minWindowWidth: 768,
                rows: $grid.data('rows'),
                columns: $grid.data('columns')
            }, function () {
                $grid.find('.inner').verticalCenter();
                $grid.width() > $window.width() ? $navTip.show() : $navTip.hide();
            });

            grid = $grid.data('grid');

            // Sets first line of a grid (the longest one) as a source for navigation plugin.
            globalNav.setItems(grid.getRowItems(0));

            /**
             * When using arrows navigation gp theme will center images by default.
             * This is not a preferred behavior for Grid Layout. For that we will
             * override navigation plugin with our own logic.
             */
            getActiveItemIndex = function () {
                var windowRight = $window.scrollLeft() + $window.width(),
                    activeIndex;

                this.$items.each(function (index) {
                    var $t = $(this),
                        itemRight = parseInt($t.offset().left, 10) + parseInt($t.width(), 10);

                    if (windowRight > itemRight) {
                        activeIndex = index;
                    } else {
                        return false;
                    }
                });

                return activeIndex ? activeIndex : false;
            };

            globalNav.options.nextItem = function () {

                activeIndex = getActiveItemIndex.call(this);

                if (activeIndex !== false) {
                    var $next = this.$items.eq(activeIndex + 1);
                    if ($next.length) {
                        var x = parseInt($next.offset().left, 10) + parseInt($next.width(), 10) - $(window).width() + grid.options.gutterWidth;
                        $('html,body').not(':animated').animate({ scrollLeft: x }, 300);
                        this.options.onNextItem && this.options.onNextItem.call(this);
                    }
                }

            };

            globalNav.options.previousItem = function () {

                activeIndex = getActiveItemIndex.call(this);

                if (activeIndex !== false) {
                    var $previous = this.$items.eq(activeIndex - 1);
                    if ($previous.length) {
                        var x = parseInt($previous.offset().left, 10) + parseInt($previous.width(), 10) - $(window).width() + grid.options.gutterWidth;
                        $('html,body').not(':animated').animate({ scrollLeft: x }, 300);
                        this.options.onPreviousItem && this.options.onPreviousItem.call(this);
                    }
                }

            };

            $grid.width() > $window.width() ? $navTip.show() : $navTip.hide();

            resizer = function () {

                if ($window.width() <= 480) {
                    grid.disable();
                } else {
                    grid.enable();
                }

                grid.resize();

            };

            resizer();
            $window.on('resize.grid-portfolio.gp', _.debounce(resizer));

        });


        /**
         * Page: Portfolio
         */
        $('.portfolio-list').each(function () {

            var $el = $(this),
                $projects = $el.find('.project'),
                $images = $el.find('.featured-image'),
                $info = $el.find('.info'),
                $hoverContents = $el.find('.hover-box-contents'),
                resizer;

            resizer = function () {

                var highest, totalWidth,
                    windowWidth = $window.width();

                if (windowWidth > 768) {

                    highest = $info.highestElement().outerHeight();

                    $images.each(function () {
                        var $t = $(this),
                            maxHeight = allowUpscale ? 3000 : $t.attr('height'),
                            height = $main.height() - highest;

                        if ($t.is('img')) {
                            $t.css('height', height > maxHeight ? maxHeight : height);
                        }
                    });

                    $hoverContents.vcenter();

                    if ($html.is('.ipad-ios4')) {

                        totalWidth = 0;

                        $el.children().each(function () {
                            totalWidth += $(this).width();
                        });

                        $el.css('width', totalWidth);

                    }

                } else {

                    if (windowWidth <= 480) {
                        $el.find('.info').css('height', 'auto');
                    }

                    $html.is('.ipad-ios4') && $el.css('width', 'auto');

                }

            };

            resizer();
            $window.on('resize.horizontal-portfolio.gp', _.debounce(resizer));

            /**
             * Set keyboard navigation items.
             */

            globalNav.setItems($projects);

            // Show project on image load, which prevents flickering.
            $projects.each(function () {
                var $t   = $(this),
                    $img = $t.find('.featured-image'),
                    img  = new Image();

                $(img).on('load error', function () {
                    $t.find('.hover-box-contents').vcenter();
                    $t.addClass('loaded');
                });
                img.src = $img.attr('src');
            });

        });


        /**
         * Page: Portfolio Single
         */
        $('.portfolio-single').each(function () {

            var $mediaList = $(this),
                $horizontalItems = $('.horizontal-item'),
                $resizable = $mediaList.find('.image, iframe'),
                $navigation = $('.portfolio-navigation'),
                $navigationLike = $navigation.find('header'),
                $navigationOther = $navigation.find('.navigation'),
                $navigationOtherProjects = $navigationOther.find('.other-projects');

            globalNav.setItems($horizontalItems.add($navigation));

            // Bind Lightbox to image links and iframes
            $('.project-image-link, .horizontal-item iframe').gpLightbox({
                onShow: function () {
                    globalNav.disableKeyboard();
                },
                onHide: function () {
                    globalNav.enableKeyboard();
                },
                loading: $mediaList.data('loading')
            });

            $resizable.each(function () {
                var $t = $(this),
                    width = $t.data('width') || $t.attr('width') || 1,
                    height = $t.data('height') || $t.attr('height') || 1;
                $t.data({
                    height: height,
                    ratio: width / height
                });
            });

            var resizer = function () {

                var contentHeight = getHorizontalPageHeight(),
                    windowWidth = $window.width(),
                    windowHeight = $window.height(),
                    otherHeight = $navigationOther.outerHeight(),
                    likeHeight = $navigationLike.outerHeight(),
                    temp;

                $resizable.each(function () {

                    var $t = $(this),
                        height = $t.data('height'),
                        ratio = $t.data('ratio'),
                        width;

                    if (allowUpscale) {
                        height = contentHeight;
                    } else {
                        height = contentHeight > height ? height : contentHeight;
                    }

                    if (windowWidth > 768) {

                        if ($t.is('iframe')) {
                            width = contentHeight * ratio;
                            // Upscale video until the width is wider than window width.
                            width = windowWidth - 30 > width ? width : windowWidth - 30;

                            $t.css({
                                width: width,
                                maxWidth: width, // required for webkit
                                height: width / ratio
                            });
                        } else {
                            $t.css({
                                width: height * ratio,
                                height: height
                            });
                        }

                    } else { // Vertical layout

                        if ($t.is('iframe')) {
                            $t.css('width', '100%');
                            $t.css('height', $t.width() / ratio);
                        } else {
                            $t.css({
                                width: allowUpscale ? '100%' :'auto',
                                maxWidth: allowUpscale ? 'auto' : '100%',
                                height: 'auto'
                            });
                        }

                    }

                });

                if (windowWidth <= 768) {

                    $('#content').addTempCss('padding-top', $sidebar.outerHeight() + 30);
                    $navigationLike.css('top', 0);

                } else {

                    $('#content').removeTempCss('padding-top');

                    if ( contentHeight < otherHeight + likeHeight ) {
                        $navigationOtherProjects.css('visibility', 'hidden');

                        temp = (contentHeight - otherHeight - likeHeight + $navigationOtherProjects.outerHeight()) / 2;
                        $navigationLike.css('top', temp > 0 ? temp : 0);
                    } else {
                        $navigationOtherProjects.css('visibility', 'visible');

                        temp = (contentHeight - otherHeight - likeHeight) / 2;
                        $navigationLike.css('top', temp);
                    }

                }

                if ($html.is('.ipad-ios4')) {

                    var totalWidth = 0;

                    $mediaList.children().each(function () {
                        totalWidth += $(this).width();
                    });

                    $mediaList.css({
                        width: totalWidth
                    });

                }

            };

            $window.on('resize.gp.portfolio-single', _.debounce(resizer));
            resizer();

            // Fade in images and videos once loaded.
            if (!$mediaList.data('lazy')) {

                $horizontalItems.each(function () {

                    var $t = $(this);

                    if ($t.is('.project-image')) {

                        var $imageLink = $t.find('.project-image-link'),
                            img = new Image();

                        $(img).on('load', function () {
                            $t.transition({ opacity: 1 }, 500);
                        }).on('error', function () {
                            $t.remove();
                        });

                        img.src = $imageLink.attr('href');

                    } else {

                        $t.transition({
                            opacity: 1
                        }, 500);

                    }

                });

            } else {

                var $lazyImages = $('.lazy-image'),
                    lazyLoading;

                $lazyImages.on('loadImage.gp', function () {

                    var $t = $(this).off('loadImage.gp'),
                        img = new Image();

                    $(img).load(function () {
                        var $img = $('<img />').attr('src', img.src);

                        $t.addClass('lazy-image-loaded').append($img);
                        $img.transition({
                            opacity: 1
                        }, 400);
                    });

                    img.src = $t.closest('a').attr('href');

                });

                lazyLoading = function (e) {

                    $lazyImages.each(function () {
                        var $t = $(this),
                            imageLeftPoint = $t.offset().left,
                            imageRightPoint = imageLeftPoint + $t.width(),
                            windowLeftPoint = $window.scrollLeft(),
                            windowRightPoint = windowLeftPoint + $window.width();

                        if ((windowLeftPoint < imageRightPoint) && (windowRightPoint > imageLeftPoint )) {
                            $t.triggerHandler('loadImage');
                            $lazyImages = $lazyImages.not($t);
                        }
                    });

                    if (!$lazyImages.length) {
                        $window.off('scroll.gp.single-portfolio');
                        $window.off('resize.gp.single-portfolio');
                    }

                };

                lazyLoading();
                $window.on('scroll.gp.single-portfolio', lazyLoading);
                $window.on('resize.gp.single-portfolio', _.debounce(lazyLoading));

            }

        });


        /**
         * Page: Vertical Blog
         */
        $('.vertical-blog').each(function () {

            $videos = $('.wrap-embed-video iframe');

            var resizer = function () {

                if ($window.width() > 768) {
                    $sidebar.css('height', $main.height());
                } else {
                    $sidebar.css('height', 'auto');
                }

                $videos.each(function () {

                    var $t = $(this),
                        maxHeight = $t.attr('height'),
                        maxWidth = $t.attr('width'),
                        ratio = maxHeight ? maxWidth / maxHeight : false;

                    if (ratio) {

                        $t.css('width', '100%');
                        $t.css('height', parseInt($t.width(), 10) / ratio);

                    }

                });

            };

            resizer();
            $window.on('resize.vertical-blog.gp', _.debounce(resizer));

        });


        /**
         * Page: Horizontal Blog
         */
        $('.horizontal-posts').each(function () {

            var $excerpts = $('.post-with-media .text-contents'),
                $resizable = $('.resizable'),
                $videos = $('.wrap-embed-video'),
                $posts = $('.post'),
                resizer;

            /**
             * Measures the highest content area, then calculates available space
             * and makes sure that every image uses the same height.
             */
            resizer = function () {

                var maxExcerptHeight = $excerpts.highestElement().outerHeight(),
                    maxMediaHeight;

                if (!maxExcerptHeight) {
                    return;
                }

                maxMediaHeight = $main.height() - maxExcerptHeight;
                maxMediaHeight = maxMediaHeight > 328 ? 328 : maxMediaHeight; // limit max height to 328

                // Set sizes for images
                $resizable.each(function () {

                    var $t = $(this),
                        postWidth = $t.css('height', maxMediaHeight).width();

                    postWidth = postWidth < 583 ? 583 : postWidth;

                    $t.closest('.post').css('width', postWidth);

                });

                // Set sizes for videos
                $videos.each(function () {

                    var $t = $(this),
                        $article = $t.closest('article'),
                        $object = $t.children('iframe:first'),
                        ratio = $object.data('ratio'),
                        postWidth;

                    if (!ratio) {
                        if ($object.attr('width') && $object.attr('height')) {
                            ratio = $object.attr('width') / $object.attr('height');
                        } else {
                            ratio = 16 / 9;
                        }
                        $object.data('ratio', ratio);
                    }

                    postWidth = maxMediaHeight * ratio;
                    postWidth = postWidth < 500 ? 500 : postWidth;

                    $object.css({
                        height: maxMediaHeight,
                        width: maxMediaHeight * ratio
                    });
                    $article.css('width', postWidth);

                });

            };

            resizer();
            $window.on('resize.horizontal-posts.gp', _.debounce(resizer));

            globalNav.setItems($posts.add($('.navigation-paging')));

            return;

        });

        /**
         * Page: Blog Single
         */
         $('.single-post').each(function () {
            var $video = $('.wrap-embed-video iframe'),
                $videoParent,
                ratio,
                resizer;

            if (!$video.length) {
                return false;
            }

            $videoParent = $video.parent();
            ratio = $video.width() / $video.height();
            resizer = function () {
                var width = $videoParent.width();
                width = width > 1080 ? 1080 : width;

                $video.css({
                    width: width,
                    height: width / ratio
                });
            };

            resizer();
            $window.on('resize.single-post.gp', _.debounce(resizer));
         });


        /**
         * Page: Contacts
         */
        $('.page-contacts').each(function () {

            var $contactsForm = $('.wpcf7'),
                $infobox = $(this).find('.page'),
                $viewport = $infobox.children('.viewport'),
                iscroll,
                resizer;

            if ($contactsForm.length) {
                $contactsForm.detach();
                $('#contacts-modal .modal-contents').append( $contactsForm );
            }

            $('#send-message').click(function () {
                $('#contacts-modal').reveal({
                    closeonbackgroundclick: true,
                    middle: true
                });
                return false;
            });

            if (!window.oldie) {
                iscroll = new iScroll($infobox.get(0), {
                        hideScrollbar: false,
                        scrollbarClass: 'iscrollbar'
                    });
            }

            resizer = function () {

                var infoHeight = $infobox.outerHeight(),
                    mainHeight = getHorizontalPageHeight();

                if ($viewport.height() > mainHeight) {

                    $infobox.css({
                        top: 0,
                        height: '100%'
                    });

                    if (iscroll && !iscroll.enabled) {
                        iscroll.enable();
                    }
                    iscroll && iscroll.refresh();

                } else {

                    var top = Math.round( ( mainHeight - infoHeight ) / 2 );
                    top = top < 0 ? 0 : top;

                    $infobox.css({
                        top: top,
                        height: 'auto'
                    });

                    iscroll && iscroll.enabled && iscroll.disable();

                }

            };

            resizer();
            $window.on('resize.contacts.gp', _.debounce(resizer));

        });


        $('.link-to-image').gpLightbox();



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

})(jQuery, window, jQuery(window));