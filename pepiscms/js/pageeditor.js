/*
 Used for hiding the menu element name textbox in pages edit/write
 */
function PageEditorUI(translation_map) {

    var slide_duration = 300;
    var label_unsaved_changes = "All unsaved changes will be lost. Proceed?";

    this.init = function () {
        if (translation_map.label_unsaved_changes)
            label_unsaved_changes = translation_map.label_unsaved_changes;


        if ($("select[name=parent_item_id]").val() == -1) {
            $("#field_item_name").hide();
        }
        $("#secondarymenu, #header").hide();


        $("select[name=parent_item_id]").change(function (event) {
            if ($(this).val() == -1) {
                $("#field_item_name").hide();
            }
            else {
                $("#field_item_name").show();
            }
            return false;
        });

        $("#menu a").click(function (event) {

            var r = confirm(label_unsaved_changes);
            if (r == true) {
                return;
            }
            event.stopPropagation();
            event.preventDefault();
        });


        $("select[name=parent_item_id]").change(function () {
            if ($(this).val() != -1 && $("#item_name").val() == '') {
                $("input[name=item_name]").val($("input[name=page_title]").val());
            }
        });
    }
}