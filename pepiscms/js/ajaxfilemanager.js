function FileManagerUI(transtlation_map, is_intranet) {

    var current_path = '/';
    var parent_path = '';
    var allowed_extensions = []; // Must be an array


    var base_path = './admin/ajaxfilemanager/';
    var browse_path = 'browse/';
    var on_file_click_function = null;

    var dialog = {
        list_of_supported_filetypes_is_empty: "The list of supported file extensions is empty. File upload not allowed.",
        filetype_not_allowed: "The filetype you are attempting to upload is not allowed.\nFile extension: ",
        empty_directory: "The directory is empty. Upload a file or create a directory.",
        uploads_directory_is_missing: "Uploads directory is missing",
        new_folder_name: "Name for new folder:",
        new_file_name_for: "New filename for ",
        loading_list_of_files: "Loading the list of files..."
    };


    //--------------------------------------------------------------------------------
    //
    // The code
    //
    //--------------------------------------------------------------------------------
    this.init = function () {

        if (transtlation_map instanceof Object) {
            if (transtlation_map.label_new_folder_name) {
                dialog.new_folder_name = transtlation_map.label_new_folder_name;
            }
            if (transtlation_map.label_new_file_name_for) {
                dialog.new_file_name_for = transtlation_map.label_new_file_name_for;
            }
            if (transtlation_map.label_loading_list_of_files) {
                dialog.loading_list_of_files = transtlation_map.label_loading_list_of_files;
            }
        }


        $("#disabled_javascript").hide();
        $("#enabled_javascript").show();

        if (window.location.hash.length > 0) {
            current_path = window.location.hash.substring(1, window.location.hash.length);
        }
        redrawTable(current_path);


        //--------------------------------------------------------------------------------
        //
        // Ajax upload
        //
        //--------------------------------------------------------------------------------
        var ajaxUpload = new AjaxUpload('upload_file_button', {
            action: base_path + '/upload/',
            name: 'file',
            autoSubmit: false,
            responseType: "json",
            onChange: function (file, extension) {

                extension = extension.toLowerCase();

                if (!(allowed_extensions instanceof Array) || allowed_extensions.length == 0) {
                    displayErrors([dialog.list_of_supported_filetypes_is_empty]);
                    return;
                }

                extension = extension.toString().trim();
                if (allowed_extensions.indexOf(extension) == -1) {
                    displayErrors([dialog.filetype_not_allowed + extension + "."]);
                    return;
                }
                ajaxUpload.setData({
                    path: current_path
                });

                ajaxUpload.submit();
            },
            onSubmit: function (file, extension) {

                $("#upload_file_progress").show();
                $("#menu, #header, #footer, #content #enabled_javascript div").css('opacity', 0.3);
                $("#upload_file_progress").css('opacity', 1);
                $("#filemanager_grid, .buttons, .path").hide();
            },
            onComplete: function (file, response) {
                $("#upload_file_progress").hide();
                $("#filemanager_grid, .buttons, .path").show();
                $("#menu, #header, #footer, #content #enabled_javascript div").css('opacity', 1);

                if (response.status != 1) {
                    displayErrors(response.errors);
                    $("#filemanager_grid").show();
                    return;
                }
                redrawTable(current_path);
            }
        });
    };

    this.setBrowsePath = function (b_p) {
        browse_path = b_p;
    };
    this.setAllowedExtensions = function (a_e) {
        allowed_extensions = a_e;
    };

    this.onFileClick = function (o_f_c) {
        on_file_click_function = o_f_c;
    };

    //--------------------------------------------------------------------------------
    //
    // Utilities functions
    //
    //--------------------------------------------------------------------------------
    function getParentPath(path) {
        if (path.length != -1) {
            path = path.substr(0, path.length - 1);
        }

        var last_index = path.lastIndexOf('/');
        if (last_index == -1) {
            return "";
        }
        return path.substr(0, (last_index));
    }

    function size_format(filesize) {
        if (filesize >= 1073741824) {
            return (filesize / 1073741824).toFixed(2) + ' Gb';
        } else if (filesize >= 1048576) {
            return (filesize / 1048576).toFixed(2) + ' Mb';
        } else if (filesize >= 1024) {
            return (filesize / 1024).toFixed(2) + ' Kb';
        }

        return Math.round(filesize, 0) + ' b';
    }

    function redrawPathBar(path) {
        path = path.split('/');

        var path_string = '';
        var path_segments = '';

        for (segment in path) {
            if (path[segment] == '') {
                continue;
            }

            path_segments += '/' + path[segment];

            path_string += ' <a href="' + base_path + browse_path + '#' + path_segments + '" class="filemanager_nav_link" title="' + path_segments + '">' + path[segment] + '</a>';
        }
        $(".path").html('<a href="' + base_path + browse_path + '#" class="filemanager_nav_link" title="">uploads</a>' + path_string);
        ppLib.addBeak('.breadcrumbs', 'a');
    }


    //--------------------------------------------------------------------------------
    //
    // HTML manipulation functions
    //
    //--------------------------------------------------------------------------------
    function redrawTable(_path) {
        window.location.hash = '#' + _path;
        //$(".path").html('<strong>'+label_loading_list_of_files+'</strong>');

        $("#filemanager_grid").attr('disabled', 'disabled');

        $("#filemanager_grid").css('opacity', 0.5);
        $.post(base_path + "getjsonfilelist/", {
            path: _path
        }, function (data) {
            $("#filemanager_grid").css('opacity', 1);

            $("#filemanager_grid").attr('disabled', '');

            if (data.status == 0) {
                if (_path != '/') {
                    alert(_path + "\n\n" + data.message);
                    window.location = base_path + browse_path;
                }
                else {
                    displayErrors([dialog.uploads_directory_is_missing]);
                }
                return false;
            }
            else if (data.status == 1) {
                $("tr.filemanager_row").remove();
                var html = '';
                current_path = data.path;
                redrawPathBar(current_path);

                parent_path = getParentPath(current_path);

                var odd = true;

                if (current_path != "" && current_path != "/") {
                    html += '<tr class="filemanager_row ' + (odd ? 'odd' : 'even') + '">';
                    html += '<td class="link"></td>';
                    html += '<td class="link"><a href="' + base_path + browse_path + '#' + parent_path + '" title="' + parent_path + '" class="filemanager_nav_link"><img src="pepiscms/theme/img/ajaxfilemanager/folder_32.png" alt="file icon" /></a></td>';
                    html += '<td><a href="' + base_path + browse_path + '#' + parent_path + '" title="' + parent_path + '" class="filemanager_nav_link label"><b>..</b></a></td>';
                    html += '<td class="link"></td>';
                    html += '<td class="link"></td>';
                    //html += '<td></td>';
                    //html += '<td></td>';
                    html += '</tr>';
                }

                // Displays message that the directory is empty
                if (data.directories.length == 0 && data.files.length == 0) {
                    html += '<tr class="filemanager_row ' + (odd ? 'odd' : 'even') + '">';
                    html += '<td class="link"></td>';
                    html += '<td class="link"></td>';
                    html += '<td colspan="3">' + dialog.empty_directory + '</td>';
                    html += '</tr>';
                }
                else {
                    $.each(data.directories, function (i, item) {

                        odd = !odd;
                        html += '<tr class="filemanager_row ' + (odd ? 'odd' : 'even') + '">';
                        html += '<td class="link"><input type="checkbox" name="files[]" class="path" value="' + item.name + '" /></td>';
                        html += '<td class="link"><a href="' + base_path + browse_path + '#' + current_path + item.name + '" title="' + current_path + item.name + '" class="filemanager_nav_link"><img src="pepiscms/theme/img/ajaxfilemanager/folder_32.png" alt="file icon" /></a></td>';
                        html += '<td><a href="' + base_path + browse_path + '#' + current_path + item.name + '" title="' + current_path + item.name + '" class="filemanager_nav_link label"><b>' + item.name + '</b></a></td>';
                        html += '<td class="link"><a class="rename_button" value="' + item.name + '" href="#"><img src="pepiscms/theme/img/ajaxfilemanager/rename_16.png" alt="" /></a></td>';
                        html += '<td class="link">-</td>';
                        html += '</tr>';
                    });


                    var link_target = 'target="_blank"'; //is_intranet ? '' : 'target="_blank"';
                    var link_path = '';

                    $.each(data.files, function (i, item) {

                        odd = !odd;

                        html += '<tr class="filemanager_row ' + (odd ? 'odd' : 'even') + '">';
                        html += '<td class="link"><input type="checkbox" name="files[]" class="path" value="' + item.name + '" /></td>';

                        link_path = current_path + item.name;

                        ext = getFileExtension(item.name);
                        if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif') {
                            html += '<td class="link"><a href="uploads' + link_path + '" ' + link_target + ' class="filemanager_file_link image" rel="groupi" title="' + item.name + '"><img data-src="' + base_path + 'thumb/' + current_path + item.name + '" alt="" /></a></td>';
                            html += '<td><a href="uploads' + link_path + '" ' + link_target + ' class="filemanager_file_link image label" rel="groupt" title="' + item.name + '">' + item.name + '</a></td>';
                        }
                        else {
                            html += '<td class="link"><a href="uploads' + link_path + '" ' + link_target + ' class="filemanager_file_link">';
                            html += '<img src="pepiscms/theme/file_extensions/' + getFileIcon(ext) + '" alt="file icon" />';
                            html += '</a></td>';
                            html += '<td><a href="uploads' + link_path + '" ' + link_target + ' class="filemanager_file_link label">' + item.name + '</a></td>';
                        }


                        html += '<td class="link"><a class="rename_button" value="' + item.name + '" href="#"><img src="pepiscms/theme/img/ajaxfilemanager/rename_16.png" alt="" /></a></td>';
                        html += '<td>' + size_format(item.size) + '</td>';
                        html += '</tr>';
                    });
                }


                $("#filemanager_grid tbody").html(html);

                window.lazyload.update();

                $("a.filemanager_nav_link").click(function (event) {
                    event.stopPropagation();
                    event.preventDefault();
                    current_path = $(this).attr("title") + "/";
                    redrawTable(current_path);
                });


                $("a.filemanager_file_link").click(function (event) {

                    if (typeof on_file_click_function == 'function') {
                        event.stopPropagation();
                        event.preventDefault();
                        on_file_click_function($(this).attr("href"));
                    }

                });


                $("a.rename_button").click(function (event) {
                    var filename = $(this).attr("value");
                    var newfilename = "";

                    event.stopPropagation();
                    event.preventDefault();

                    while (newfilename == "") {
                        newfilename = prompt(dialog.new_file_name_for + filename, filename);
                        if (newfilename == null)
                            return;
                    }

                    if (filename != newfilename) {
                        sendCommand('rename', current_path, new Array(filename, newfilename));
                    }
                });

                // The inerface call
                $(document).trigger('interface-ready');
            }
        }, 'json');
    }


    //--------------------------------------------------------------------------------
    //
    // Events
    //
    //--------------------------------------------------------------------------------

    function getCheckedFiles() {
        var files = new Array();
        $("#filemanager input.path:checked").each(function (i, element) {
            files.push(element.value);
        });
        return files;
    }

    function sendCommand(_command, _path, _files, _new_location) {
        if (!(_files instanceof Array))
            _files = new Array();

        $.post(base_path + "sendcommand/", {
            command: _command,
            path: _path,
            files: _files.join('/'),
            new_location: _new_location
        }, function (data) {

            if (data.status == 1 || data.status == 2) {
                $("#delete_files_box").hide();
                $("#move_files_box").hide();
                $("#create_folder_box").hide();

                $("#filemanager").show();

                redrawTable(current_path);
            }
            else {
                // Lets call it a fatal error
                if (data.status == 0) {
                    displayErrors(data.errors);
                }
            }

        }, 'json').fail(function(response) {
            alert('Error: ' + response.responseText);
        });
    }

    function displayErrors(errors) {
        $("#error_boxes").html('').css('opacity', 1);
        html = '';
        for (var i = 0; i < errors.length; i++) {
            html += '<div class="dialog_box error"><img src="pepiscms/theme/img/dialog/messages/error_32.png" alt="" />';
            html += '<p>' + errors[i] + '</p>';
            html += '</div>';
            html += '<div style="clear: both"></div>';
        }

        $("#error_boxes").html(html);
        $("#error_boxes").slideDown(300);
        setTimeout(function () {
            $("#error_boxes").animate({
                opacity: 0
            }, 2400).slideUp(300);
        }, 4000);
    }

    $("#delete_files_button").click(function (event) {
        var files = getCheckedFiles();
        if (files.length == 0) {
            return;
        }

        var fileList = document.createElement('ul');
        $(fileList).addClass('file_list');

        $("#filemanager").hide();
        $('#delete_files_box .filelistContainer').empty();
        $("#delete_files_box").show();

        var items = [];
        for (var i = 0; i < files.length; i++) {
            console.log(files[i]);
            items.push('<li><img src="pepiscms/theme/file_extensions/' + getFileIcon(getFileExtension(files[i])) + '" alt="file icon" /><span>' + files[i] + '</span></li>');
            $(fileList).append(items[i]);
        }
        $(fileList).appendTo('#delete_files_box .filelistContainer');
        ppLib.applyEvens('ul.file_list', 'li');
    });

    $("#delete_files_commit_button").click(function (event) {
        sendCommand('delete', current_path, getCheckedFiles());
    });


    $("#move_files_button").click(function (event) {
        var files = getCheckedFiles();
        if (files.length == 0) {
            return;
        }

        $("#move_files").val(current_path);
        $("#filemanager").hide();
        $("#move_files_box").show();
        $("#move_files").focus();

        var fileList = document.createElement('ul');
        $(fileList).addClass('file_list');

        var items = [];
        for (var i = 0; i < files.length; i++) {
            items.push('<li><img src="pepiscms/theme/file_extensions/' + getFileIcon(getFileExtension(files[i])) + '" alt="file icon" /><span>' + files[i] + '</span></li>');
            $(fileList).append(items[i]);
        }
        $(fileList).appendTo('#move_files_box .filelistContainer');
        ppLib.applyEvens('ul.file_list', 'li');
    });

    $("#move_files_commit_button").click(function (event) {
        var new_location = $("#move_files").val();
        if (new_location.length > 0) {
            sendCommand('move', current_path, getCheckedFiles(), new_location);
        }
    });
    $("#filemanagerform").submit(function (event) {
        event.stopPropagation();
        event.preventDefault();
    });

    $("#create_folder_button").click(function (event) {
        var newfilename = "";
        while (newfilename == "") {
            newfilename = prompt(dialog.new_folder_name, "");
            if (newfilename == null || newfilename == "")
                return;
        }
        sendCommand('create', current_path, false, newfilename);

    });
    $(".filemanager_cancelbutton").click(function (event) {

        event.stopPropagation();
        event.preventDefault();

        $("#filemanager").show();
        $("#create_folder_box, #move_files_box, #delete_files_box, #select_files_box").hide();
        $('#move_files_box .filelistContainer *').remove();
    });
}