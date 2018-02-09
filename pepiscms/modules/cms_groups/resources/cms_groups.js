(function ($) {
    $('td').click(function () {
        $checkbox = $('input', $(this));
        if ($checkbox.length) {
            $checkboxes = $('input[type=radio]', $(this).parent());
            $checkboxes.prop('checked', false);
            $checkbox.prop('checked', true).trigger('change');
        }
    });
})(jQuery);