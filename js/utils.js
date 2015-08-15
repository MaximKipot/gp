
window.log = function(){
  if( this.console ) {
    if ( arguments.length == 1 ) {
      console.log( arguments[0] );
    } else {
      console.log( Array.prototype.slice.call( arguments ) );
    }
  }
};

/**
 * Checks for iOS4 or less, which does not have position:fixed.
 */
window.iPadWithIOS4 = function () {

  if ( /(iPad)/i.test( navigator.userAgent ) ) {
    if(/OS [2-4]_\d(_\d)? like Mac OS X/i.test(navigator.userAgent)) {
        return true;
    } else if(/CPU like Mac OS X/i.test(navigator.userAgent)) {
        return true;
    } else {
        return false;
    }
  } else {
    return false;
  }

};


/**
 * jQuery plugins
 */
;(function ($, window, isUndefined) {

  $.support.transform = (function() {
    var elStyle = document.createElement('div').style;

    return elStyle.transition !== isUndefined ||
           elStyle.WebkitTransition !== isUndefined ||
           elStyle.MozTransition !== isUndefined ||
           elStyle.MsTransition !== isUndefined ||
           elStyle.OTransition !== isUndefined;

  })();

  /**
   * --------------------------------------------------------------------
   * Positions element in the vertical middle.
   * --------------------------------------------------------------------
   *
   * Usage: $('.element').vcenter();
   */
  $.fn.vcenter = function (options) {

    var $el = $(this);

    return $el.each(function () {

      var $p = $el.parent(),
          $offsetP = $el.offsetParent(), // Find a parent that has a position
          middle = $el.height() / -2;

          // If parent does not have position, then calculate the parent offset.
          if ($offsetP.get(0) != $p.get(0)) {
              middle += $p.position().top / 2;
          }

          $el.css('margin-top', middle);

    });

  };


  // Legacy
  $.fn.verticalCenter = function( options ) {

    var $t = $( this );

    if ( $t.length > 1 ) {

      $t.each( function () {
        $( this ).verticalCenter( options );
      });

    } else {

      var defaults = {
        preloadSiblings: true
      }

      options = $.extend( {}, defaults, options );

      var $p = $t.parent();
      var height = $t.outerHeight();
      var parentHeight = $p.height();

      if ( options.preloadSiblings ) {

        var $imageSiblings = $p.find( 'img' );

        if ( $imageSiblings.length && ( $t.data( 'child-images-loaded' ) == isUndefined ) ) {

          $t.data( 'child-images-loaded', true );

          $t.css( 'visibility', 'hidden' );

          $imageSiblings.each( function () {

            var img = new Image();

            $( img ).error( function () {

              $t.hide().css( 'visibility', 'visible' ).fadeIn();
              $t.verticalCenter();

            } ).load( function () {

              $t.hide().css( 'visibility', 'visible' ).fadeIn();
              $t.verticalCenter();

            } );

            img.src = $( this ).attr( 'src' );

          });

        }

      }

      if ( parentHeight <= height ) {
          return;
      }

      var top = Math.floor( (parentHeight - height) / 2 ),
          position = $t.css( 'position' );

      if ( ( position == 'absolute' ) || ( position == 'relative' ) ) {
        $t.css( 'top', top );
      } else {
        $t.css({
          top: top,
          position: 'relative'
        });
      }

    }

    return this;

  };


  /**
   * --------------------------------------------------------------------
   * Returns highest DOM element in the jQuery array.
   * --------------------------------------------------------------------
   */
  $.fn.highestElement = function () {

    var $el = $(this),
        elementHeight = 0,
        elementIndex = false;

    $el.each(function (index) {
      var height = $(this).outerHeight();

      if (height > elementHeight) {
        elementHeight = height;
        elementIndex = index;
      }
    });

    return elementIndex !== false ? $el.eq(elementIndex) : $('');

  };


  /**
   * --------------------------------------------------------------------
   * Changes CSS while remembering old value, so it can be restored later.
   * --------------------------------------------------------------------
   */
  $.fn.addTempCss = function(cssProp, cssValue) {

    if (this.data('temp-css-' + cssProp) == isUndefined) {
      this.data('temp-css-' + cssProp, this.css(cssProp));
    }

    return this.css(cssProp, cssValue);

  };

  $.fn.removeTempCss = function(cssProp) {

    var originalValue = this.data('temp-css-' + cssProp);

    if (originalValue) {
      this.removeData('temp-css-' + cssProp);
      this.css(cssProp, originalValue);
    }

    return this;

  };


  // hoverIntent r6 // 2011.02.26 // jQuery 1.5.1+
  $.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev])}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev])};var handleHover=function(e){var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t)}if(e.type=="mouseenter"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob)},cfg.interval)}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob)},cfg.timeout)}}};return this.bind('mouseenter',handleHover).bind('mouseleave',handleHover)};


  // jquery-mousewheel Copyright (c) 2013 Brandon Aaron (http://brandonaaron.net)
  (function(){var d=["wheel","mousewheel","DOMMouseScroll"];var f="onwheel" in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"];var e,a;if($.event.fixHooks){for(var b=d.length;b;){$.event.fixHooks[d[--b]]=$.event.mouseHooks}}$.event.special.mousewheel={setup:function(){if(this.addEventListener){for(var g=f.length;g;){this.addEventListener(f[--g],c,false)}}else{this.onmousewheel=c}},teardown:function(){if(this.removeEventListener){for(var g=f.length;g;){this.removeEventListener(f[--g],c,false)}}else{this.onmousewheel=null}}};$.fn.extend({mousewheel:function(g){return g?this.bind("mousewheel",g):this.trigger("mousewheel")},unmousewheel:function(g){return this.unbind("mousewheel",g)}});function c(g){var h=g||window.event,m=[].slice.call(arguments,1),o=0,j=0,i=0,l=0,k=0,n;g=$.event.fix(h);g.type="mousewheel";if(h.wheelDelta){o=h.wheelDelta}if(h.detail){o=h.detail*-1}if(h.deltaY){i=h.deltaY*-1;o=i}if(h.deltaX){j=h.deltaX;o=j*-1}if(h.wheelDeltaY!==undefined){i=h.wheelDeltaY}if(h.wheelDeltaX!==undefined){j=h.wheelDeltaX*-1}l=Math.abs(o);if(!e||l<e){e=l}k=Math.max(Math.abs(i),Math.abs(j));if(!a||k<a){a=k}n=o>0?"floor":"ceil";o=Math[n](o/e);j=Math[n](j/a);i=Math[n](i/a);m.unshift(g,o,j,i);return($.event.dispatch||$.event.handle).apply(this,m)}})();


  // jquery-touchwipe Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
  (function(){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})();

}(jQuery, window));


/**
 * Easing functions.
 */
jQuery.extend(jQuery.easing, {
  def: 'easeOutQuad',
  easeOutQuad: function (x, t, b, c, d) {
    return -c *(t/=d)*(t-2) + b;
  }
});


/**
 * --------------------------------------------------------------------
 * Keyboard arrows & mousewheel navigation.
 * --------------------------------------------------------------------
 */
(function ($, $window, $document, isUndefined) {

  Navigation = function (options) {

    this.options = $.extend({}, Navigation.settings, options);
    this.$items = [];
    this.$html = $('html,body');

  };

  Navigation.settings = {

    onKeyboardNext: null,
    onKeyboardPrevious: null,
    nextItem: null,
    onNextItem: null,
    previoutItem: null,
    onPreviousItem: null,
    onSetItems: null

  };

  Navigation.prototype = {

    setItems: function ($items) {

      this.$items = $items;

      this.options.onSetItems && this.options.onSetItems.call(this);

      if (this.$items && (this.$items.length > 1)) {
        this.enableMousewheel();
        this.enableKeyboard();
      }

    },

    getActive: function () {

      var center = $window.scrollLeft() + ($window.width() / 2),
          distances = [],
          distancesAbs = [],
          minDistance = 0,
          index = 0;

      this.$items.each(function () {
        var $t = $(this),
            left = parseInt($t.offset().left, 10),
            right = left + parseInt($t.width(), 10),
            distance = center - (right - $t.width() / 2);

        distances.push(distance);
        distancesAbs.push(Math.abs(distance));

      });

      minDistance = Math.min.apply(Math, distancesAbs);
      index = distancesAbs.indexOf(minDistance);

      return {
        'item': this.$items.eq(index),
        'distance': distances[index]
      };

    },

    nextItem: function () {

      if (this.options.nextItem) {
        this.options.nextItem.call(this);
        return false;
      }

      if (!this.$items.length) {
        return false;
      }

      var active = this.getActive();

      if (active.distance < -30) {

        this.scrollToItem(active.item);

      } else {

        var index = this.$items.index(active.item);
        this.scrollToItem(this.$items.eq(index + 1));

      }

      this.options.onNextItem && this.options.onNextItem.call(this);

    },

    previousItem: function () {

      if (this.options.previousItem) {
        this.options.previousItem.call(this);
        return false;
      }

      if (!this.$items.length) {
        return false;
      }

      var active = this.getActive();

      if (active.distance > 30) {

        this.scrollToItem(active.item);

      } else {

        var index = this.$items.index(active.item);
        if (0 !== index) {
          this.scrollToItem(this.$items.eq(index - 1));
        } else {
          this.scrollTo(0);
        }

      }

      this.options.onPreviousItem && this.options.onPreviousItem.call(this);

    },

    enableMousewheel: function() {

      var that = this;

      $window.on('mousewheel.gp', _.debounce(function (e, delta, deltaX, deltaY) {
        if ((0 === deltaX) && ($(e.target).closest('.scroll-container').length === 0)) {
          deltaY < 0 ? that.nextItem() : that.previousItem();
        }
      }));

    },

    scrollTo: function (x) {

      if ($window.width() >= $document.width()) {
        return;
      }

      this.$html.not(':animated').animate({ scrollLeft: x }, 300);

    },

    scrollToItem: function ($item) {

      if ($item && $item.length && ($window.width() < $document.width())) {

        this.scrollTo($item.offset().left - ($window.width() - $item.width())  / 2);

      }

    },

    enableKeyboard: function () {
      var that = this;

      $window.on('keydown.navigation.gp', function (e) {

          if (!that.$items.length) {
            return true;
          }

          // on right arrow click
          if (39 == e.which) {

            if (that.options.onKeyboardNext) {
              if (that.options.onKeyboardNext.apply(that, e)) {
                that.nextItem();
              }
            } else {
              that.nextItem();
            }

            return false;
          }

          // on left arrow click
          if (37 == e.which) {

            if (that.options.onKeyboardPrevious) {
              if (that.options.onKeyboardPrevious.apply(that, e)) {
                that.previousItem();
              }
            } else {
              that.previousItem();
            }

            return false;
          }

      });

    },

    disableKeyboard: function () {
      $window.off('keydown.navigation.gp');
    }

  };

})(jQuery, jQuery(window), jQuery(document));


(function ( $, window, undefined ) {

  $.Appreciate = function( options, element, callback ) {

    this.$element     = $( element );
    this.callback     = callback;

    this.options      = $.extend( {}, $.Appreciate.settings, options );

    this.ajaxurl      = this.$element.data( 'ajaxurl' );
    this.id           = this.$element.data( 'id' );
    this.title_after  = this.$element.data( 'title-after' );

    this._init();

  };

  $.Appreciate.settings = {

    template: '<span class="icon"></span><b class="numbers">{count}</b>'

  };

  $.Appreciate.prototype = {

    _init: function () {

      var self = this;

      this.count = this.$element.data( 'count' );
      if ( !this.count ) {
        this.count = 0;
      }

      if ( this.$element.is( '.has-appreciated' ) ) {
        this.$element.find( '.appreciate-title' ).html( this.title_after );
      }

      this.$template = this.options.template.replace( '{count}', this.count );
      this.$template = $( this.$template );

      this.$element.append(
          this.$template
        );

      this.$element.click( function () {

        var $t = $( this );
        if ( $t.is( '.has-appreciated' ) ) {
          return false;
        }

        self.count++;

        self.$element.find( '.numbers' ).html( self.count );
        self.$element.find( '.appreciate-title' ).html( this.title_after );

        if ( self.ajaxurl != undefined ) {

          $.post( self.ajaxurl, {
            action: 'appreciate',
            post_id: self.id
          });

        }

        $t.addClass( 'has-appreciated' );

        return false;

      });

    }

  }

  $.fn.appreciate = function( options, callback ) {

   this.data( 'apprecaite', new $.Appreciate( options, this, callback ) );
   return this;

  }

})( jQuery, window );


(function ( $, window, undefined ) {

  MobileNav = function( $items, options ) {

    this.options = $.extend( MobileNav.settings, options );
    this.$items  = $items;

    this._create();

  }

  MobileNav.settings = {
    openButtonTitle: 'Menu',
    minWindowWidth: 480
  }

  MobileNav.prototype = {

    _create: function () {

      var self = this;

      /**
       * Create mobile menu DOM element.
       */
      var menuTemplate = '' +
        '<div id="mobilenav" class="closed">' +
          '<a href="#" class="btn-open">' + this.options.openButtonTitle + '</a>' +
          '<nav></nav>' +
        '</div>';

      var $menu = $( menuTemplate );
      var $nav  = $menu.find('nav');

      this.$items.each(function () {
        var level = 1;
        var $t = $(this);
        if ( $t.data('level') != undefined ) {
          level = $t.data('level');
        }

        var $a = $('<a />').attr({
                    href: $t.attr('href')
                  }).html($t.html());

        $a.addClass('level-' + level);

        if ( self.options.active.get(0) == $t.get(0) ) {
          $a.addClass('active');
        }

        $nav.append( $a );
      });

      $( 'body' ).append( $menu );
      this.$menu = $menu;

      $menu.css({
        left: $menu.outerWidth() * -1
      })

      /**
       * Open / Close button functionality.
       */
      $menu.find( '.btn-open' ).click( function () {
        if ( $menu.is( '.closed' ) ) {
          self.open();
        } else {
          self.close();
        }
        return false;
      });

    },


    open: function () {

      var windowHeight = $(window).height();
      var docHeight    = $(document).height();

      this.$menu.removeClass('closed').addClass('opened');

      this.$menu.css({
        height: windowHeight > docHeight ? windowHeight : docHeight
      }).animate({
        left: 0
      }, 300);

    },


    close: function () {

      this.$menu.removeClass('opened').addClass('closed');
      this.$menu.animate({
        left: this.$menu.outerWidth() * -1
      }, 300);

    }


  }

})( jQuery, window );

