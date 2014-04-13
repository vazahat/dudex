/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
(function( $ )
{
    var _elements = {};
    _elements.slider = $('#slider');
    _elements.progressbar = $('#profile_progressbar');
    _elements.caption = $('.profile-progressbar-caption', _elements.progressbar);
    _elements.complete = $('.profile-progressbar-complete', _elements.progressbar);

    _elements.slider.slider(
    {
        range: "min",
        min: 0,
        max: 100,
        value: 60,
        slide: function( event, ui )
        {
            _elements.caption.text(ui.value + '%');
            _elements.complete.css('width', ui.value + '%');
        }
    });
})(jQuery);
