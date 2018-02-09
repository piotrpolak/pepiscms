$(document).ready(function() {
    ppLib_2.toggleOverlay('.overlay_window', 'body.plain a.popup');
    $('body.direct .cancel').click(function(e) {
        e.preventDefault();
        parent.$('.close_overlay').trigger('click');
    });
    // To close popup, print <span class="popup_close"></span> inside body
    if ($('body.popup .popup_close').length) {
        parent.$('.white_mask').show();

        // TODO Check HASH
        var p_location = parent.location;
        var i = parent.location.toString().indexOf('#');
        if (i > 0)
        {
            p_location = parent.location.toString().substr(0, i)
        }

        parent.location = p_location + '#' + $(parent).scrollTop(); // TODO sprawdzić czy to na pewno działa pod IE i innymi
        parent.location.reload(true);
    }


    if (window.location.hash.length > 1)
    {
        offset = window.location.hash.substr(1);
        if (offset > 0)
        {
            $(document).scrollTop(offset, function() {
                window.location.hash = '';
            });
        }
    }
});

$('body.plain').ready(function() {
    autoresize();
});
$(window).resize(function() {
    autoresize();
});
$('.overlay_window iframe').ready(function() {
    autoresize();
});


/* functions */
var ppLib_2 = {
    /* controls overlay_window */
    toggleOverlay: function(el, trigger) {
        var html = '<div class="overlay_mask" style="display: none;"></div><div class="overlay_window">';
        html += '<div class="box_content">';
        html += '<h4>';
        html += '<a href="#" class="trigger close_overlay">';
        html += '<img src="pepiscms/theme/img/popup/popup_close_9.png" alt="">';
        html += '</a>';
        html += '<span class="popup_title">{Window Title}</span>';
        html += '</h4>';
        html += '<div class="white_mask">';
        html += '<img src="pepiscms/theme/img/popup/loader_32.gif" alt="">';
        html += '</div>';
        html += '<iframe src=""></iframe>';
        html += '</div>';
        html += '</div>';

        $('html body').append(html);

        $('.overlay_mask').css('opacity', 0.8);
        $('.overlay_mask').hide();
        $(trigger).click(function(e) {
            e.preventDefault();

            var url = $(this).attr('href');
            if (!$(this).hasClass('noappend')) {
                url += '/layout-popup/direct-1';
            }

            var title = $(this).attr('title') ? $(this).attr('title') : '&nbsp;';
            $('.popup_title').html(title);

            $(el).each(function() {
                if (!$(this).is(':visible')) {
                    $('.white_mask').show();
                    $(this).show();
                    $('.overlay_mask').show();
                    $('iframe', this).attr('src', url);
                    $('iframe').load(function() {
                        $('.white_mask').hide();
                    });
                } else {
                    $('.white_mask').show();
                    $('iframe', this).attr('src', url);
                    $('iframe').load(function() {
                        $('.white_mask').hide();
                    });
                }
            });

        });
        $('.trigger.close_overlay').click(function(e) {
            e.preventDefault();
            $(el).each(function() {
                if ($(this).is(':visible')) {
                    $('.overlay_mask').hide();
                    $(this).hide();
                    $('iframe', this).attr('src', '');
                    $('.white_mask').hide();
                }
            });
        });
    },
    /* sets title and icon for overlay */
    applyOvTitle: function(el, ovTitle, ovIconUrl) {
        $(document).ready(function() {
            $('h4 span', el).text(ovTitle);
            var ovIcon = document.createElement('img');
            ovIcon.setAttribute('src', ovIconUrl);
            $(ovIcon).prependTo('h4', el);

        });
    }
};

function autoresize() {
    var x_margin = 80;
    var y_margin = 40;
    var max_width = 1500;
    //var min_height = 400;
    var width = $(window).width() - x_margin;
    var height = $(window).height() - y_margin;

    if (width > max_width) {
        width = max_width;
    }

    $('.overlay_window').width(width);
    $('.overlay_window iframe').width(width); // 18
    $('.overlay_window').height(height - 20);
    $('.overlay_window iframe').height(height - 52); // 30 - wysokość dolnej i górnej belki

    $('.overlay_window').css('marginLeft', -(width / 2));
    $('.overlay_window').css('top', y_margin / 2);
}