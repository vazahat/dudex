/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
(function( $ )
{
    if ( !PROFILEPROGRESSBARPARAMS.totalQuestionCount )
    {
        return;
    }
        
    var _elements = {}, _methods = {};
    
    _elements.totalQuestionCount = +PROFILEPROGRESSBARPARAMS.totalQuestionCount;
    _elements.completeQuestionCount = +PROFILEPROGRESSBARPARAMS.completeQuestionCount;
    _elements.profileProgressbar = document.getElementById('profile-progressbar');
    _elements.editForm = document.getElementById('editForm');
    _elements.caption = $('.profile-progressbar-caption', _elements.profileProgressbar);
    _elements.complete = $('.profile-progressbar-complete', _elements.profileProgressbar);

    _methods.getCompletePercent = function()
    {
        var completed;
        
        return (completed = Math.round((this.completeQuestionCount * 100) / this.totalQuestionCount)) > 100 ? 100 : completed;
    }.bind(_elements);
        
    (_methods.showCompletePercent = function()
    {
        var complete = _methods.getCompletePercent();

        this.caption.text(complete + '%');
        this.complete.animate({width: complete + '%'}, 
        {
            duration: 'slow',
            specialEasing: {width: 'easeOutBounce'},
            queue: false
        });
    }.bind(_elements))();
    
    _methods.onCompleteQuestion = function()
    {
        switch ( this.type.toLowerCase() )
        {
            case 'text':
            case 'textarea':
                _methods.complete.call(this, this.value.trim().length !== 0 ? 'inc' : 'dec');
            break;
            
            case 'checkbox':
            case 'radio':
                _methods.complete.call(this, $(this).closest('td').find('input:checked').length !== 0 ? 'inc' : 'dec');
            break;
            
            case 'select-one':
                var elements = $(this).closest('td').find('select');

                if ( elements.length === 3 )
                {
                    var day = elements.filter('[name^="day_"]').val();
                    var month = elements.filter('[name ^= "month_"]').val();
                    var year  = elements.filter('[name ^= "year_"]').val();
                    var data = Date.parse(year + '-' + ((month.length === 1) ? '0' + month : month) + '-' + ((day.length === 1) ? '0' + day : day) + 'T00:00:00.000');

                    _methods.complete.call(this, !isNaN(data) ? 'inc' : 'dec');
                }
                else
                {
                    _methods.complete.call(this, this.value.trim().length !== 0 ? 'inc' : 'dec');
                }
            break;
        }
    };
        
    _methods.complete = function( action )
    {
        action = action === 'inc' ? action : 'dec';
        
        var node = $(this).closest('td');
        var completed = node.data('complete');
            
        if ( !completed )
        {
            node.data('complete', action);
        }
        else if ( completed !== action )
        {
            action === 'inc' ? _elements.completeQuestionCount++ : _elements.completeQuestionCount--;
            _methods.showCompletePercent();
            node.data('complete', action);
        }
    };
        
    $('input:visible,select:visible,textarea:visible', _elements.editForm).each(function()
    {
        switch ( this.type.toLowerCase() )
        {
            case 'text':
            case 'textarea':
                $(this).on('keyup', _methods.onCompleteQuestion);
                break;
            case 'radio':
            case 'checkbox':
            case 'select-one':
                $(this).on('change', _methods.onCompleteQuestion);
                break;
        }
    }).on('error.complete', _methods.onCompleteQuestion).trigger('error.complete');
        
})(jQuery);
