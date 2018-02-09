(function ($) {
    /**
     * Appends SQL editor code
     *
     * @param code
     * @param replace
     */
    function insertCodeToEditor(code, replace) {
        if (replace == true) {
            $('#sql_input').val(code);
        }
        else {
            $('#sql_input').val($('#sql_input').val() + code);
        }

        onSqlInputChange();
    }

    /**
     * Handler fired on SQL editor change, hides clear button when console is empty
     *
     * @param event
     */
    function onSqlInputChange(event) {
        if ($('#sql_input').val() == '') {
            $('#clear').hide();
        }
        else {
            $('#clear').show();
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Handlers
    // -----------------------------------------------------------------------------------------------------------------


    /**
     * On table name click
     */
    $('ul.tables > li > a').click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        var table = $(this).attr('title');

        $('#table_names ul > li > a').removeClass('active');
        $('.fields', $(this).parent()).show();

        $(this).addClass('active');
        $('.table_queries').hide();
        $('#table_queries_' + table).show();
    });


    /**
     * On query click
     */
    $('a.sql').click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        var sqlQuery = $(this).attr('title');

        // Adds a new line
        if ($(this).hasClass('nl')) {
            sqlQuery = "\n" + sqlQuery;
        }

        // Replaces/appends input
        insertCodeToEditor(sqlQuery, $(this).hasClass('replace'));

        // Focuses on the input
        if ($(this).hasClass('focus')) {
            $('#sql_input').focus();
        }
    });

    /**
     * On clear button click
     */
    $('#clear').click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        insertCodeToEditor('', true);

        $('#resultset').remove();
        $('div.error').fadeOut(800);
        $('div.error').fadeOut(800);
        $('.field').hide();
        onSqlInputChange();
    });

    /**
     * Constructor calls
     */
    $('#table_queries div.table_queries:first').show();
    $('#sql_input').bind('input propertychange', onSqlInputChange);
    onSqlInputChange();
})(jQuery);