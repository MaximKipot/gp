
( function ( $, window, undefined ) {

    $( function () {

        $( '#gp-set-content-position' ).click(function () {
            var $t = $( this );
            window.open( $t.attr( 'href' ), 'positionWindow' );
            return false;
        });

        var $box = $( '.info' );
        var $parent = $box.parent();

        $box.draggable({
            containment: $parent,
            scroll: false
        });

        $( '#btn-save-position' ).click( function () {

            /**
             * Position in %
             */
            var x = $box.position().left / $parent.width();
            var y = $box.position().top / $parent.height();

            x = Math.round(x * 10000) / 100;
            y = Math.round(y * 10000) / 100;

            var $opener = window.opener.jQuery( '#gp_background_meta_box' );

            $opener.find( '[name=gp_box_position_left]' ).val( x + '%' );
            $opener.find( '[name=gp_box_position_top]' ).val( y + '%' );

            $opener.find( '#gp-content-position .left' ).html( x + '%' );
            $opener.find( '#gp-content-position .top' ).html( y + '%' );

            window.close();

            return false;

        })

    });

})( jQuery, window );