/*	jQuery HiddenPosition Plugin - easily position any DOM element, even if it's hidden
 *  Examples and documentation at: http://www.garralab.com/hiddenposition.php
 *  Copyright (C) 2012  garralab@gmail.com
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
(function($) {
    $.fn.positionOn2 = function(element, align) {
        return this.each(function() {
            var target   = $(this);
            var position1 = element.offset();
            var position = element.position();
            var offset = element.offset();
            var posY = offset.top - $(window).scrollTop();
            var posX = offset.left - $(window).scrollLeft();
           // var x      = parseFloat(element.css('left'));;
          //  var y      = parseFloat(element.css('top'));;

            //if(align == 'right') {
            //posX -= (/*target.outerWidth() -*/ element.outerWidth()/2);
            //} else if(align == 'center') {
              //  x -= target.outerWidth() / 2 - element.outerWidth() / 2;
            //}

            target.css({
                position: 'absolute',
                zIndex:   50000000,
                top:      posY,
                left:     posX
            });
        });
    };

    $.fn.positionOn = function(element, align) {
        return this.each(function() {
            var target   = $(this);
            var sTop = $(window).scrollTop();
            var sLeft = $(window).scrollLeft();
            var w = element.width();
            var h = element.height();
            var offset = element.offset();
            var $p = target;
            var pOffset = {
                left: 0,
                top:  0
            };
            while(typeof $p == 'object') {

                if ($p.css('position') == 'relative' ) {
                    pOffset = $p.offset();
                    break;
                }
                $p = $p.parent();

                if(typeof $p.offset() == 'undefined') break;
            }
                offset.left = offset.left - (pOffset.left);
                offset.top = offset.top - (pOffset.top);



            var pos = {
                left: offset.left - sLeft,
                right: offset.left + w - sLeft,
                top:  offset.top - sTop,
                bottom: offset.top + h - sTop
            };

            pos.tl = { x: pos.left, y: pos.top };
            pos.tr = { x: pos.right, y: pos.top };
            pos.bl = { x: pos.left, y: pos.bottom };
            pos.br = { x: pos.right, y: pos.bottom };

            target.css({
                position: 'absolute',
                zIndex:   5000000,
                top:      pos.top,
                left:      pos.left
            });
        });
    };
})(jQuery);