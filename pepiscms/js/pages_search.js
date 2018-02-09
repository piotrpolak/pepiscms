$(document).ready(function() {

    $("#searchbox_container").css("display", "inline");

    $("#searchbox").keypress(function(e) {

        var searched_value = ($("#searchbox").val().toString()).toLowerCase();

        if (e.charCode == 0)
        {
            searched_value = "";
            $("#searchbox").val("");
            $("table tr.empty").show();
        }

        $("table tr").show();
        $("table tr.empty").hide();

        $("table tr").each(function(i) {
            var value = $(this).children("td.first").children("span.menu_element_name").children("a").html();

            if (value != null && value.toLowerCase().indexOf(searched_value, 0) == -1)
            {
                $(this).hide();
            }
        });
    });

    $("#searchbox_reset_button").click(function(event) {
        $("#searchbox").val("");
        $("table tr").show();
        $("table tr.empty").show();
        event.stopPropagation();
        event.preventDefault();
        return false;
    });
});