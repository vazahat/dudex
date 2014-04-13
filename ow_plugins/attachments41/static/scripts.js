// ------------------------------ < CORE > ------------------------------

ATTP = {};

ATTP.CORE = {};

ATTP.CORE.ObjectRegistry = {};

/**
 * View
 */
ATTP.CORE.View = function( node )
{
    if ( !node ) throw "View Constructor: Undefined Node";

    this.node = node;
};

ATTP.CORE.View.PROTO = function()
{
    this.$ = function( sel )
    {
        if ( !sel )
        {
            return $(this.node);
        }

        return $(sel, this.node);
    };

    this.renderContent = function(markup, container)
    {
        if ( markup.css )
        {
            OW.addCss(markup.css);
        }

        if ( markup.html )
        {
            $(container).html(markup.html);
        }

        if ( markup.js )
        {
            OW.addScript(markup.js);
        }
    };
};

ATTP.CORE.View.prototype = new ATTP.CORE.View.PROTO();

/**
 * Model
 */
ATTP.CORE.AjaxModel = function( rsp, delegate )
{
    this.rsp = rsp;
    this.delegate = delegate;

    this.delegate.ajaxEnd = this.delegate.ajaxEnd || function(){};
    this.delegate.ajaxSuccess = this.delegate.ajaxSuccess || function(){};
    this.delegate.ajaxStart = this.delegate.ajaxStart || function(){};
};

ATTP.CORE.AjaxModel.PROTO = function()
{
    this.query = function( command, params )
    {
        params = params || {};

        this.delegate.ajaxStart(command, params);

        $.ajax({
            type: 'POST',
            url: this.rsp,
            data: {
                "params": JSON.stringify(params),
                "command": command
            },
            context: this.delegate,
            success: function(r)
            {
                this.ajaxSuccess(command, r);
            },
            complete: function(r)
            {
                this.ajaxEnd(command, r);
            },
            dataType: 'json'
        });
    };
};

ATTP.CORE.AjaxModel.prototype = new ATTP.CORE.AjaxModel.PROTO();



ATTP.CORE.UploadModel = function( rsp, delegate )
{
    this.rsp = rsp;
    this.delegate = delegate;
    this.fakeIframe = null;
    this.uniqId = ATTP.CORE.uniqId('uploadModel');

    ATTP.CORE.ObjectRegistry[this.uniqId] = this;
};

ATTP.CORE.UploadModel.PROTO = function()
{
    this.upload = function( file, command, query )
    {
        query = query || {};

        var form, parent;
        parent = $(file).parent();

        this.fakeIframe = $('<iframe id="iframe-' + this.uniqId + '" name="attachmentHandler" style="display: none"></iframe>');
        form = $('<form enctype="multipart/form-data" method="POST" target="attachmentHandler" style="display: none"></form>');

        form.attr('action', this.rsp);
        form.append('<input type="hidden" name="uniqId" value="' + this.uniqId + '"/>');
        form.append('<input type="hidden" name="command" value="' + command + '"/>');
        form.append('<input type="hidden" name="query" value=\'' + JSON.stringify(query) + '\' />');

        form.append(file);

        $('body').prepend(form).prepend(this.fakeIframe);
        this.delegate.uploadStart(command, query);

        form.get(0).submit();

        parent.append(file);
        form.remove();
    };

    this.uploadComplete = function(r)
    {
        this.fakeIframe.remove();

        if ( r.type != 'uploadError' )
        {
            try
            {
                this.delegate.uploadSuccess(r);
            }
            catch ( e )
            {
                alert(e);
            }
        }

        this.delegate.uploadEnd(r);
    };
};

ATTP.CORE.UploadModel.prototype = new ATTP.CORE.UploadModel.PROTO();


ATTP.CORE.State = function( data )
{
    data = data || {};
    this.state = data;

    this.observer = new ATTP.CORE.Observer(this);
}

ATTP.CORE.State.PROTO = function()
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

ATTP.CORE.State.prototype = new ATTP.CORE.State.PROTO();


ATTP.CORE.Observer = function( context )
{
    this.events = {};
    this.context = context;
};

ATTP.CORE.Observer.PROTO = function()
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

ATTP.CORE.Observer.prototype = new ATTP.CORE.Observer.PROTO();


ATTP.CORE.uniqId = function( prefix )
{
    prefix = prefix || '';

    return prefix + (Math.ceil(Math.random() * 1000000000)).toString();
}


ATTP.UTILS = {};

ATTP.UTILS.addInvitation = function( nodes )
{
    var $nodes, start, complete;

    $nodes = $(nodes);

    start = function()
    {
        var $self = $(this);
        if ( $self.val() == $self.attr('inv') )
        {
            $self.val('');
        }

        $self.removeClass('invitation');
    };

    complete = function()
    {
        var $self = $(this);
        if ( !$self.val() )
        {
            $self.val($self.attr('inv'));
            $self.addClass('invitation');
        }
    };

    $nodes.unbind('focus.invitation').on('focus.invitation', start);
    $nodes.unbind('blur.invitation').on('blur.invitation', complete);
};

ATTP.UTILS.LinkObserver = function( input, callBack )
{
    this.callback = callBack;
    this.input = $(input);

    this.startObserve();
};

ATTP.UTILS.LinkObserver.prototype =
{
    startObserve: function()
    {
        var self = this;

        var detect = function()
        {
            var val = self.input.val();

            if ( $.trim(val) )
            {
                self.detectLink();
            }
        };

        this.input.bind('paste', function(){
            setTimeout(function() {
                detect();
            }, 100);
        });

        this.input.bind('blur', function(){
            setTimeout(function() {
                detect();
            }, 100);
        });

        this.input.keyup(function(e)
        {
            if (e.keyCode == 32 || e.keyCode == 13) {
                detect();
            }
        });
    },

    detectLink: function()
    {
        var text, rgxp, result;

        text = this.input.val();
        rgxp = /(http(s)?:\/\/)?((\d+\.\d+\.\d+\.\d+)|(([\w-]+\.)+([a-z,A-Z][\w-]*)))(:[1-9][0-9]*)?(\/?([?\w\-.\,\/:%+@&*=]+[\w\-\,.\/?:%+@&=*|]*)?)?/;
        result = text.match(rgxp);

        if ( !result )
        {
            return false;
        }

        this.callback.call(this, result[0]);
    }
};

ATTP.UTILS.observeLinks = function( input, callBack )
{
    return new ATTP.UTILS.LinkObserver(input, callBack);
};


// ------------------------------ </ CORE > ------------------------------


// ------------------------------ < Attachments > ------------------------------

$(function(){
    ATTP.UTILS.addInvitation($('input:text[inv],textarea[inv]'));
});


ATTP.Attachments = function(uniqId, opt)
{
    this.uniqId = uniqId;

    this.result = '';
    this.content = '';
    this.contentType = null;

    this.view = new ATTP.CORE.View(document.getElementById(this.uniqId));

    this.view.$result = this.view.$('.ATT_Result');
    this.view.$resultContent = this.view.$('.ATT_ResultContent');
    this.view.$close = this.view.$('.ATT_BodyClose');
    this.view.$self = $(this.view.node);

    this.observer = new ATTP.CORE.Observer(this);
    this.floatBox = null;

    this.init();
};

ATTP.Attachments.PROTO = function()
{

    this.init = function()
    {
        var self = this;

        this.view.$close.click(function()
        {
            self.hide();
        });

        this.bind('onHide', function()
        {
            this.view.$('.ATT_LinkPanel').appendTo(this.view.$('.ATT_Panels'));
            this.contentType = null;
        });

        this.bind('onBeforeChange', function()
        {
            this.view.$('.ATT_LinkPanel').appendTo(this.view.$('.ATT_Panels'));
        });
    };

    this.bind = function( eventName, callback, context )
    {
        this.observer.bind(eventName, callback, context);
    }

    this.trigger = function( eventName, eventObj )
    {
        this.observer.trigger(eventName, eventObj);
    }

    this.onChange = function(){};

    this.showBox = function( options )
    {
        var self = this;

        options.addClass = 'att-floatbox';
        this.floatBox = new OW_FloatBox(options);
        this.floatBox.$container.addClass('att-floatbox')

        this.floatBox.bind('close', function()
        {
            self.trigger('onBoxClose', this);
        });
    }

    this.sowImagePanel = function()
    {
        var $contents, $title, $controls;

        $contents = this.view.$('.ATT_ImagePanel .ATT_PanelBody');
        $title = this.view.$('.ATT_ImagePanel .ATT_PanelTitle');
        $controls = this.view.$('.ATT_ImagePanel .ATT_PanelControls');

        this.contentType = 'image';

        this.showBox(
            {
                width: 420,
                $contents: $contents,
                $title: $title,
                $controls: $controls
            });
    };

    this.showVideoPanel = function()
    {
        var $contents, $title, $controls;

        $contents = this.view.$('.ATT_VideoPanel .ATT_PanelBody');
        $title = this.view.$('.ATT_VideoPanel .ATT_PanelTitle');
        $controls = this.view.$('.ATT_VideoPanel .ATT_PanelControls');

        this.contentType = 'video';

        this.showBox(
            {
                width: 420,
                $contents: $contents,
                $title: $title,
                $controls: $controls
            });
    };

    this.showLinkPanel = function()
    {
        if ( !this.linkPanel )
        {
            return;
        }

        var $contents = this.view.$('.ATT_LinkPanel');
        $contents.data('returnTo', $contents.parent());

        this.view.$resultContent.empty().append($contents);
        this.view.$result.show();
        this.view.$result.find('.AL_Input').focus();

        this.view.$self.addClass('att-result-full-width');

        this.contentType = 'link';
    };

    this.queryLinkResult = function( link )
    {
        if ( !this.linkPanel )
        {
            return;
        }

        this.linkPanel.queryResult(link);
    };

    this.setResult = function( result )
    {
        this.result = result;
    };

    this.setContent = function( content )
    {
        this.content = content;
    };

    this.save = function()
    {
        this.trigger('onBeforeChange', {
            "result": this.result
        });

        if ( this.content )
        {
            this.view.renderContent(this.content, this.view.$resultContent);
            this.view.$result.show();
            this.view.$self.removeClass('att-result-full-width');
        }

        this.trigger('onChange', {
            "result": this.result
        });
    };

    this.changeResult = function( newres )
    {
        if ( this.result )
        {
            $.extend(this.result, newres);
        }
        else
        {
            this.result = newres;
        }

        this.trigger('onChange', {
            "result": this.result
        });
    };

    this.hide = function()
    {
        if ( this.view.$resultContent.children(":first").data('returnTo') )
        {
            var returnTo = this.view.$resultContent.children(":first").data('returnTo');
            returnTo.append(this.view.$resultContent.children(":first"));
        }

        this.view.$resultContent.empty();
        this.view.$result.hide();

        this.result = '';
        this.content = '';

        this.trigger('onHide');

        this.save();
    };

    this.reset = function()
    {
        this.hide();
        this.trigger('onReset');
    };
};

ATTP.Attachments.prototype = new ATTP.Attachments.PROTO();


ATTP.ImagePanel = function(uniqId, glob)
{
    var self = this;

    this.delegate = ATTP.CORE.ObjectRegistry[glob.delegate];

    this.uniqId = uniqId;
    this.glob = glob;

    this.busy = false;

    this.ajax = new ATTP.CORE.AjaxModel(this.glob.rsp, this);
    this.uploader = new ATTP.CORE.UploadModel(this.glob.uploader, this);

    this.view = new ATTP.CORE.View(document.getElementById(this.uniqId));

    this.view.$result = this.view.$('.AI_Result');
    this.view.$uploadPanel = this.view.$('.AI_UploadPanel');
    this.view.$uploadButton = this.view.$('input.IA_UploadButton');
    this.view.$uploadInput = this.view.$('.AI_UploadInput');

    this.view.$takePanel = this.view.$('.AI_TakePanel');
    this.view.$takeControls = this.view.$('.AI_TakePhotoControls');
    this.view.$takeShootBtn = this.view.$('.AI_TakePhotoBtn');
    this.view.$takeResetBtn = this.view.$('.AI_ResetPhotoBtn');

    this.view.$takeScreen = this.view.$('.AI_TakeScreen');


    this.view.$cancelBtn = this.view.$('.AI_Cancel');
    this.view.$uploadSaveBtn = this.view.$('.AI_UploadSave');
    this.view.$takeSaveBtn = this.view.$('.AI_TakeSave');
    this.view.$closeBtn = this.view.$('.AI_Close');

    this.view.$mainControls = this.view.$('.AI_MainControl');
    this.view.$views = this.view.$('.AI_View');

    this.view.$('.AI_SwitchToTakeButton').click(function()
    {
        self.showTakePanel();
    });

    this.view.$uploadInput.change(function()
    {
        self.uploader.upload(this, 'imageUploader');
    });

    this.view.$cancelBtn.click(function()
    {
        self.goHome();
    });

    this.view.$closeBtn.click(function()
    {
        self.close();
    });

    this.delegate.bind('onBoxClose', function()
    {
        self.goHome();
    });

    this.view.$uploadSaveBtn.click(function()
    {
        self.delegate.save();
        self.delegate.floatBox.close();
    });
}

ATTP.ImagePanel.PROTO = function()
{
    this.ajaxStart = function( command, params ) {};
    this.ajaxEnd = function( response ) {};

    this.ajaxSuccess = function( command, response )
    {
        if ( response.content )
        {

            this.view.$views.hide();
            this.view.renderContent(response.content, this.view.$result);
            this.view.$result.show();

            this.view.$mainControls.hide();
            this.view.$cancelBtn.show();

            if ( command == 'imageUploader' )
            {
                this.view.$uploadSaveBtn.show();
            }

            this.fitWindow();
        }

        if ( response.result )
        {
            this.delegate.setContent(response.result.content);
            this.delegate.setResult(response.result.oembed);

            if ( command == 'webcamHandler' )
            {
                this.view.$takeSaveBtn.find('input').removeClass('ow_inprogress');
                this.delegate.save();
                this.delegate.floatBox.close();
            }
        }
    };

    this.fitWindow = function()
    {
        this.delegate.floatBox.fitWindow();
    };

    this.close = function()
    {
        this.delegate.floatBox.close();
        this.busy = false;
    }

    this.uploadSuccess = function( response )
    {
        this.ajaxSuccess('imageUploader', response);
    };

    this.uploadStart = function( command, params )
    {
        this.view.$uploadButton.addClass('ow_inprogress');
        this.view.$uploadInput.hide();
    };

    this.uploadEnd = function( response )
    {
        this.view.$uploadButton.removeClass('ow_inprogress');
        this.view.$uploadInput.show();

        if ( response.type == 'uploadError' )
        {
            OW.error(response.error);
        }
    };


    this.goHome = function()
    {
        this.busy = false;

        this.view.$views.hide();
        this.view.$mainControls.hide();
        this.view.$uploadPanel.show();
        this.view.$closeBtn.show();

        this.fitWindow();
    };

    /* Take a photo*/

    this.showTakePanel = function()
    {
        this.view.$views.hide();
        this.view.$takePanel.show();

        this.view.$mainControls.hide();
        this.view.$cancelBtn.show();

        this.initWebcam();

        this.fitWindow();
    }

    this.initWebcam = function()
    {
        var loaded = false, self = this;;
        var screen = this.view.$takeScreen;

        this.view.$takeControls.hide();
        this.view.$takeResetBtn.hide();

        if ( webcam.activeScreen )
        {
            webcam.activeScreen.empty();
        }

        webcam.set_swf_url(this.glob.webcam.swf);
        webcam.set_api_url(this.glob.webcam.uploader);
        webcam.set_quality(this.glob.webcam.quality);
        webcam.set_shutter_sound(true, this.glob.webcam.sound);

        // Generating the embed code and adding it to the page:
        screen.html(webcam.get_html(screen.width(), screen.height()));

        webcam.activeScreen = screen;

        webcam.set_hook('onError',function(e)
        {
            loaded = false;
            screen.addClass('ai-take-screen-error');
            screen.text(e);
        });

        webcam.set_hook('onLoad',function(e)
        {
            loaded = true;
            self.view.$takeControls.show();
            self.view.$takeShootBtn.show();
        });

        webcam.set_hook('onComplete',function(r)
        {
            var response = {};

            self.busy = false;
            r = $($.parseXML(r));

            response.result =
            {
                "content": {
                    "html": r.find('content html').text(),
                    "js": r.find('content js').text()
                },

                "oembed": {
                    "type": "photo",
                    "fileId": r.find('fileId').text(),
                    "genId": r.find('fileId').text(),
                    "filePath": r.find('filePath').text()
                }
            };

            self.ajaxSuccess('webcamHandler', response);
        });


        this.view.$takeShootBtn.unbind().click(function()
        {
            if ( !loaded )
            {
                return false;
            }

            webcam.freeze();

            self.view.$takeShootBtn.hide();
            self.view.$takeResetBtn.show();

            self.view.$takeSaveBtn.show();
        });

        this.view.$takeResetBtn.unbind().click(function()
        {
            if ( !loaded )
            {
                return false;
            }

            webcam.reset();

            self.view.$takeShootBtn.show();
            self.view.$takeResetBtn.hide();

            self.view.$takeSaveBtn.hide();
        });


        this.view.$takeSaveBtn.unbind().click(function()
        {
            if ( !loaded || self.busy )
            {
                return false;
            }

            self.busy = true;

            self.view.$takeSaveBtn.find('input').addClass('ow_inprogress');

            webcam.upload();
        });

        /*this.view.$('.AI_SettingPhotoBtn').click(function()
         {
         webcam.configure('camera');
         });*/
    };
}

ATTP.ImagePanel.prototype = new ATTP.ImagePanel.PROTO();


ATTP.VideoPanel = function(uniqId, glob)
{
    var self = this;

    this.busy = false;
    this.delegate = ATTP.CORE.ObjectRegistry[glob.delegate];

    this.oembed = false;

    this.uniqId = uniqId;
    this.glob = glob;

    this.ajax = new ATTP.CORE.AjaxModel(this.glob.rsp, this);
    this.view = new ATTP.CORE.View(document.getElementById(this.uniqId));

    this.view.$views = this.view.$('.AV_View');
    this.view.$mainControls = this.view.$('.AV_MainControl');

    this.view.$home = this.view.$('.AV_Home');

    this.view.$cancelBtn = this.view.$('.AV_Cancel');
    this.view.$saveBtn = this.view.$('.AV_Save');
    this.view.$closeBtn = this.view.$('.AV_Close');

    this.view.$result = this.view.$('.AV_Result');

    this.view.$embedHomeBtn = this.view.$('.AV_EmbedHomeBtn');
    this.view.$embedHomeInput = this.view.$('.AV_EmbedHomeInput');
    this.view.$embedHomeC = this.view.$('.AV_EmbedHomeC');

    this.view.$YTSearchHomeInputC = this.view.$('.AV_YT_SearchHomeInputC');
    this.view.$YTSearchHomeInput = this.view.$('.AV_YT_SearchHomeInput');
    this.view.$YTSearchHomehBtn = this.view.$('.AV_YT_SearchHomeBtn');
    this.view.$YTSearchInput = this.view.$('.AV_YT_SearchInput');

    this.view.$cancelBtn.click(function()
    {
        self.goHome();
    });

    this.view.$closeBtn.click(function()
    {
        self.close();
    });

    this.view.$embedHomeInput.on('input keyup', function() //TODO Refactor when IE will support onInput event
    {
        self.setOembed({
            type: 'video',
            html: this.value
        });
    });

    this.view.$YTSearchHomehBtn.bind('click', function()
    {
        self.ytSearch();
    });

    this.view.$YTSearchHomeInput.bind('keydown', function(e)
    {
        if ( e.keyCode == 13 )
        {
            self.ytSearch();
        }
    });

    this.delegate.bind('onBoxClose', function()
    {
        self.goHome();
    });

    this.view.$saveBtn.click(function()
    {
        self.query('videoPreview', {oembed: self.oembed});
    });
};

ATTP.VideoPanel.PROTO = function()
{
    this.goHome = function()
    {
        this.view.$views.hide();
        this.view.$mainControls.hide();
        this.view.$home.show();
        this.view.$closeBtn.show();
        this.view.$YTSearchHomeInput.val('').blur();
        this.view.$embedHomeInput.val('').blur();

        this.view.$result.empty();

        this.fitWindow({
            width: 420
        });
    };

    this.fitWindow = function( params )
    {
        this.delegate.floatBox.fitWindow(params);
    };

    this.close = function()
    {
        this.delegate.floatBox.close();
    };

    this.query = function(command, params)
    {
        if ( this.busy )
        {
            return false;
        }

        params.window = {
            width: jQuery(window).width(),
            height: jQuery(window).height()
        };

        params.uniqId = this.uniqId;

        this.ajax.query(command, params);
    }

    this.ytSearch = function()
    {
        var query = this.view.$YTSearchHomeInput.val(),
            inv = this.view.$YTSearchHomeInput.attr('inv');

        if ( query == inv || !$.trim(query) )
        {
            return false;
        }

        this.query('ytSearch', {
            "query": query
        });
    };

    this.ajaxStart = function( command, params )
    {
        this.busy = true;

        if ( command == 'videoPreview' )
        {
            this.view.$saveBtn.find('input').addClass('ow_inprogress');
        }

        if ( command == 'videoRenderEmbed' )
        {
            this.view.$embedHomeBtn.removeClass('ow_ic_add').addClass('ow_preloader');
        }

        if ( command == 'ytSearch' )
        {
            this.view.$YTSearchHomehBtn.removeClass('ow_ic_lens').addClass('ow_preloader');
        }
    };

    this.ajaxEnd = function( command, response )
    {
        this.busy = false;

        if ( command == 'videoPreview' )
        {
            this.view.$saveBtn.find('input').removeClass('ow_inprogress');
        }

        if ( command == 'videoRenderEmbed' )
        {
            this.view.$embedHomeBtn.addClass('ow_ic_add').removeClass('ow_preloader');
        }

        if ( command == 'ytSearch' )
        {
            this.view.$YTSearchHomehBtn.addClass('ow_ic_lens').removeClass('ow_preloader');
        }
    };

    this.ajaxSuccess = function( command, response )
    {
        if ( response.error )
        {
            OW.error(response.error);

            return;
        }

        if ( command == 'videoPreview' )
        {
            this.delegate.setResult(response.oembed);
            this.delegate.setContent(response.content);
            this.delegate.save();

            this.close();

            return;
        }

        if ( response.content )
        {
            this.view.renderContent(response.content, this.view.$result);

            this.view.$views.hide();
            this.view.$result.show();

            this.view.$mainControls.hide();
            this.view.$cancelBtn.show();

            this.fitWindow(response.fb || {});
        }

        if ( response.content && command == 'videoRenderEmbed' )
        {
            this.view.$saveBtn.show();
        }
    };

    this.setOembed = function( oembed )
    {
        this.oembed = oembed;

        if ( this.oembed && this.oembed.html )
        {
            this.view.$saveBtn.show();
        }
        else
        {
            this.view.$saveBtn.hide();
        }
    };
}

ATTP.VideoPanel.prototype = new ATTP.VideoPanel.PROTO();


ATTP.YouTubeList = function( uniqId, glob )
{
    var self = this,
        data = glob.data, searchDelegate,
        busy = false;

    this.uniqId = uniqId;
    this.delegate = ATTP.CORE.ObjectRegistry[glob.delegate];

    this.ajax = new ATTP.CORE.AjaxModel(glob.rsp, this);
    this.view = new ATTP.CORE.View(document.getElementById(this.uniqId));

    ATTP.UTILS.addInvitation(this.view.$('input:text[inv],textarea[inv]'));

    this.view.$list = this.view.$('.YT_List');

    /*this.view.$list.on('jsp-arrow-change', function( event, isAtTop, isAtBottom, isAtLeft, isAtRight )
    {
        if ( isAtBottom )
        {
            console.log(123);
        }
    });*/

    this.view.$listBody = this.view.$('.YT_ListBody');
    this.view.$more = this.view.$('.YT_More');
    this.view.$morelabel = this.view.$('.YT_MoreLabel');

    this.view.$input = this.view.$('.YT_Input');
    this.view.$search = this.view.$('.YT_SearchBtn');

    searchDelegate = function()
    {
        var val = self.view.$input.val(),
            inv = self.view.$input.attr('inv');

        if ( val == inv || !$.trim(val) )
        {
            return false;
        }

        self.query('ytSearchList', {
            "query": val
        });
    };

    this.view.$search.click(searchDelegate);
    this.view.$input.bind('keydown', function(e)
    {
        if ( e.keyCode == 13 )
        {
            searchDelegate.apply(this);
        }
    });

    this.view.$more.click(function()
    {
        self.more();
    });

    this.more = function()
    {
        self.query('ytMore');
    };

    this.bindItems = function( items )
    {
        var self = this;

        $(items).each(function()
        {
            self.bindItem(this);
        });
    };

    this.bindItem = function( item )
    {
        var self = this, $item = $(item), oembed;

        $item.click(function()
        {
            if ( $item.hasClass('YTL_ItemSelected') )
            {
                return;
            }

            self.resetItems($item);
            $item.addClass('ytl-item-selected').addClass('YTL_ItemSelected');

            oembed = $item.find('.YTL_ItemOembed').val();

            self.delegate.setOembed($.parseJSON(oembed));
        });

        $item.find('.YTL_ItemThumb').click(function()
        {
            self.showItemVideo($item);
        });
    };

    this.updateScroll = function( toTop )
    {
        this.view.$list.css("overflow", "hidden");
        window.setTimeout(function()
        {
            var scrollApi;

            self.view.$list.css("overflow", "auto");
            scrollApi = OW.addScroll(self.view.$list);
            if ( toTop )
            {
                scrollApi.scrollToY(0, false);
            }
        });
    };

    this.resetItems = function( item )
    {
        if ( item )
        {
            self.view.$('.YTL_Item').not(item).removeClass('ytl-item-selected').removeClass('yt-item-video-view').removeClass('YTL_ItemSelected');
            self.view.$('.YTL_ItemVideo').not(item.find('.YTL_ItemVideo')).empty().hide();
            self.view.$('.YTL_ItemThumb').not(item.find('.YTL_ItemThumb')).show();
        }
        else
        {
            self.view.$('.YTL_Item').removeClass('ytl-item-selected').removeClass('yt-item-video-view').removeClass('YTL_ItemSelected');
            self.view.$('.YTL_ItemVideo').empty().hide();
            self.view.$('.YTL_ItemThumb').show();
        }
    };

    this.showItemVideo = function( item )
    {
        var embed, video, $video;
        $video = item.find('.YTL_ItemVideo');
        video = $video.attr('ytvideo');
        embed = $('<iframe width="260" height="200" src="http://www.youtube.com/embed/' + video + '?autoplay=1" frameborder="0" allowfullscreen></iframe>');

        item.addClass("yt-item-video-view");
        item.find('.YTL_ItemThumb').hide();
        $video.html(embed).show();

        this.updateScroll();
    };

    this.query = function( command, query )
    {
        if ( busy )
        {
            return false;
        }

        query = query || {};

        query.window = {
            width: jQuery(window).width(),
            height: jQuery(window).height()
        };

        query.data = data;
        this.ajax.query(command, query);
    };

    this.ajaxStart = function( command, response )
    {
        busy = true;

        if ( command == 'ytMore' )
        {
            this.view.$morelabel.addClass('ow_preloader');
        }

        if ( command == 'ytSearchList' )
        {
            this.view.$search.removeClass('ow_ic_lens').addClass('ow_preloader');
        }
    };

    this.ajaxEnd = function( command, response )
    {
        busy = false;

        if ( command == 'ytMore' )
        {
            this.view.$morelabel.removeClass('ow_preloader');
        }

        if ( command == 'ytSearchList' )
        {
            this.view.$search.addClass('ow_ic_lens').removeClass('ow_preloader');
        }
    };

    this.ajaxSuccess = function( command, response )
    {
        if ( response.more )
        {
            var $html = $(response.more.html);
            this.view.$listBody.append($html);
            this.bindItems($html);
        }

        if ( response.content )
        {
            this.view.renderContent(response.content, this.view.$listBody);

            this.bindItems(this.view.$('.YTL_Item'));

            this.delegate.fitWindow(response.fb || {});
        }

        if ( response.data )
        {
            data = response.data;
        }

        if ( response.viewMore )
        {
            this.view.$more.show();
        }
        else
        {
            this.view.$more.hide();
        }

        this.updateScroll(command != 'ytMore');
    };


    this.bindItems(this.view.$('.YTL_Item'));
    this.updateScroll(true);
};



ATTP.LinkPanel = function(uniqId, glob)
{
    var self = this;

    this.processedLink = false;
    this.shown = false;
    this.busy = false;
    this.delegate = ATTP.CORE.ObjectRegistry[glob.delegate];

    this.delegate.linkPanel = this;

    this.uniqId = uniqId;
    this.glob = glob;

    this.ajax = new ATTP.CORE.AjaxModel(this.glob.rsp, this);
    this.view = new ATTP.CORE.View(document.getElementById(this.uniqId));

    this.view.$input = this.view.$('.AL_Input');
    this.view.$add = this.view.$('.AL_Add');

    this.delegate.bind('onHide', function()
    {
        self.view.$input.val('');
        self.shown = false;
    });

    this.delegate.bind('onReset', function()
    {
        self.processedLink = null;
    });

    this.delegate.bind('onBeforeChange', function()
    {
        self.view.$input.val('');
        self.shown = false;
    });

    this.view.$add.bind('click', function()
    {
        self.shown = false;
        self.processedLink = null;
        self.queryResult(self.view.$input.val());

        return false;
    });

    this.view.$input.bind('keydown', function(e)
    {
        if ( e.keyCode == 13 )
        {
            self.shown = false;
            self.processedLink = null;
            self.queryResult(self.view.$input.val());

            return false;
        }
    });
};

ATTP.LinkPanel.PROTO = function()
{
    this.queryResult = function( link )
    {
        link = $.trim(link);

        if ( this.shown || link == this.processedLink )
        {
            return;
        }

        this.view.$input.val(link);

        this.query('queryLink', {
            "link": link
        });
    };

    this.query = function(command, params)
    {
        if ( this.busy )
        {
            return false;
        }

        params.uniqId = this.uniqId;

        this.ajax.query(command, params);
    }


    this.ajaxStart = function( command, params )
    {
        this.busy = true;

        $(this.view.node).removeClass('ow_ic_add').addClass('ow_preloader');
    };

    this.ajaxEnd = function( command, response )
    {
        this.busy = false;

        $(this.view.node).removeClass('ow_preloader').addClass('ow_ic_add');
    };

    this.ajaxSuccess = function( command, response )
    {
        if ( response.content )
        {
            this.delegate.content = response.content;
        }

        if ( response.result )
        {
            this.delegate.result = response.result;
        }

        if ( response.result || response.content )
        {
            this.delegate.save();
        }

        if ( response.processedUrl )
        {
            this.processedLink = response.processedUrl;
            this.shown = true;
        }
    };

    this.changeThumb = function( url )
    {
        this.delegate.changeResult({
            "thumbnail_url": url
        });
    };
}

ATTP.LinkPanel.prototype = new ATTP.LinkPanel.PROTO();


ATTP.Attachment = function(uniqId, delegate)
{
    var self = this;

    this.delegate = ATTP.CORE.ObjectRegistry[delegate];
    this.uniqId = uniqId;
    this.node = document.getElementById(this.uniqId);
    this.onChange = function(){};

    //OW.resizeImg(this.$('.EQ_AttachmentImageC'),{width:'150'});

    this.$('.ATT_SelectPicture').click(function()
    {
        self.showImageSelector();
    });
};


ATTP.Attachment.PROTO = function()
{
    this.$ = function (sel)
    {
        return $(sel, this.node);
    };

    this.showImageSelector = function()
    {
        var fb, $contents, self = this;

        $contents = this.$('.ATT_PicturesFbContent')
        fb = new OW_FloatBox({
            $title: this.$('.ATT_PicturesFbTitle'),
            icon_class: 'ow_ic_picture',
            $contents: $contents,
            width: 520
        });

        $contents.find('.ATT_PictureItem').unbind().click(function()
        {
            var img = $('img', this);
            self.changeImage(img.attr('src'));

            fb.close();
        });
    };

    this.changeImage = function( url )
    {
        var clone, original;

        original = this.$('.ATT_ItemImage');
        clone = original.clone();
        clone.attr('src', url);
        original.replaceWith(clone);

        this.delegate.changeThumb(url);
    };

};
ATTP.Attachment.prototype = new ATTP.Attachment.PROTO();

ATTP.AttachmentsControl = function(uniqId, params)
{
    var form = owForms[params.formName], attachments, input, view;

    view = new ATTP.CORE.View(document.getElementById(uniqId));

    input = $('#' + params.inputId);

    attachments = ATTP.CORE.ObjectRegistry[params.attachmentId] ? ATTP.CORE.ObjectRegistry[params.attachmentId] : null;
    ATTP.UTILS.observeLinks(input, function(link)
    {
        attachments.queryLinkResult(link);
    });

    view.$('.EQ_AttachmentPhoto').click(function()
    {
        attachments.sowImagePanel();
    });

    view.$('.EQ_AttachmentVideo').click(function()
    {
        attachments.showVideoPanel();
    });

    view.$('.EQ_AttachmentLink').click(function()
    {
        attachments.showLinkPanel();
    });

    attachments.bind('onChange', function( state )
    {
        var attInp;

        attInp = $('#' + params.attachmentInputId);

        if ( state.result )
        {
            attInp.val(JSON.stringify(state.result));
        }
        else
        {
            attInp.val('');
        }

    });

    ATTP.UTILS.addInvitation(view.$('input:text[inv],textarea[inv]'));
};

ATTP.playVideoCallback = function( node, uniqId )
{
    var v = $(node).hide().next('.ATTP-Video-Player');
    v.show().html(v.find('textarea').val());
    
    if ( uniqId )
    {
        $("#" + uniqId).addClass("attp-video-playing");
    }
};

ATTP.showImageCallback = function( src )
{
    var floatBox = new OW_FloatBox({layout: 'empty'});
    var $fakeImageC = $('<div></div>');
    var $fakeImg = $('<img src="'+src+'" />');

    var nHeight = $(window).height();
    var nWidth = $(window).width();

    $fakeImg.css({
        "max-height": nHeight - 20 - (nHeight / 100) * 12,
        "max-width": nWidth - (nWidth / 100) * 10
    });

    $fakeImg.addClass('attp-floatbox-image');
    $fakeImageC.append($fakeImg);
    $fakeImageC.css({visibility:'hidden',position:'absolute',left:'-9999px'});
    $(document.body).append ($fakeImageC);

    var width = $fakeImg.width();

    $fakeImg.load(function()
    {
        width = $fakeImg.width();

        floatBox.setContent($fakeImg);

        floatBox.fitWindow({
            "width": width
        });
    });

    if ( width )
    {
        floatBox.fitWindow({
            "width": width
        });
    }

    floatBox.fitWindow({
        "width": width
    });

    floatBox.bind('close', function(){
        $fakeImageC.remove();
    });
};

// ------------------------------ </ Attachments > ------------------------------

// ------------------------------ < AjaxLoader > ------------------------------

if ( window.ATTPAjaxLoadCallbackQueue )
{
    $.each(window.ATTPAjaxLoadCallbackQueue, function(i, fnc)
    {
        fnc.call();
    })
}

// ------------------------------ </ AjaxLoader > ------------------------------


// ------------------------------ < Webcam > ------------------------------

/* JPEGCam v1.0.9 */
/* Webcam library for capturing JPEG images and submitting to a server */
/* Copyright (c) 2008 - 2009 Joseph Huckaby <jhuckaby@goldcartridge.com> */
/* Licensed under the GNU Lesser Public License */
/* http://www.gnu.org/licenses/lgpl.html */

/* Usage:
	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
		webcam.set_api_url( 'test.php' );
		webcam.set_hook( 'onComplete', 'my_callback_function' );
		function my_callback_function(response) {
			alert("Success! PHP returned: " + response);
		}
	</script>
	<a href="javascript:void(webcam.snap())">Take Snapshot</a>
*/

// Everything is under a 'webcam' Namespace
window.webcam = {
	version: '1.0.9',

	// globals
	ie: !!navigator.userAgent.match(/MSIE/),
	protocol: location.protocol.match(/https/i) ? 'https' : 'http',
	callback: null, // user callback for completed uploads
	swf_url: 'webcam/webcam.swf', // URI to webcam.swf movie (defaults to cwd)
	shutter_url: 'webcam/shutter.mp3', // URI to shutter.mp3 sound
	api_url: '', // URL to upload script
	loaded: false, // true when webcam movie finishes loading
	quality: 90, // JPEG quality (1 - 100)
	shutter_sound: true, // shutter sound effect on/off
	stealth: false, // stealth mode (do not freeze image upon capture)
	hooks: {
		onLoad: null,
		onComplete: null,
		onError: null
	}, // callback hook functions

	set_hook: function(name, callback) {
		// set callback hook
		// supported hooks: onLoad, onComplete, onError
		if (typeof(this.hooks[name]) == 'undefined')
			return alert("Hook type not supported: " + name);

		this.hooks[name] = callback;
	},

	fire_hook: function(name, value) {
		// fire hook callback, passing optional value to it
		if (this.hooks[name]) {
			if (typeof(this.hooks[name]) == 'function') {
				// callback is function reference, call directly
				this.hooks[name](value);
			}
			else if (typeof(this.hooks[name]) == 'array') {
				// callback is PHP-style object instance method
				this.hooks[name][0][this.hooks[name][1]](value);
			}
			else if (window[this.hooks[name]]) {
				// callback is global function name
				window[ this.hooks[name] ](value);
			}
			return true;
		}
		return false; // no hook defined
	},

	set_api_url: function(url) {
		// set location of upload API script
		this.api_url = url;
	},

	set_swf_url: function(url) {
		// set location of SWF movie (defaults to webcam.swf in cwd)
		this.swf_url = url;
	},

	get_html: function(width, height, server_width, server_height) {
		// Return HTML for embedding webcam capture movie
		// Specify pixel width and height (640x480, 320x240, etc.)
		// Server width and height are optional, and default to movie width/height
		if (!server_width) server_width = width;
		if (!server_height) server_height = height;

		var html = '';
		var flashvars = 'shutter_enabled=' + (this.shutter_sound ? 1 : 0) +
			'&shutter_url=' + escape(this.shutter_url) +
			'&width=' + width +
			'&height=' + height +
			'&server_width=' + server_width +
			'&server_height=' + server_height;

		if (this.ie) {
			html += '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="'+this.protocol+'://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="'+width+'" height="'+height+'" id="webcam_movie" align="middle"><param name="wmode" value="opaque" /><param name="allowScriptAccess" value="always" /><param name="allowFullScreen" value="false" /><param name="movie" value="'+this.swf_url+'" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="'+flashvars+'"/></object>';
		}
		else {
			html += '<embed wmode="opaque" id="webcam_movie" src="'+this.swf_url+'" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="'+width+'" height="'+height+'" name="webcam_movie" align="middle" allowScriptAccess="always" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="'+flashvars+'" />';
		}

		this.loaded = false;
		return html;
	},

	get_movie: function() {
		// get reference to movie object/embed in DOM
		if (!this.loaded) return alert("ERROR: Movie is not loaded yet");
		var movie = document.getElementById('webcam_movie');
		if (!movie) alert("ERROR: Cannot locate movie 'webcam_movie' in DOM");
		return movie;
	},

	set_stealth: function(stealth) {
		// set or disable stealth mode
		this.stealth = stealth;
	},

	snap: function(url, callback, stealth) {
		// take snapshot and send to server
		// specify fully-qualified URL to server API script
		// and callback function (string or function object)
		if (callback) this.set_hook('onComplete', callback);
		if (url) this.set_api_url(url);
		if (typeof(stealth) != 'undefined') this.set_stealth( stealth );

		this.get_movie()._snap( this.api_url, this.quality, this.shutter_sound ? 1 : 0, this.stealth ? 1 : 0 );
	},

	freeze: function() {
		// freeze webcam image (capture but do not upload)
		this.get_movie()._snap('', this.quality, this.shutter_sound ? 1 : 0, 0 );
	},

	upload: function(url, callback) {
		// upload image to server after taking snapshot
		// specify fully-qualified URL to server API script
		// and callback function (string or function object)
		if (callback) this.set_hook('onComplete', callback);
		if (url) this.set_api_url(url);

		this.get_movie()._upload( this.api_url );
	},

	reset: function() {
		// reset movie after taking snapshot
		this.get_movie()._reset();
	},

	configure: function(panel) {
		// open flash configuration panel -- specify tab name:
		// "camera", "privacy", "default", "localStorage", "microphone", "settingsManager"
		if (!panel) panel = "camera";
		this.get_movie()._configure(panel);
	},

	set_quality: function(new_quality) {
		// set the JPEG quality (1 - 100)
		// default is 90
		this.quality = new_quality;
	},

	set_shutter_sound: function(enabled, url) {
		// enable or disable the shutter sound effect
		// defaults to enabled
		this.shutter_sound = enabled;
		this.shutter_url = url ? url : 'shutter.mp3';
	},

	flash_notify: function(type, msg) {
		// receive notification from flash about event
		switch (type) {
			case 'flashLoadComplete':
				// movie loaded successfully
				this.loaded = true;
				this.fire_hook('onLoad');
				break;

			case 'error':
				// HTTP POST error most likely
				if (!this.fire_hook('onError', msg)) {
					alert("JPEGCam Flash Error: " + msg);
				}
				break;

			case 'success':
				// upload complete, execute user callback function
				// and pass raw API script results to function
				this.fire_hook('onComplete', msg.toString());
				break;

			default:
				// catch-all, just in case
				alert("jpegcam flash_notify: " + type + ": " + msg);
				break;
		}
	}
};
