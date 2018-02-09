(function ($) {
    var $selectbox = $("#cms_customization_logo_predefined");
    var $logoRow = $('#field_cms_customization_logo');

    function doAction() {
        if ($selectbox.val()) {
            $logoRow.hide();
        }
        else {
            $logoRow.show();
        }
    }

    $selectbox.change(doAction);
    doAction();

    //$.each($('option', $selectbox), function(index, element)
    //{
    //    $element = $(element);
    //c
    //    $element.css({'background-image': 'url(pepiscms/theme/img/customization_icons/'+$element.val()+')'});
    //
    //});
})(jQuery);