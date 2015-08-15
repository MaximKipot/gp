
var log = console.log ? console.log : function () {};

(function ($) {

    $(function () {

        $('#project-type-layout').each(function () {
            var $t = $(this),
                $option = $('#project-type-layout-option');

            $t.on('change keydown', function () {
                $t.val() == 'grid' ? $option.show() : $option.hide();
            }).change();
        });

    });

})(jQuery);