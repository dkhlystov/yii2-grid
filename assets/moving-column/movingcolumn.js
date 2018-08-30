$(document).on('mousedown', 'td.moving-column > span.moving-handle', function(e) {
    if (e.which == 1) {
        moveStart(e, $(this).closest('tr'));
    };
});

var $handler, $overlay;

function moveStart(e, $tr) {
    //make handler
    var $table = $tr.closest('table'), o = $tr.offset(), topMin = $table.offset().top, topMax = topMin + $table.height() - $tr.outerHeight();
    $handler = $('<div></div>')
        .css({'background-color': '#fff', 'position': 'absolute', 'top': o.top, 'left': o.left, 'width': $tr.outerWidth(), 'height': $tr.outerHeight(), 'overflow': 'hidden'})
        .append($tr.closest('table').clone().css({'margin-top': -$tr.position().top}))
        .data({
            'target': $tr,
            'index': $tr.index(),
            'top': o.top,
            'topMin': topMin,
            'topMax': topMax,
            'pageY': e.pageY
        })
        .appendTo('body');

    //overlay
    $overlay = $('<div></div>').css({'position': 'absolute', 'top': 0, 'left': 0, 'width': '100%', 'height': '100%'}).appendTo('body');

    //hide row
    $tr.css({'opacity': 0});

    //attach move handlers
    $(document).on('mouseup', moveEnd);
    $(document).on('mousemove', moveMove);
};

function moveMove(e) {
    var top = $handler.data('top') + e.pageY - $handler.data('pageY'), topMin = $handler.data('topMin'), topMax = $handler.data('topMax');
    if (top < topMin) {
        top = topMin;
    };
    if (top > topMax) {
        top = topMax;
    };
    $handler.css('top', top);

    //target row moving
    top = top + $handler.outerHeight() / 2;
    var $tr = $handler.data('target');
    $tr.parent().find('tr').not($tr).each(function() {
        var $this = $(this), t = $this.offset().top;

        if (typeof $this.find('td.moving-column').data('movingDisabled') == 'undefined') {
            if (top > t && top < t + $this.outerHeight() - 1) {
                if ($tr.offset().top < t) {
                    $tr.insertAfter($this);
                } else {
                    $tr.insertBefore($this);
                };
                return false;
            };
        };
    });
};

function moveEnd() {
    $(document).off('mouseup', moveEnd);
    $(document).off('mousemove', moveMove);

    var $tr = $handler.data('target'), oldIndex = $handler.data('index');

    $tr.css({'opacity': 1});
    $handler.remove();
    $overlay.remove();

    if ($tr.index() != oldIndex) {
        $tr.trigger('moving-column.moved');
    };
};
