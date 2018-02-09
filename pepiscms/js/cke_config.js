CKEDITOR.editorConfig = function (config) {

    // http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
    config.toolbar =
        [
            ['Format'], ['Styles'], ['Bold', 'Italic', 'Underline', 'Strike', 'NumberedList', 'BulletedList'], //,,'-''Nbsp'
            ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', 'RemoveFormat'],
            ['Undo', 'Redo', '-', 'Find', 'Replace'],
            ['Blockquote'],
            ['Link', 'Unlink'],
            ['Image', 'Flash', 'Table', 'SpecialChar'],
            ['Maximize', 'ShowBlocks', '-', 'Source', '-', 'About']
        ];
    config.toolbarCanCollapse = true;

    config.entities_latin                   = false;
    config.pasteFromWordRemoveFontStyles    = true;
    config.pasteFromWordRemoveStyles        = true;
    config.resize_minWidth                  = '100%';
    config.resize_maxWidth                  = '100%';
    config.startupFocus                     = false; // Causes some problems under firefox
    config.allowedContent                   = true; // Prevent the iframes from being stripped
};