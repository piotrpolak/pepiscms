var serverOffsetSec = 0; // This should be computed just once

$(document).on('ready', function () {
    ppLib.fixLayout();
    ppLib.resizeText('#font_resize_bar a');
    ppLib.navFilesTree('.file_tree', '.has_items a');

    ppLib.helpBubbles('#session_counter .help_icon', 'data-hint', 'white');
    ppLib.helpBubbles('form a, ul.dashboard_actions li a, .formbuilder a.question, .datagrid a');

    ppLib.hideSuccessAfterAWhile('div.success');
    ppLib.doFancyBox('a.image');
    ppLib.makeTablesBeautiful('table.datagrid');
    ppLib.makeInterfaceButtonsToWork();
    ppLib.makeLinksConfirmable('a.ask_for_confirmation');
    ppLib.makeHeavyOperationsWork('a.heavy_operation');

    if (typeof (sessionNowUTC) !== 'undefined') {
        var today = new Date();
        var todaySecsUTC = Math.floor(today.getTime() / 1000 + (today.getTimezoneOffset() * 60));
        serverOffsetSec = Math.floor(todaySecsUTC - sessionNowUTC);

        ppLib.sessionCounter(sessionTimeoutUTC, serverOffsetSec);
        ppLib.sessionCounterRefresh();
    }
});


$(document).on('interface-ready', function () {
    ppLib.doFancyBox('a.image');
});

var ppLib = {

    /** Changes input[submit].button to BUTTONs and apply icons to each of it */
    makeInterfaceButtonsToWork: function () {
        $('input[type=submit].button:visible').each(function () { // a.button:visible

            var $button = $(this);

            /* creating destination button */
            var $newButton = $(document.createElement('button'));
            $newButton.attr('id', id);
            $newButton.attr('name', $button.attr('name'));

            /* creating unique random ID... */
            var id = $(this).attr("id");
            if (!id) {
                id = Math.floor(Math.random() * 1000);
            }
            id = 'tmp_id_' + id;

            /* creating button icon */
            var buttonIcon = new Image();
            buttonIcon.setAttribute('alt', 'icon');
            var iconPath = 'pepiscms/theme/img/dialog/buttons/'; // pepiscms is always mapped! do not prepend!

            /* if element has data-icon attribute, get icon's path from it's value */
            if ($(this).attr('data-icon')) {
                buttonIcon.src = $button.attr('data-icon');
            }
            /* otherwise... */
            else {
                var icons = [];
                icons['create_folder'] = 'folder_add_16.png';
                icons['move_files'] = 'folder_go_16.png';
                icons['button.delete_files'] = 'folder_delete_16.png';
                icons['upload_file'] = 'upload_16.png';
                icons['cancel'] = 'cancel_16.png';
                icons['save'] = 'save_16.png';
                icons['apply'] = 'apply_16.png';
                icons['next'] = 'next_16.png';
                icons['previous'] = 'previous_16.png';
                icons['filter_clear'] = 'filter_clear_16.png';
                icons['filter_apply'] = 'filter_apply_16.png';
                icons['back'] = 'back_16.png';

                var classes = $button.attr('class').split(' ');
                for (var i = 0; i < classes.length; i++) {
                    if ((classes[i] == ('button')) || (classes[i] == '')) {
                        continue;
                    }
                    else {
                        //alert(classes[i]);
                        buttonIcon.src = iconPath + icons[classes[i]];
                        $newButton.addClass(classes[i]);
                    }
                }

            }

            /* adding button to the DOM tree */
            $button.before($newButton);

            $newButton.text($(this).val() ? $(this).val() : $(this).text());
            $newButton.prepend(buttonIcon);

            /* copying events (click and/or submit) from source element to new one */
            function copyElementEvents(fromElement, toElement) {
                var srcEvs = fromElement.data('events');
                for (var evType in srcEvs) {
                    for (var idx in srcEvs[evType]) {
                        toElement[evType](srcEvs[evType][idx].handler);
                    }
                }

            }
            copyElementEvents($button, $newButton);

            if ($button.is('a')) {
                var bHref = $button.attr('href');
                $($newButton).click(function () {
                    location.href = bHref;
                });
            }
            /* remove old input[type=submit] or a.button */
            $button.detach();

        });


        /*
         * Makes links with the following classes to act as if they were commit buttons
         */
        $('a.save, a.submit').click(function (event) {
            $(this).parents('form').submit();
        });
    },


    /* Highligting table rows*/
    makeTablesBeautiful: function (handler) {
        handler = $(handler);
        $("td", handler).hover(
            function (event) {
                $(this).parent().children("td").addClass("hovered");
            },
            function (event) {
                $(this).parent().children("td").removeClass("hovered");
            }
        );

        /* Datagrid actions */
        $('a.remove_form_image, a.remove_form_file').click(function (event) {
            event.stopPropagation();
            event.preventDefault();

            var name = $(this).attr('rel');
            $('input[name=\'form_builder_files_remove[' + name + ']\']').val(1);
            $(this).closest('.input').find('input.inputImage, input.inputFile').fadeIn(500);
            $(this).closest('.form_image').fadeOut(500);

        });

        /* Move datagrid items up/down */
        $('table tr td a.moveUp.json, table tr td a.moveDown.json').click(function (event) {

            event.stopPropagation();
            event.preventDefault();
            var doRequest = false;
            var callback = false;
            var that = this;

            if ($(this).hasClass('moveUp')) {
                var prevParentTr = $(this).parent().parent().prev();
                if (prevParentTr.children('td').length) {
                    doRequest = true;
                    callback = function () {
                        $(that).parent().parent().prev().insertAfter($(that).parent().parent())
                    };
                }
            }
            else {
                var nextParentTr = $(this).parent().parent().next();
                if (nextParentTr.children('td').length) {
                    doRequest = true;
                    callback = function () {
                        $(that).parent().parent().next().insertBefore($(that).parent().parent());
                    };

                }
            }

            if (doRequest) {
                $.getJSON($(this).attr('href') + '/json-1', function (data) {
                    if (data.status != 1) {
                        alert(data.message);
                    }
                    else {
                        callback.call(callback);
                        $('table tr:even').removeClass('odd even').addClass('even');
                        $('table tr:odd').removeClass('odd even').addClass('odd');
                        $(that).parent().parent().removeClass('odd even');
                        $(that).parent().parent().addClass('flash');
                        setTimeout(function(){
                            $(that).parent().parent().removeClass('flash');
                            $('table tr:even').removeClass('odd even').addClass('even');
                            $('table tr:odd').removeClass('odd even').addClass('odd');
                        }, 500);
                    }
                });
            }
        });

        $('table tr td a.delete.json').click(function (event) {
            event.stopPropagation();
            event.preventDefault();

            var r = confirm('Are you sure?');
            if (r != true) {
                return;
            }

            var $row = $(this).parent().parent();
            $row.hide();

            $.getJSON($(this).attr('href') + '/json-1', function (data) {
                if (data.status != 1) {
                    alert(data.message);
                    $row.show();
                }
            });
        });

        // Adding classes
        $('table tr:even').addClass('even');
        $('table tr:odd').addClass('odd');


        /*
         * Toggle
         */
        function assignEventHandlersForToggle() {
            $('table a.toggle').mouseover(function () {
                $(this).addClass('off');
                $(this).removeClass('on');
            });
            $('table a.toggle').mouseout(function () {
                if ($(this).attr('rel') == 'donfireevents') {
                    $(this).attr('rel', '');
                    return
                }
                $(this).addClass('on');
                $(this).removeClass('off');
            });
            $('table a.off').mouseover(function () {
                $(this).addClass('on');
                $(this).removeClass('off');
            });
            $('table a.off').mouseout(function () {
                if ($(this).attr('rel') == 'donfireevents') {
                    $(this).attr('rel', '');
                    return
                }
                $(this).addClass('off');
                $(this).removeClass('on');
            });
        }

        assignEventHandlersForToggle();
        $('table a.toggle').click(function (event) {
            event.stopPropagation();
            event.preventDefault();

            var toggle = 1;


            if ($(this).hasClass('on')) {
                toggle = 0;
                $(this).removeClass('off');
                $(this).addClass('on');
                $(this).attr('rel', 'donfireevents');
            }
            else {
                toggle = 1;
                $(this).removeClass('on');
                $(this).addClass('off');
                $(this).attr('rel', 'donfireevents');
            }

            $.getJSON($(this).attr('href') + '/json-1/toggle-' + toggle, function (data) {
                if (data.status != 1) {
                    alert(data.message);
                }
            });

            assignEventHandlersForToggle();
        });

        /*
         * Datagrid filter autosubmit
         */
        var datagrid_filter_form = $('.filter_form.autosubmit');
        if (datagrid_filter_form.length > 0) {
            datagrid_filter_form.change(function (event) {
                $(this).submit();
            });

        }
    },

    /* Displaying confirmation box */
    makeLinksConfirmable: function (handler) {
        $(handler).click(function (event) {
            var r = confirm('Are you sure?');
            if (r == true) {
                return;
            }

            event.stopPropagation();
            event.preventDefault();
        });

    },

    /* attach splash for heavy operations */
    makeHeavyOperationsWork: function (handler) {
        $(handler).click(function (event) {
            $('#heavy_operation_indicator').fadeIn(1000);
        });
    },

    /* Hiding success message with a delay */
    hideSuccessAfterAWhile: function (handler) {
        var interval = setInterval(hideMessages, 2000);

        function hideMessages() {
            $(handler).animate({
                opacity: 0
            }, 2400).slideUp(300);
            clearInterval(interval);
        }
    },
    /* Attaches fancybox to a handler */
    doFancyBox: function (handler) {
        $(handler).colorbox({
            scalePhotos: true,
            overlayClose: true,
            maxWidth: '90%',
            maxHeight: '90%',
            opacity: .6,
            speed: 250
        });
    },

    /* fixes or adds elements to layout using additional markup  */
    fixLayout: function () {
        ppLib.addSeparators('#optional_actions_bar', 'span,a');
        ppLib.addSeparators('footer p.rFloated', 'span,a');
        //ppLib.addSeparators('.action_bar','a');
        ppLib.addBeak('#secondary_navigation', 'li.active, li.active a');
        ppLib.addBeak('.breadcrumbs', 'a');
        ppLib.addSeparators('.separable', 'a');

        var borderStroke = document.createElement('span');
        $(borderStroke).addClass('break_stroke');
        $('.breadcrumbs a').append(borderStroke);

        /* fix primary_navigation */
        function fixPriNav(el) {
            $('li:nth-child(8)', el).after('<li class="clear" />');

        }
    },

    /* adds separators to list of elements '|' */
    addSeparators: function (container, el) {
        $(container).each(function () {
            var sep = document.createElement('span');
            $(sep).text(' | ');
            $(sep).addClass('sep');
            $(el, this).not(':last').after(sep);
        });
    },

    /* adds beaks to active elements */
    addBeak: function (container, el) {
        var beak = document.createElement('span');
        $(beak).text('{beak}');
        $(beak).addClass('beak');
        $(el, container).append(beak);

        if (container == '.breadcrumbs') {
            var borderStroke = document.createElement('span');
            $(borderStroke).addClass('break_stroke');
            $(el, container).append(borderStroke);
        }

    },

    /* fonts' sizes handler */
    resizeText: function (trigger) {

        $('#font_resize_bar a').removeClass('active');
        var fSizeCookie = readCookie('fSize');

        switch (fSizeCookie) {
            case 'small':
                $('body').addClass('hasSmallFonts');
                $('#font_resize_bar li:first-child a').addClass('active');
                break;

            case 'normal':
                $('body').removeClass('hasSmallFonts');
                $('body').removeClass('hasBigFonts');
                $('#font_resize_bar li:nth-child(2) a').addClass('active');
                break;

            case 'big':
                $('body').addClass('hasBigFonts');
                $('header .box_content > div').css('margin-top', '0.75%');
                $('#font_resize_bar li:last-child a').addClass('active');
                break;

            default:
                $('body').removeClass('hasSmallFonts');
                $('body').removeClass('hasBigFonts');
                $('#font_resize_bar li:nth-child(2) a').addClass('active');
                break;
        }

        $(trigger).click(function (e) {
            e.preventDefault();


            $('body').removeClass();
            $('header .box_content > div').css('margin-top', '0.9%');
            $(trigger).removeClass();
            $(this).addClass('active');

            var fSize = $(this).attr('data-fontsize');
            switch (fSize) {
                case 'small':
                    $('body').addClass('hasSmallFonts');
                    break;

                case 'normal':
                    $('body').removeClass();
                    break;

                case 'big':
                    $('body').addClass('hasBigFonts');
                    $('header .box_content > div').css('margin-top', '0.75%');
                    break;
            }
            createCookie('fSize', fSize, 0);
        });
    },

    /* print iframe's src */
    doPrint: function (trigger, frameId) {
        $(frameId).ready(function () {
            $(trigger).click(function (e) {
                e.preventDefault();
                frameName = frameId.replace('#', '');
                window.frames[frameName].focus();
                window.frames[frameName].print();
            });
        });
    },

    /* nav.fileTree handler */
    navFilesTree: function (el, trigger) {

        /* if item has .active, leave it open */
        $('li', el).each(function () {
            if ($('ul a', this).hasClass('active')) {
                $(this).addClass('open');
            }

        });
        $('li', el).each(function () {
            if ($(this).hasClass('open')) {
                $(trigger, this).attr('title', 'Fold items');
            } else {
                $('ul', this).hide();
            }
        });

        /* open/close categories */
        $(trigger, el).click(function (e) {
            e.preventDefault();
            if ($(this).parent('li').hasClass('open')) {
                $(this).next('ul').slideUp(100);
                $(this).parent('li').removeClass('open');
            }
            else {
                $(this).next('ul').slideDown(200);
                $(this).parent('li').addClass('open');
            }

        });

        /* if item has no cildren, display message */
        $('ul.nonempty', el).each(function () {
            if ($(this).children().length < 1) {
                $(this).html('<li><em>No items in this category.</em></li>');
            }
        });
    },

    /* hint bubbles handler */
    helpBubbles: function (trigger, bubbleContentSrc, bubbleColor) {
        // declarations
        var hintColorClass;

        if( !bubbleContentSrc )
        {
            bubbleContentSrc = 'title';
        }

        // check if bubbleColor given
        if (bubbleColor) {
            switch (bubbleColor) {
                case 'white':
                    hintColorClass = 'qtip-light';
                    break;
                case 'red':
                    hintColorClass = 'qtip-red';
                    break;
                case 'green':
                    hintColorClass = 'qtip-green';
                    break;
                case 'blue':
                    hintColorClass = 'qtip-blue';
                    break;
                default:
                    hintColorClass = 'qtip-plain';
                    break;
            }
        }
        // run qtip on trigger with data-hint content
        $(trigger).qtip({
            content: {
                attr: bubbleContentSrc
            },
            position: {
                viewport: $(window),
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-shadow qtip-rounded ' + hintColorClass
            }
        });
    },

    /* form-data copy handler */
    copyDataBetweenFields: function (trigger, formGroup_1, formGroup_2, fPrefix) {
        $(trigger).click(function (e) {
            e.preventDefault();
            $('input[type=text]', formGroup_1).each(function () {
                var fVal = $(this).val();
                var fAttr = $(this).attr('name');
                $('input[name=' + fPrefix + fAttr + ']', formGroup_2).val(fVal);
            });
        });

    },

    /* apply .even to the matched group of elements (TR's, Li's and so on) */
    applyEvens: function (container, el) {
        $(el + ':odd', container).addClass('even');
    },

    /* display session expiration time */
    sessionCounter: function (sessionTimeoutUTC, serverOffsetSec) {

        var today = new Date();
        var todaySecsUTC = Math.floor(today.getTime() / 1000 + (today.getTimezoneOffset() * 60));

        var secsLeft = (sessionTimeoutUTC - todaySecsUTC) + serverOffsetSec;
        var minLeft = Math.floor(secsLeft / 60);

        if (secsLeft < 0) {
            // TODO Wylogowanie LUB wyświetlenie ajaxowego formularza logowania (bez przeładowania)
            $('#session_counter p').html('Your session is over. You have to relogin. <a href="' + adminUrl + '" class="refresh">Refresh</a>.');
        }
        else {
            var left = minLeft + " minutes";
            if (minLeft < 1) {
                left = secsLeft + " seconds";
            }

            if (minLeft < 10) {
                $('#session_counter').fadeIn();
            }

            $('#session_counter span.left').text(left);
        }

        setTimeout("ppLib.sessionCounter(sessionTimeoutUTC, serverOffsetSec)", 1000);
    },
    sessionCounterRefresh: function () {

        $('#session_counter .refresh').click(function (e) {
            e.preventDefault();
            $.getJSON('admin/login/json_session_refresh', function (data) {
                if (data.success > 0) {
                    //alert("Klucz 'success' == " + val + '. Hiding...')
                    $('#session_counter').fadeOut(300);
                    sessionTimeoutUTC += 30000;
                } else {
                    //alert("Key 'success' == " + val + '. Please, log in again.');
                }
            });


        });
    }
};


/* Cookies */
function createCookie(name, value, expDate) {
    /* expDate wyrazony w minutach */
    if (expDate) {
        var date = new Date();
        date.setTime(date.getTime() + (expDate * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }

    else
        expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');

    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
        }
    }
    return null;
}
function eraseCookie(name) {
    createCookie(name, "", -1);
}