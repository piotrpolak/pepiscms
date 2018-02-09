(function ($) {
    var checkbox = $("#is_root");

    if (checkbox.length > 0) {
        if (checkbox.attr('checked')) {
            $('#field_groups').hide();
        }
        else {
            $('#field_groups').show();
        }
    }
    checkbox.change(function (event) {
        $('#field_groups').show();
        if (!$(this).is(':checked')) {
            $('#field_groups').show();
        }
        else {
            $('#field_groups').hide();
        }
    });
})(jQuery);