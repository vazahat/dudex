/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.static
 * @since 1.0
 */

MCOMPOSE = {};

MCOMPOSE.Observer = function( context )
{
    this.events = {};
    this.context = context;
};

MCOMPOSE.Observer.PROTO = function()
{
    this.bind = function(eventName, callback, context)
    {
        context = context || false;

        if ( !this.events[eventName] )
        {
            this.events[eventName] = [];
        }

        this.events[eventName].push({
            callback: callback,
            context: context
        });
    };

    this.trigger = function( eventName, eventObj )
    {
        var self = this;

        eventObj = eventObj || {};

        if ( !this.events[eventName] )
        {
            return false;
        }

        $.each(this.events[eventName], function(i, o)
        {
            o.callback.call(o.context || self.context, eventObj);
        });
    };

    this.unbind = function( eventName )
    {
        this.events[eventName] = [];
    };
}

MCOMPOSE.Observer.prototype = new MCOMPOSE.Observer.PROTO();

MCOMPOSE.State = function( data )
{
    data = data || {};
    this.state = data;

    this.observer = new MCOMPOSE.Observer(this);
}

MCOMPOSE.State.PROTO = function()
{
    this.mergeState = function( state )
    {
        $.extend(this.state, state);

        this.observer.trigger('change');
    };

    this.setState = function( state )
    {
        state = state || {};
        this.state = state;

        this.observer.trigger('change');
    };

    this.getState = function()
    {
        return this.state;
    };
};

MCOMPOSE.State.prototype = new MCOMPOSE.State.PROTO();

MCOMPOSE.UserState = function( data )
{
    data = data || {};
    this.state = data;

    this.observer = new MCOMPOSE.Observer(this);

    this.searchedKWs = [];

    this.addKeyword = function( kw )
    {
        this.searchedKWs.push(kw);
    };

    this.isSearched = function( kw )
    {
        for ( var i = 0; i < this.searchedKWs.length; i++ )
        {
            if ( kw.search(this.searchedKWs[i]) === 0 )
            {
                return true;
            }
        }

        return false;
    };

    this.find = function( filter )
    {
        var out = [], cache, self = this;
        cache = this.getState();

        $.each(cache, function(id, item)
        {
            var found = $.isFunction(filter)
                ? filter.call(self, item, id)
                : true

            if ( found ) {
                out.push(item);
            }
        });

        return out;
    };
};

MCOMPOSE.UserState.prototype = new MCOMPOSE.State.PROTO();



MCOMPOSE.userSelector = (function() {

    var _cache = new MCOMPOSE.UserState();
    var ajaxTimeout, syncing = false;

    var node, select2;
    var _settings = {};

    var formatResult, formatSelection, getData, syncData, getDataFromCache, highlightTerm, getGroupSettings;

    getGroupSettings = function( group ) {
        return _settings.groups[group] || _settings.groupDefaults;
    };

    highlightTerm = function( term, text ) {
        var match = text.toUpperCase().indexOf(term.toUpperCase()),
            tl=term.length,
            markup = [];

        if ( match < 0 ) {
            markup.push(text);
        } else {
            markup.push(text.substring(0, match));
            markup.push("<span class='select2-match'>");
            markup.push(text.substring(match, match + tl));
            markup.push("</span>");
            markup.push(text.substring(match + tl, text.length));
        }

        return markup.join("");
    };

    formatResult = function( data, container, query ) {
        if ( data.type == "msg" )
            return '<div class="mc-message ow_small">' + data.text + '</div>';

        if ( !data.id )
            return '<div class="mc-group ow_small ow_remark">' + data.text + '</div>';
        
        var html = $(data.html);
        html.find(".mc-ddi-text").html("<span>" + highlightTerm(query.term, data.text) + "</span>");
        
        return html;
    };

    formatSelection = function( data, container ) {
        var text = data.text, countHtml = '';

        container.addClass('ow_border mc-tag-bg');
        container.find('.select2-search-choice-close').addClass('ow_border');

        if ( data.count !== null && data.count !== false ) {
            countHtml = '<span class="ow_txt_value mc-selection-count">' + data.count + '</span>';
        }
        
        if ( (data.text.length - 3) > 30 ) {
            text = data.text.substring(0, 30) + '...';
        }

        return '<a class="mc-tag-label" target="_blank" href="' + data.url + '">' + text + ' ' + countHtml + '</a>';
    };

    syncData = function( term, callback ) {
        syncing = true;
        $.getJSON(_settings.rspUrl, {term: term, context: _settings.context}, function( data ) {
            syncing = false;
            if ( $.isFunction(callback) ) callback(data);
            _cache.mergeState(data);
        });
    };

    getDataFromCache = function( term, count ) {
        count = count || 5;

        var out = [], groups = {}, orderedGroups = [], state = _cache.find(function( item ) {
            var temp = item.text.toUpperCase().indexOf(term.toUpperCase());
            if ( temp < 0 ) {
                return false;
            }

            temp -= 1;

            return temp < 0 || item.text[temp] == ' ';
        });
        
        state.reverse();
        
        var groupsCount = 0, lastGroup, val = select2.val();

        $.each(state, function(id, item) {
            if ( $.inArray(item.id.toString(), val) >= 0 || count == 0 ) {
                return;
            }
            count--;

            if ( item.group ) {
                if ( !groups[item.group] ) {
                    groups[item.group] = {
                        text: item.group,
                        children: []
                    };
                    groupsCount++;
                    lastGroup = item.group;
                }

                groups[item.group].children.push(item);
            } else {
                out.push(item);
            }
        });

        if (out.length > 0 || groupsCount > 0) {
            _settings.groups = _settings.groups || {};

            $.each(_settings.groups, function(groupName, groupSettings) {
               if ( !groups[groupName] && groupSettings.alwaysVisible && groupSettings.noMatchMessage ) {
                    groups[groupName] = {
                        text: groupName,
                        children: [{
                            text: OW.getLanguageText(groupSettings.noMatchMessage.prefix, groupSettings.noMatchMessage.key, {
                                "term": term
                            }),
                            type: 'msg'
                        }]
                    };

                    groupsCount++;
                }
            });
        }
        
        $.each(groups, function( i, group ) {
            orderedGroups.push(group);
        });
        
        orderedGroups.sort(function(a, b) {
            var sA = getGroupSettings(a.text);
            var sB = getGroupSettings(b.text);
            
            return sA.priority - sB.priority;
        });
        
        if ( lastGroup ) {
            var temp = groupsCount == 1 && !getGroupSettings(lastGroup).alwaysVisible ? groups[lastGroup].children : orderedGroups;
            
            $.each(temp, function(id, group) {
                out.unshift(group);
            });
        }

        return out;
    };

    getData = function( options ) {
        var state = getDataFromCache(options.term);

        var sync = $.trim(options.term) && !_cache.isSearched(options.term);

        if ( sync ) {

            if ( ajaxTimeout ) {
                window.clearTimeout(ajaxTimeout);
            }

            ajaxTimeout = window.setTimeout(function() {
                _cache.addKeyword(options.term);
                syncData(options.term);
            }, 300);
        }

        if ( (!sync || state.length) && !syncing ) {
            options.callback({
                results: state
            });
        }
    };

    return {
       init: function( selector, settings, options, data ) {

            if ( $.isPlainObject(data) ) {
                _cache.setState(data);
            }

            _settings = settings;

            node = $(selector);

            node.select2($.extend(options, {
                "query": getData,
                "formatResult": formatResult,
                "formatSelection": formatSelection,
                "formatNoMatches": function( term ) {
                    return OW.getLanguageText('mcompose', 'selector_no_matches', {
                        "term": term
                    });
                },
                "formatSearching": function() {
                    return OW.getLanguageText('mcompose', 'selector_searching');
                },
                "formatSelectionTooBig": function( limit ) {
                    return OW.getLanguageText('mcompose', 'selector_too_many', {
                        "limit": limit
                    });
                }
            }));

            node.next(".us-field-fake").hide();

            select2 = node.data().select2;

            node.get(0).focus = function() {
                select2.focusSearch();
            };

            _cache.observer.bind('change', function()
            {
                select2.updateResults();
            });

            return select2;
       }
    }
})();

MCOMPOSE.UserSelectorFormElement = function( id, name ) {
    var formElement = new OwFormElement( id, name );

    formElement.init = function( selector, settings, options, data ) {
        formElement.select2 = MCOMPOSE.userSelector.init(selector, settings, options, data);
    };

    formElement.resetValue = function() {
        formElement.select2.data([]);
    };

    formElement.getValue = function() {
        return formElement.select2.val();
    };

    formElement.setValue = function( val ) {
        formElement.select2.data(val);
    };

    return formElement;
};

MCOMPOSE.sendMessage = function(params, scope)
{
    var form = owForms[params.formName];
    
    var floatBoxOnClose = function() {
        
        var changed = 0;
        
        changed += $.trim(form.elements.message.getValue()).length > 0 ? 1 : 0;
        changed += $.trim(form.elements.subject.getValue()).length > 0 ? 1 : 0;
        
        if ( changed > 0 ) return confirm(OW.getLanguageText("mcompose", "close_fb_confirmation"));
        
        return true;
    };
    
    var setupFloatBox = function() {
        var fb = scope.floatBox;
        
        fb.bind("close", floatBoxOnClose);
    };
    
    if ( scope.floatBox ) {
        setupFloatBox();
    }
};