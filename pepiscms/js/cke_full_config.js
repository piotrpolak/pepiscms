CKEDITOR.editorConfig = function (config) {

    // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
    config.toolbar =
        [
            ['Save', 'Preview'],
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'],
            ['Undo', 'Redo', '-', 'Find', 'Replace', '-', 'RemoveFormat'],
            ['Blockquote'],
            ['Link', 'Unlink', 'Anchor'],
            ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'],
            '/',
            ['Format', 'Font', 'FontSize', 'Styles'],
            ['Bold', 'Italic', 'Underline', 'Strike', 'NumberedList', 'BulletedList'], //,'Nbsp','-'
            ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],

            ['TextColor', 'BGColor'],
            ['Maximize', 'ShowBlocks', '-', 'Source', '-', 'About']
        ];
    config.toolbarCanCollapse = true;
    config.entities_latin = false;
    config.pasteFromWordRemoveFontStyles = true;
    config.pasteFromWordRemoveStyles = true;
    config.resize_minWidth = '100%';
    config.resize_maxWidth = '100%';
    config.startupFocus = false; // Causes some problems under firefox
    config.allowedContent = true; // Prevent the iframes from being stripped
};