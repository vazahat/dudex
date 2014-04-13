
var OA_TagsField = function( id, name, params ){
    var $contex = $('#'+id);
    var spaceReplace = function( tag ){
        return tag.replace(new RegExp(" ",'g'), '_');
    }
    var getLastTag = function(){
        $el = $('span.values input:last-child', $contex);

        if( $el.length != 0 ){
            return $el.val();
        }

        return false;
    };
    var removeAllTags = function(){
        $('span.values').empty();
        $('ul li.tag').remove();
    }
    var removeTag = function( tag ){
        var tagstring = spaceReplace(tag);
        $('span.values input.tag-' + tagstring, $contex).remove();
        $('ul li.tag-' + tagstring, $contex).remove();
    };
    var addTag = function( tag ){
        var tagstring = spaceReplace(tag);
        if( !tag || tag.length < 3 ){
            OW.warning(params.labels.too_short);
            return false;
        }else if( $('span.values input.tag-' + tagstring, $contex).length != 0 ){
            OW.error(params.labels.duplicate);
            return false;
        }else{
            $('span.values', $contex).append($('<input type="hidden" name="' + name + '[]" value="' + tag + '" class="tag-' + tagstring + '" />'));
            $('ul li.new_tag', $contex).before($('<li class="tag tag-' + tagstring + '"><span>' + tag + '</span><a href="javascript://">x</a></li>'));
            $('li.tag-' + tagstring + ' a', $contex).bind('click', {tag:tag}, function(e){removeTag(e.data.tag);});
            return true;
        }
    };

    $('li.new_tag input', $contex).focus(
        function(){
            $('ul', $contex).addClass('focused');
        }
    ).blur(
        function(){
            $('ul', $contex).removeClass('focused');
        }
    );

    $('ul', $contex).click( function(){
        $('li.new_tag input', $contex).focus();
    } );

    $('li.new_tag input', $contex).keydown(
        function(e){
            var textVal = $.trim($(this).val()).toLowerCase();
            var keyCode = e.which;

            if( keyCode == 13 ){
                if( addTag(textVal) ){
                    $(this).val('').css({width:18}).focus();
                }

                if (e.stopPropagation){
                    e.stopPropagation();
                }

                if (e.preventDefault){
                    e.preventDefault();
                }

            }else if( keyCode == 8 ){
                if( textVal == '' ){
                    var lastTag = getLastTag();
                    if( lastTag != false ){
                        removeTag(lastTag);
                    }
                }
            }else{
                $(this).css({width:($(this).val().length * 7 + 18)});
            }
        }
        );

    for( var i = 0; i < params.initItems.length; i++ ){
        $('li.tag-' + spaceReplace(params.initItems[i]) + ' a', $contex).bind('click', {tag:spaceReplace(params.initItems[i])}, function(e){removeTag(e.data.tag);});
    }

    var formElement = new OwFormElement(id, name);

    formElement.getValue = function(){
        var $inputs = $("span.values input", $contex);
        var values = [];
        $.each( $inputs, function(index, data){
                values.push($(this).val());
            }
        );
        return values;
    };

    formElement.setValue = function(val){
        for( var i = 0; i < val.length; i++ ){
            addTag(val[i]);
        }
    }

    formElement.resetValue = function(){
        removeAllTags();
    }

    return formElement;
}

