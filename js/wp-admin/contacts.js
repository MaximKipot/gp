
(function ($) {

    $(function () {

        var $btnAdd = $('#gp-add-contact');
        var $container = $('.gp-contact-information tbody');

        $btnAdd.click(function () {

                var $title = $('input[name="gp_contact_info_add_title"]');
                var $content = $('textarea[name="gp_contact_info_add_content"]');

                var $newElement = $('<tr class="gp-contact" />');
                $('<input type="text" name="gp_contact_info_title[]" />').val($title.val())
                                                                             .appendTo($newElement)
                                                                             .wrap('<td />');

                $('<textarea name="gp_contact_info_content[]" />').val($content.val())
                                                                      .appendTo($newElement)
                                                                      .wrap('<td />');

                $newElement.insertBefore($container.find('.add-element'));

                $title.val('');
                $content.val('');

                return false;

            });

    });

})(jQuery);