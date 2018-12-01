$(document).on('mousedown', 'td.moving-column > span.moving-handle', function(e) {
    if (e.which == 1) {
        moveStart(e, $(this).closest('tr'));
    };
});

var $handler, $overlay;

function moveStart(e, $tr) {
    var $table = $tr.closest('table');

    // Position
    var tp = $table.position(), to = $table.offset(), o = $tr.offset();
    var top = tp.top + o.top - to.top, left = tp.left + o.left - to.left;

    // Max and min
    var topMin = tp.top, topMax = topMin + $table.outerHeight() - $tr.outerHeight();

    // Handler
    $handler = $('<div></div>')
        // .css({'background-color': '#fff', 'position': 'absolute', 'top': o.top, 'left': o.left, 'width': $tr.outerWidth(), 'height': $tr.outerHeight(), 'overflow': 'hidden'})
        .css({'background-color': '#fff', 'position': 'absolute', 'top': top, 'left': left, 'width': $tr.outerWidth(), 'height': $tr.outerHeight(), 'overflow': 'hidden'})
        .append($tr.closest('table').clone().css({'margin-top': to.top - o.top}))
        .data({
            'target': $tr,
            'index': $tr.index(),
            'top': top,
            'topMin': topMin,
            'topMax': topMax,
            'pageY': e.pageY
        })
        .appendTo($table.parent());

    // Overlay
    $overlay = $('<div></div>').css({'position': 'absolute', 'top': 0, 'left': 0, 'width': '100%', 'height': '100%'}).appendTo('body');

    // Hide row
    $tr.css({'opacity': 0});

    // Attach move handlers
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

    // Row moving
    top = top + $handler.outerHeight() / 2;
    var $tr = $handler.data('target'), to = $tr.closest('table').offset();
    $tr.parent().find('tr').not($tr).each(function() {
        var $this = $(this), t = $this.offset().top - to.top;

        if (typeof $this.find('td.moving-column').data('movingDisabled') == 'undefined') {
            if (top > t && top < t + $this.outerHeight() - 1) {
                if ($tr.index() < $this.index()) {
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
