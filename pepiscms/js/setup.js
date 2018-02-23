(function ($) {
    var $selectbox = $("#cms_customization_logo_predefined");
    var $logoRow = $('#field_cms_customization_logo');

    function toggleLogo() {
        if ($selectbox.val()) {
            $logoRow.hide();
        }
        else {
            $logoRow.show();
        }
    }

    $selectbox.change(toggleLogo);
    toggleLogo();
})(jQuery);