(function ($) {
    $.fn.yiiClock = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.yiiGridView');
            return false;
        }
    };

    var defaults = {
        refreshUrl: undefined,
        hashKey: undefined
    };

    var methods = {
        init: function (options) {
            return this.each(function () {
                var $e = $(this);
                var settings = $.extend({}, defaults, options || {});
                $e.data('yiiClock', {
                    settings: settings
                });

                var id = settings.id;

                $("#" + id)
                    .mouseover(function(){
                        var dateBlock = $("#date");
                        var clockBlock = $("#clock");
                        if(dateBlock.is(":visible"))
                            return;
                        clockBlock.hide();
                        dateBlock.show();
                    })
                    .mouseout(function(){
                        var dateBlock = $("#date");
                        var clockBlock = $("#clock");
                        if(clockBlock.is(":visible"))
                            return;
                        dateBlock.hide();
                        clockBlock.show();
                    });

                $('#' + id + '_wrap ul.dropdown-menu li a').click(function (e) {
                    e.preventDefault();
                    var timeZone = $(this).data("offset");
                    settings.offset = timeZone;
                    methods.refresh.apply($e);
                    return false;
                });


                $("#" + id).ClockGMT({offset:settings.default_timezone});

            });
        },

        refresh: function () {
            var $e = this,
                settings = this.data('yiiClock').settings;
            var id = settings.id;
            var tzone = settings.offset;
            $("#" + id).ClockGMT('reset',{offset:tzone});
        },

        destroy: function () {
            return this.each(function () {
                $(window).unbind('.yiiClock');
                $(this).removeData('yiiClock');
            });
        },

        data: function () {
            return this.data('yiiClock');
        },
    };

})(window.jQuery);