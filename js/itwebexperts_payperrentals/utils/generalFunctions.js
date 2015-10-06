(function ($) {
        $.fn.nextTo = function (baseElement, options) {
            if (this.length == 0) return false;

            var settings = $.extend({
                position: 'bottom', // bottom/top/right/left
                shareBorder: 'right', // bottom/top/right/left
                offsetX: 0,
                offsetY: 0
            }, options || {});

            var looseElement = this;
            var baseOffset = baseElement.offset();
            var looseOffset = looseElement.offset();

            looseElement.css('position', 'absolute');

            // general position
            if (settings.position == 'bottom')
                looseElement.css('top', (baseOffset.top + baseElement.outerHeight()) + 'px');
            else if (settings.position == 'top')
                looseElement.css('top', (baseOffset.top - looseElement.outerHeight()) + 'px');
            else if (settings.position == 'right')
                looseElement.css('left', (baseOffset.left + baseElement.outerWidth()) + 'px');
            else if (settings.position == 'left')
                looseElement.css('left', (baseOffset.left - looseElement.outerWidth()) + 'px');

            // align border
            if (settings.shareBorder == 'right')
                looseElement.css('left', baseOffset.left - (looseElement.outerWidth() - baseElement.outerWidth()) + 'px');
            else if (settings.shareBorder == 'left')
                looseElement.css('left', baseOffset.left + 'px');
            else if (settings.shareBorder == 'top')
                looseElement.css('top', baseOffset.top + 'px');
            else if (settings.shareBorder == 'bottom')
                looseElement.css('top', baseOffset.top - (looseElement.outerHeight() - baseElement.outerHeight()) + 'px');

            // add offset
            looseElement.css('left', (parseInt(looseElement.css('left').replace('px', '')) + settings.offsetX) + 'px');
            looseElement.css('top', (parseInt(looseElement.css('top').replace('px', '')) + settings.offsetY) + 'px');
        };
    })(jQuery);

function getSerialNumbers(classValue, serviceUrl, selectCompleted){

        $jppr(classValue).each(function () {
            var params = {
                productId: $jppr(this).attr('prid'),
                startDate: $jppr(this).attr('start_date'),
                endDate: $jppr(this).attr('end_date')
            }

            $jppr(this).autocomplete({
                serviceUrl: serviceUrl,
                type: 'post',
                minChars: 0,
                paramName: 'value',
                params: params,
                onSelect: function (suggestion) {
                    if (typeof selectCompleted === 'function') {
                        selectCompleted($jppr(this).val());
                    }
                }
            });
        });
    }

