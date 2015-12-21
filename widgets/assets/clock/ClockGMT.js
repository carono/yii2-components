/**
 * Created by alex on 15.12.15.
 */
(function($) {

    $.fn.ClockGMT = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist');
            return false;
        }
    };

    var defaults = {
        default: "+3",
        offset: undefined, // default gmt offset
        hour24: true,
    };

    var clockData = {};

    var methods = {
        init:function(options) {
                var $e = $(this);
                var settings = $.extend({}, defaults, options || {});

                clockData[$e.attr('id')] = {settings: settings};
                if(settings.offset !== undefined) {
                    try {
                        if(localStorage.getItem('timezone_offset'))
                            localStorage.removeItem('timezone_offset');
                        localStorage.setItem('timezone_offset', settings.offset)
                    }catch(e){
                        console.log(e);
                    }
                }
                var offset = localStorage.getItem('timezone_offset') || settings.default;
                var id = $(this).attr('id');

                Date.prototype.stdTimezoneOffset = function() {
                    var jan = new Date(this.getFullYear(), 0, 1);
                    var janUTC = jan.getTime() + (jan.getTimezoneOffset() * 60000);
                    var janOffset = new Date(janUTC + (3600000 * offset));
                    var jul = new Date(this.getFullYear(), 6, 1);
                    var julUTC = jul.getTime() + (jul.getTimezoneOffset() * 60000);
                    var julOffset = new Date(julUTC + (3600000 * offset));
                    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
                };
                Date.prototype.dst = function() {
                    if( parseFloat(offset) <= -4 && parseFloat(offset) >= -10 ) {
                        var dCheck = new Date;
                        var utcCheck = dCheck.getTime() + (dCheck.getTimezoneOffset() * 60000);
                        var newCheck = new Date(utcCheck + (3600000 * offset));
                        return this.getTimezoneOffset() < this.stdTimezoneOffset();
                    }
                };
                // create new date object
                var dateCheck = new Date;

                if( dateCheck.dst() ) {
                    offset = parseFloat(offset) + 1;
                }
                clockData[$e.attr('id')].settings.offset = offset;
                methods.start.apply(this);
        },

        start: function() {
            var $e = $(this),
                intervalId;
            var data = clockData[$e.attr('id')];

            intervalId = setInterval(function() {
                var d = new Date,
                    utc = d.getTime() + (d.getTimezoneOffset() * 60000),
                    nd = new Date(utc + (3600000 * data.settings.offset));
                var s = nd.getSeconds();
                var m = nd.getMinutes();
                var hh = nd.getHours();

                var timeSting = hh + ':' + ((m < 10) ? "0" + m : m) + ((s < 10) ? ":0" + s : ":" + s);
                $('#clock').html(timeSting);
            }, 1000);

            // save start time
            clockData[$e.attr('id')].settings.ttStartTime = $.now();
            clockData[$e.attr('id')].settings.intervalId = intervalId;
        },

        stop: function() {
            var $e = $(this);
            var data = clockData[$e.attr('id')];

            if(data.settings.intervalId) {
                clearInterval(data.settings.intervalId);
                clockData[$e.attr('id')].settings.intervalId = null;
            }
            return data;
        },

        reset: function(options) {
            var data = methods.stop.call(this);
            methods.init.call(this, options || {});
        },

        data:function() {
            var id = $(this).attr('id');
            return clockData[id];
        }
    }

})(window.jQuery);