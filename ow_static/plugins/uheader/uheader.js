/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package uheader.static
 */
UHEADER = {};

UHEADER.CORE = {};

UHEADER.CORE.ObjectRegistry = {};

UHEADER.CORE.uniqId = function( prefix )
{
    prefix = prefix || '';

    return prefix + (Math.ceil(Math.random() * 1000000000)).toString();
}

/**
* Model
*/
UHEADER.CORE.AjaxModel = function( rsp, delegate )
{
   this.rsp = rsp;
   this.delegate = delegate;

   this.delegate.ajaxEnd = this.delegate.ajaxEnd || function(){};
   this.delegate.ajaxSuccess = this.delegate.ajaxSuccess || function(){};
   this.delegate.ajaxStart = this.delegate.ajaxStart || function(){};
};

UHEADER.CORE.AjaxModel.PROTO = function()
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

UHEADER.CORE.AjaxModel.prototype = new UHEADER.CORE.AjaxModel.PROTO();

UHEADER.CORE.UploadModel = function( rsp, delegate )
{
    this.rsp = rsp;
    this.delegate = delegate;
    this.fakeIframe = null;
    this.uniqId = UHEADER.CORE.uniqId('uploadModel');

    UHEADER.CORE.ObjectRegistry[this.uniqId] = this;
};

UHEADER.CORE.UploadModel.PROTO = function()
{
    this.upload = function( file, query )
    {
        query = query || {};

        var form, parent, submitter;
        parent = $(file).parent();

        this.fakeIframe = $('<iframe id="iframe-' + this.uniqId + '" name="attachmentHandler" style="display: none"></iframe>');
        form = $('<form enctype="multipart/form-data" method="POST" target="attachmentHandler" style="display: none"></form>');

        form.attr('action', this.rsp);
        form.append('<input type="hidden" name="uniqId" value="' + this.uniqId + '"/>');
        form.append('<input type="hidden" name="query" value=\'' + JSON.stringify(query) + '\' />');
        submitter = $('<input type="submit" value="Submit" />');
        form.append(submitter);

        form.append(file);

        $('body').prepend(form).prepend(this.fakeIframe);
        this.delegate.uploadStart(query);

        form.find("#submit-" + this.uniqId);
        form.submit();
        
        parent.append(file);
        form.remove();
    };

    this.uploadComplete = function(r)
    {
        this.fakeIframe.remove();

        if ( r.type !== 'upload' )
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

UHEADER.CORE.UploadModel.prototype = new UHEADER.CORE.UploadModel.PROTO();

UHEADER.bindDrag = function($node, delegate)
{
    $node = $($node);

    if ($node.data.uhDrag)
    {
        $node.data.uhDrag.setDelegate(delegate);

        return $node.data.uhDrag;
    }

    var uhDelegate = delegate;

    var uhDrag = {};

    var notify = function( method, position )
    {
        var result = true, preventDefault = false;

        var event = {
            target: $node,
            position: position,

            preventDefault: function()
            {
                preventDefault = true;
            }
        };

        if ( uhDelegate[method] )
        {
            result = uhDelegate[method].call(uhDelegate, event);
        }

        return result === false ? false : !preventDefault;
    };

    uhDrag.setDelegate = function( d )
    {
        uhDelegate = d;
    };

    var dragging = false,
        pressed = false;

    var position = {
        x: 0,
        y: 0
    };

    var start = {
        x: 0,
        y: 0
    };

    var delegates =
    {
        mouseDown: function( e )
        {
            pressed = true;

            position.y = 0;
            position.x = 0;

            start.y = e.pageY;
            start.x = e.pageX;

            if ( !notify('start', position) )
            {
                pressed = false;
            }
        },

        mouseMove: function( e )
        {
            if ( !pressed ) return;

            dragging = true;

            var p = {
                x: e.pageX - start.x,
                y: e.pageY - start.y
            };

            if ( notify('drag', p) )
            {
                position = p;
            }
        },

        mouseUp: function()
        {
            if ( !pressed ) return;

            pressed = false;
            dragging = false;

            notify('end', position);
        }
    };

    $node.mousedown(delegates.mouseDown);
    $(document.body).mousemove(delegates.mouseMove);
    $(document.body).mouseup(delegates.mouseUp);

    $(document.body).on('selectstart', function()
    {
        return !dragging;
    });

    $node.attr('unselectable', 'on')
        .css('user-select', 'none')
        .on('selectstart', false);
};

UHEADER.CoverMenu = function(delegate)
{
   //$('.ow_context_action').off();

   var $uploadInput = $('#uh-upload-cover');
   var $uploadInputClone = $uploadInput.clone();

    $(document).on("change.upload", "#uh-upload-cover", function()
    {
        delegate.upload($uploadInput);
        
        var newInput = $uploadInputClone.clone();
        $uploadInput.replaceWith(newInput);
        $uploadInput = newInput;
    });

    $('#uhco-stick').click(function()
    {
        delegate.stick();
    });

    $('#uhco-reposition').click(function()
    {
        delegate.reposition();
    });
    
    /*$('#uhco-upload').click(function()
    {
        var fakeFile = $('<input type="file" name="file" style="top: -2000px; visibility: hidden; position: absolute;" />');
        fakeFile.prependTo("body");
        
        fakeFile.change(function() {
            delegate.upload(fakeFile);
            
            fakeFile.remove();
        });
        
        fakeFile.click();
    });*/

    $('#uhco-remove').click(function()
    {
        delegate.remove();
    });

    $('#uhco-gallery').click(function()
    {
        delegate.gallery();
    });
    
    $('#uhco-restore-default').click(function() {
        delegate.restoreDefault();
    });
};

UHEADER.Cover = function( options, delegate )
{
    var userId = options.userId;
    var $cover = $('#uh-cover');

    var launcher = new UHEADER.CoverImage.nativeLauncher(userId, options.cover);
    UHEADER.CoverImage.setLauncher(launcher);

    $(document).on('click', '#uh-cover-image-wrap', function( e )
    {
        if ( $cover.hasClass("uh-is-default-template") ) {
            return;
        }
        
        if ( !$(e.target).is(".uh-head-text *") )
            UHEADER.CoverImage.show();
    });

    if ( options.viewOnlyMode )
    {
        return;
    }

    var imageLoaded = true;
    var $image = $('#uh-cover-image');
    var $overlay = $('#uh-cover-overlay');
    var $conextMenu = $('.uh-cover-add-btn-wrap', '#uhc-controls');
    var coverData = {};

    coverData.templateId = options.cover.templateId;
    coverData.defaultTemplateId = options.cover.templateId;

    coverData.position = {
        top: 0,
        left: 0
    };

    coverData.canvas = {
        height: $cover.height(),
        width: $cover.width()
    };

    coverData.userId = userId;

    var setImage = function( src )
    {
        if ( $image.attr('src') !== src )
        {
            var tmpImage = $image.clone();
            tmpImage.attr('src', src);
            $image.replaceWith(tmpImage);
            $image = tmpImage;
        }

        $image.show();
        imageLoaded = true;

        $cover.removeClass('uh-cover-no-cover');
        $cover.addClass('uh-cover-has-cover');
        
        $conextMenu.addClass('ow_photo_context_action'); // Design hack
    };

    var unsetImage = function( src )
    {
        $image.hide();

        imageLoaded = false;

        $cover.removeClass('uh-cover-has-cover');
        $cover.addClass('uh-cover-no-cover');

        $conextMenu.removeClass('ow_photo_context_action'); // Design hack
    };

    var coverMode = 'view';
    var switchMode = function( mode )
    {
       $cover.removeClass('uh-cover-mode-' + coverMode);
       coverMode = mode;
       $cover.addClass('uh-cover-mode-' + coverMode);

       delegate.switchMode(mode);
    };
    
    var setRatio = function( ratio ) {
        $cover.find(".uh-scaler-img").css("width", ratio + "%");
    };

    // Upload Model

    var uploadModel, uploadDelegate = {};

    uploadDelegate.uploadStart = function( query )
    {
        switchMode('loading');
    };

    uploadDelegate.uploadSuccess = function( response )
    {
        if ( response.message )
        {
            OW.info(response.message);
        }

        if (response.src)
        {
            setImage(response.src);
        }

        if ( response.data )
        {
            coverData.position.top = response.data.position.top;
            coverData.position.left = response.data.position.left;

            $image.css(coverData.position);

            if ( response.data.css )
            {
                coverData.css = response.data.css;
                $image.css(coverData.css);
            }
        }
        
        if ( response.ratio ) {
            setRatio(response.ratio);
        }
        
        coverData.templateId = response.templateId || null;

        switchMode('reposition');
    };

    uploadDelegate.uploadEnd = function( response )
    {
        if ( response.type === 'error' )
        {
            switchMode('view');
            OW.error(response.error);
        }
    };

    uploadModel = new UHEADER.CORE.UploadModel(options.uploader, uploadDelegate);

    // Ajax Model

    var ajaxModel, ajaxDelegate = {};

    ajaxDelegate.$controls = $('#uhc-controls');

    ajaxDelegate.ajaxStart = function( command )
    {
        if ( command === 'removeCover' )
        {
            return;
        }

        if ( command === 'addFromPhotos' 
                || command === 'chooseTemplate'
                || command === 'switchToDefaultTemplates' )
        {
            switchMode('loading');

            return;
        }

        this.$controls.addClass('ow_preloader').addClass('uh-cover-controls-loading');
    };

    ajaxDelegate.ajaxSuccess = function( command, response )
    {
        if ( response.type === 'error' )
        {
            OW.error(response.error);
            switchMode('view');

            return;
        }

        if ( response.message )
        {
            OW.info(response.message);
        }

        if ( !response.src )
        {
            unsetImage();
        }
        else
        {
            setImage(response.src);
        }

        if ( response.data )
        {
            coverData.position.top = response.data.position.top;
            coverData.position.left = response.data.position.left;

            $image.css(coverData.position);

            if ( response.data.css )
            {
                coverData.css = response.data.css;
                $image.css(coverData.css);
            }
        }
        
        if ( response.ratio ) {
            setRatio(response.ratio);
        }
        
        coverData.templateId = response.templateId || null;

        if ( response.mode ) {
            switchMode(response.mode);
        } else {
            switchMode('view');
        }
        
        if ( response.defaultTemplateMode ) {
            $cover.addClass("uh-is-default-template");
        }
        else {
            $cover.removeClass("uh-is-default-template");
        }
    };

    ajaxDelegate.ajaxEnd = function( command )
    {
        if ( command === 'removeCover' )
        {
            return;
        }

        this.$controls.removeClass('ow_preloader').removeClass('uh-cover-controls-loading');
    };

    ajaxModel = new UHEADER.CORE.AjaxModel(options.responder, ajaxDelegate);

    // Gallery
    var galleryDelegate = {};
    
    galleryDelegate.selectPhoto = function( photoId ) {
        ajaxModel.query('addFromPhotos', {
            "photoId": photoId,
            "userId": userId,
            "height": $cover.height(),
            "width": $cover.width()
        });
    };
    
    galleryDelegate.selectTemplate = function( templateId, reposition ) {
        reposition = reposition === false ? false : true;
        
        ajaxModel.query('chooseTemplate', {
            "templateId": templateId,
            "userId": userId,
            "reposition": reposition
        });
    };
    
    UHEADER.GallerySwitcher.setDelegate(galleryDelegate);

    // Toolbar

    var toolbarDelegate = {};

    toolbarDelegate.upload = function( input )
    {
        uploadModel.upload(input, {
            height: $cover.height(),
            width: $cover.width()
        });
    };

    toolbarDelegate.reposition = function()
    {
        switchMode('reposition');
    };

    toolbarDelegate.remove = function()
    {
        if ( !confirm(OW.getLanguageText('uheader', 'delete_cover_confirmation')) )
        {
            return false;
        }

        unsetImage();
        switchMode('view');

        ajaxModel.query('removeCover', coverData);
    };

    toolbarDelegate.gallery = function()
    {
        var options = {};
        
        options.winHeight = $(window).height();
        options.winWidth = $(window).width();

        UHEADER.GallerySwitcher.show(userId, options);
    };
    
    toolbarDelegate.restoreDefault = function() 
    {
        if ( !confirm(OW.getLanguageText('uheader', 'restore_cover_confirmation')) )
        {
            return false;
        }
        
        ajaxModel.query('switchToDefaultTemplates', coverData);
    };
    
    toolbarDelegate.stick = function()
    {
        ajaxModel.query('stickTemplate', coverData);
    };

    UHEADER.CoverMenu(toolbarDelegate);


    var dragDelegate = {};

    dragDelegate.startPosition = {},
    dragDelegate.dimension = {},
    dragDelegate.direction = 'all',

    //dragDelegate.image =

    dragDelegate.css = {
        top: parseInt($image.css('top')),
        left: parseInt($image.css('left'))
    };

    dragDelegate.setPosition = function( p )
    {
        if ( typeof p.y !== 'undefined' )
        {
            this.css.top = p.y;
        }

        if ( typeof p.x !== 'undefined' )
        {
           this.css.left = p.x;
        }

        $image.css(this.css);
    };

    dragDelegate.start = function()
    {
        if ( !imageLoaded )
        {
            return false;
        }

        var pos = $image.position();
        this.startPosition.y = pos.top;
        this.startPosition.x = pos.left;

        this.css = {
            top: this.startPosition.y,
            left: this.startPosition.x
        };

        this.dimension.parentHeight = $cover.height();
        this.dimension.parentWidth = $cover.width();
        this.dimension.imageHeight = $image.height();
        this.dimension.imageWidth = $image.width();
    };

    dragDelegate.drag = function( e )
    {
        var top = this.startPosition.y + e.position.y;
        var left = this.startPosition.x + e.position.x;
        var bottom = -(this.dimension.imageHeight - (this.dimension.parentHeight + (-top)));
        var right = -(this.dimension.imageWidth - (this.dimension.parentWidth + (-left)));

        top = top >= 0 ? 0 : top;
        left = left >= 0 ? 0 : left;

        var p = {};

        if ( bottom < 0 )
        {
            p.y = top;
        }

        if ( right < 0 )
        {
            p.x = left;
        }

        this.setPosition(p);
    };

    dragDelegate.end = function( e )
    {
        coverData.position.top = this.css.top;
        coverData.position.left = this.css.left;
    };

    UHEADER.bindDrag($overlay, dragDelegate);

    // Simple DOM binds

    $('#uh-reposition-cancel').click(function()
    {
        ajaxModel.query('cancelChanges', coverData);
    });


    $('#uh-reposition-save').click(function()
    {
        coverData.canvas.height = $cover.height();
        coverData.canvas.width = $cover.width();

        ajaxModel.query('saveCover', coverData);
    });
};


UHEADER.Header = function( options )
{
    var userId = options.userId;

    var $header = $('#uh-header');
    var headerMode = 'view'

    var switchMode = function( mode )
    {
        $header.removeClass('uh-mode-' + headerMode);
        headerMode = mode;
        $header.addClass('uh-mode-' + headerMode);
    };

    this.cover = new UHEADER.Cover(options.cover,
    {
        switchMode: function( mode )
        {
            if ( mode === 'view' )
            {
                switchMode('view');
            }
            else
            {
                switchMode('coverEdit');
            }
        }
    });
};



$(function(){

    var to = null;

    var clearTO = function()
    {
        if ( to )
        {
            window.clearTimeout(to);
        }
    };

    $('.uh-at-more-wrap').hover(function(){
        clearTO();

        $(this).find(".uh-at-more-body").stop(true, true).show().animate({"margin-top": 0, opacity: 1}, "fast");
        //$(this).find('.ow_context_action').addClass('active');
    },
    function(){
        clearTO();
        var self = $(this);

        to = window.setTimeout(function()
        {
            self.find(".uh-at-more-body").stop(true, true).animate({"margin-top": -10, opacity: 0}, "fast", function()
            {
                $(this).hide();
            });

            //self.find('.ow_context_action').removeClass('active');
        }, 200);
    });

});


UHEADER.CoverImage = (function()
{
    var launcher;

    var setLauncher = function(l)
    {
        launcher = l;
    };

    var getLauncher = function(l)
    {
        return launcher;
    };

    var show = function()
    {
        launcher.show();
    };

    return {
        setLauncher: setLauncher,
        getLauncher: getLauncher,
        show: show
    };
})();

UHEADER.CoverImage.nativeLauncher = function( userId, cover )
{
    this.show = function()
    {
        OW.ajaxFloatBox('UHEADER_CMP_CoverView', [userId], {
            layout: 'empty'
        });
    };
};

UHEADER.CoverImage.photoLauncher = function( cover )
{
    this.show = function()
    {

    };
};


UHEADER.PhotoSelector = function( options, gallery )
{
    var isListFull = options.listFull;
    var $list = $('#uhps-list');
    var $listContent = $('#uhps-list-content');

    var $preloader = $('#uhps-preloader');
    if ( isListFull )
    {
        $preloader.hide();
    }

    var offset = function() {
        return $listContent.find('.uh-photo').length;
    };

    var ajaxModel, ajaxDelegate = {};

    ajaxDelegate.ajaxStart = function( command )
    {
        $preloader.css('visibility', 'visible');
    };

    ajaxDelegate.ajaxSuccess = function( command, response )
    {
        offset = response.offset;

        if ( response.listFull )
        {
            isListFull = true;
            $preloader.hide();
        }

        if ( response.list )
        {
            $listContent.append(response.list);
        }

        OW.addScroll($list);
    };

    ajaxDelegate.ajaxEnd = function( command )
    {
        $preloader.css('visibility', 'hidden');
    };

    ajaxModel = new UHEADER.CORE.AjaxModel(options.responder, ajaxDelegate);

    $('#uhps-cancel').click(function() {
        gallery.close();
    });

    $(document).off('click.uhps');
    $(document).on('click.uhps', '.uh-photo', function() {
        var photo = $(this);

        gallery.delegate.selectPhoto(photo.data('id'));
        gallery.close();
    });

    $list.on('jsp-arrow-change', function( event, isAtTop, isAtBottom, isAtLeft, isAtRight )
    {
        if ( isListFull )
        {
            return;
        }

        if ( isAtBottom )
        {
            ajaxModel.query('loadMorePhotos', {
                offset: offset()
            });
        }
    });

    this.tabAppearing = function() {
        //pass
    };
    
    this.tabAppeared = function() {
        OW.addScroll($list);
    };
};

UHEADER.Gallery = function( uniqId, settings ) {
    var self = this;
    
    this.currentTplId = null;
    this.current = null;
    
    this.cont = $("#" + uniqId);
    this.ajax = new UHEADER.CORE.AjaxModel(settings.rsp, this);
    
    this.$coverImage = this.cont.find(".uh-cover-image");
    this.$cover = this.cont.find("#uh-cover");
    this.$header = this.cont.find("#uh-header");
    
    this.$coverPreloader = this.cont.find(".uh-cover-preloader");
    this.$list = this.cont.find(".uh-template-list");
    
    this.imageTpl = this.$coverImage.clone();
    this.imageTpl.removeAttr("style");
    
    this.refreshScroll();
    
    if ( settings.current ) {
        this.current = settings.current;
        this.currentTplId = settings.current.id;
        this.selectTemplate(this.currentTplId);
    }
        
    this.cont.find(".uh-template").click(function() {
        self.selectTemplate($(this).data("id"));

        return false;
    });
};

UHEADER.Gallery.prototype = {
    refreshScroll: function() {
        this.listScroll = OW.addScroll(this.$list);
    },
    
    ajaxStart: function( command ) {
        if ( command === "loadTemplate" ) {
            this.$coverPreloader.show();
        }
    },
            
    ajaxEnd: function( command ) {
        if ( command === "loadTemplate" ) {
            this.$coverPreloader.hide();
        }
    },
            
    ajaxSuccess: function (command, response) {
        if ( response.type === 'error' )
        {
            OW.error(response.error);
            return;
        }

        if ( response.message )
        {
            OW.info(response.message);
        }
        
        if ( response.error )
        {
            OW.error(response.error);
        }
        
        if ( response.warning )
        {
            OW.warning(response.warning);
        }
        
        if ( response.cover ) {
            this.drawCover(response.cover);
        }
        
        if ( command === "removeTemplate" ) {
            this.switchToNearestFromCurrent();
        }
    },
            
    switchToNearestFromCurrent: function() {
        var tplIdForDelete = this.currentTplId;
        var next = this.findTpl(this.currentTplId).next(".uh-template");
        var prev = this.findTpl(this.currentTplId).prev(".uh-template");
        
        if ( !next.length && !prev.length ) {
            this.switchToEmpty();
        } else {
            var switchTo = next.length ? next.data("id") : prev.data("id");
            this.selectTemplate(switchTo);
        }
        
        this.removeFromList(tplIdForDelete);
    },
            
    switchToEmpty: function() {
        this.cont.addClass("uh-template-empty");
        this.cont.find(".uh-template-nocovers").show();
        this.removeImage();
    },
            
    removeFromList: function( tplId ) {
        this.findTpl(tplId).remove();
        this.refreshScroll();
    },
            
    findTpl: function( id ) {
        return this.$list.find("[data-id=" + id + "]");
    },
    
    unSelectTemplate: function( tplId ) {
        this.currentTplId = 0;
        var tpl = this.findTpl(tplId);
        tpl.find(".uh-template-selector").hide();
    },
    
    selectTemplate: function( tplId ) {
        var listHeight = this.$list.height();
        var scrollPos = this.listScroll.getContentPositionY();
        var tpl = this.findTpl(tplId);
        var pos = tpl.position();
        var height = tpl.height();
        var selector = tpl.find(".uh-template-selector");

        if ( this.currentTplId !== tplId ) {
            this.ajax.query("loadTemplate", {"id": tplId});
            this.unSelectTemplate(this.currentTplId);
            this.currentTplId = tplId;
            
            this.pushHistory(tplId, tpl.attr("href"));
        }
        
        selector.css("top", height / 2 - selector.height() / 2);
        selector.show();
        
        if ( scrollPos + listHeight < pos.top + height ) {
            this.listScroll.scrollToY(pos.top + height - listHeight, true);
        }
        
        if ( scrollPos > pos.top ) {
            this.listScroll.scrollToY(pos.top, true);
        }
    },
            
    pushHistory: function( tplId, url ) {
        // Skip. Should be overwritten
    },
            
    removeImage: function() {
        this.$coverImage.hide();
    },
            
    drawCover: function( data ) {
        var newImage = this.imageTpl.clone();
        
        newImage.attr("src", data.src);
        this.$coverImage.replaceWith(newImage);
        this.$coverImage = newImage;
        
        if (data.css) {
            this.$coverImage.css(data.css);
        }
        
        this.cont.find(".uh-template-info-users").text(data.users);
        
        if ( data.canvas && data.canvas.height ) {
            this.$cover.height(data.canvas.height);
        }
    }
};



UHEADER.AdminGallery = function( uniqId, settings ) {
    UHEADER.Gallery.call(this, uniqId, settings);
    
    var self = this;
    
    this.ajaxStart = function (command) {
        UHEADER.Gallery.prototype.ajaxStart.call(this, command);
        
        if ( command === "saveInfoLines" ) {
            $(".uh-template-lines-saving").show();
        }
        
        if ( command === 'saveInfo' )
        {
            $(".uh-template-info-saving").show();
        }
    };
    
    this.ajaxSuccess = function (command, response) {
        UHEADER.Gallery.prototype.ajaxSuccess.call(this, command, response);
        
        if ( response.infoLines ) {
            $.each(response.infoLines, function(line, label) {
                self.cover.setInfoLine(line, label);
            });
        }
    };
    
    this.ajaxEnd = function (command) {
        UHEADER.Gallery.prototype.ajaxEnd.call(this, command);
        
        if ( command === "saveInfoLines" ) {
            $(".uh-template-lines-saving").hide();
        }
        
        if ( command === 'saveInfo' )
        {
            $(".uh-template-info-saving").hide();
        }
    };
    
    this.cover = new UHEADER.AdminCoverTemplate(this, {
        "responder": settings.rsp
    });
    
    this.initInfoLines();
};

$.extend(UHEADER.AdminGallery.prototype, UHEADER.Gallery.prototype, {
    initInfoLines: function() {
        var self = this;
        
        var lineConfiguration = function( line ) {
            var out = {};
            out.key = line.val();

            if (out.key == "base-question") {
                out.question = line.parent().find(".uh-template-info-question-c select").val();
            }

            return out;
        };

        var infoConfiguration = function() {
            var out = {};
            out.line1 = lineConfiguration($("#line1_id"));
            out.line2 = lineConfiguration($("#line2_id"));

            return out;
        };

        var saveInfoTO = 0;
        var saveInfo = function() {
            if ( saveInfoTO ) window.clearTimeout(saveInfoTO);
            
            window.setTimeout(function() {
                var info = infoConfiguration();
                self.ajax.query("saveInfoLines", info);
            }, 100);
        };

        $("#line1_id, #line2_id").change(function() {
            if ( $(this).val() === "base-question" ) {
                $(this).parent().find(".uh-template-info-question-c").show();
            } else {
                $(this).parent().find(".uh-template-info-question-c").hide();

                saveInfo();
            }
        });

        $(".uh-template-info-question-c select").change(saveInfo);
    },
    
    drawCover: function( data ) {
        this.cover.setCover(data);
        
        var tpl = this.findTpl(data.id);
        if (data.previewCss) {
            tpl.find("img").css(data.previewCss);
        }
        
        tpl.find(".uh-template-flag-default")[data["default"] ? "show" : "hide"]();
    },
            
    pushHistory: function( tplId, url ) {
        if ( history && history.replaceState ) {
            history.replaceState({tplId: tplId}, null, url);
        }
    },
    
    removeImage: function() {
        this.cover.unsetImage();
    }
});



UHEADER.AdminCoverMenu = function(delegate)
{
    $('#uhco-reposition').click(function()
    {
        delegate.reposition();
    });

    $('#uhco-remove').click(function()
    {
        delegate.remove();
    });
};


UHEADER.AdminCoverTemplate = function( gallery, options )
{
    var self = this;
    
    var $header = $('#uh-header');
    var $image = $('#uh-cover-image');
    var $cover = $('#uh-cover');
    var $coverWrap = $('#uh-cover-wrap');
    var $info = $(".uh-template-info-table");
    var $overlay = $('#uh-cover-overlay');
    var $conextMenu = $('.uh-cover-add-btn-wrap', '#uhc-controls');
    var coverData = {};
    
    var ajaxModel = gallery.ajax;
    
    coverData.tplId = gallery.currentTplId;
    coverData.info = {
        "default": gallery.current ? gallery.current["default"] : false,
        "roles": gallery.current ? gallery.current["roles"] : []
    };

    coverData.position = {
        top: 0,
        left: 0
    };
    
    coverData.canvas = {
        height: $cover.height(),
        width: $cover.width()
    };

    var coverMode = 'view';
    var switchMode = function( mode )
    {
       $coverWrap.removeClass('uh-cover-mode-' + coverMode);
       coverMode = mode;
       $coverWrap.addClass('uh-cover-mode-' + coverMode);
    };
    
    var setImage = function( src )
    {
        if ( $image.attr('src') === src )
        {
            return;
        }

        var tmpImage = $image.clone();
        tmpImage.attr('src', src);
        $image.replaceWith(tmpImage);

        $image = tmpImage;
        $image.show();
    };
    
    var saveInfo = function()
    {
        coverData.info["roles"] = [];
        $info.find(".uh-template-info-role:checked").each(function() {
            coverData.info["roles"].push(this.value);
        });
        
        coverData.info["default"] = $info.find(".uh-template-info-default").get(0).checked;
        ajaxModel.query('saveInfo', coverData);
    };
    
      
    // Ajax Model

    ajaxModel.delegate._ajaxStart = ajaxModel.delegate.ajaxStart;
    ajaxModel.delegate.ajaxStart = function( command )
    {
        ajaxModel.delegate._ajaxStart(command);
        
        if ( command === 'cancelReposition' || command === "saveReposition" )
        {
            $('#uhc-controls').addClass('ow_preloader').addClass('uh-cover-controls-loading');
        }
    };

    ajaxModel.delegate._ajaxEnd = ajaxModel.delegate.ajaxEnd;
    ajaxModel.delegate.ajaxEnd = function( command )
    {
        ajaxModel.delegate._ajaxEnd(command);
        
        if ( command === 'cancelReposition' || command === "saveReposition" )
        {
            $('#uhc-controls').removeClass('ow_preloader').removeClass('uh-cover-controls-loading');
        }
    };
    
    // Toolbar

    var toolbarDelegate = {};

    toolbarDelegate.reposition = function()
    {
        switchMode('reposition');
    };

    toolbarDelegate.remove = function()
    {
        if ( !confirm(OW.getLanguageText('uheader', 'admin_delete_cover_confirmation')) )
        {
            return false;
        }
        
        ajaxModel.query('removeTemplate', coverData);
    };

    UHEADER.AdminCoverMenu(toolbarDelegate);


    var dragDelegate = {};

    dragDelegate.startPosition = {},
    dragDelegate.dimension = {},
    dragDelegate.direction = 'all',

    dragDelegate.css = {
        top: parseInt($image.css('top')),
        left: parseInt($image.css('left'))
    };

    dragDelegate.setPosition = function( p )
    {
        if ( typeof p.y !== 'undefined' )
        {
            this.css.top = p.y;
        }

        if ( typeof p.x !== 'undefined' )
        {
           this.css.left = p.x;
        }
        
        $image.css(this.css);
    };

    dragDelegate.start = function()
    {
        this.startPosition.y = parseInt($image.css('top'));
        this.startPosition.x = parseInt($image.css('left'));

        this.css = {
            top: this.startPosition.y,
            left: this.startPosition.x
        };

        this.dimension.parentHeight = $cover.height();
        this.dimension.parentWidth = $cover.width();
        this.dimension.imageHeight = $image.height();
        this.dimension.imageWidth = $image.width();
    };

    dragDelegate.drag = function( e )
    {
        var top = this.startPosition.y + e.position.y;
        var left = this.startPosition.x + e.position.x;
        var bottom = -(this.dimension.imageHeight - (this.dimension.parentHeight + (-top)));
        var right = -(this.dimension.imageWidth - (this.dimension.parentWidth + (-left)));

        top = top >= 0 ? 0 : top;
        left = left >= 0 ? 0 : left;

        var p = {};

        if ( bottom < 0 )
        {
            p.y = top;
        }

        if ( right < 0 )
        {
            p.x = left;
        }

        this.setPosition(p);
    };

    dragDelegate.end = function( e )
    {
        coverData.position.top = this.css.top;
        coverData.position.left = this.css.left;
    };

    UHEADER.bindDrag($overlay, dragDelegate);
    
    // Public methods
    this.setCover = function( cover ) {
        coverData.tplId = gallery.currentTplId;
        
        if ( cover.src ) {
            setImage(cover.src);
        }
        
        if ( cover.data )
        {
            coverData.position.top = cover.data.position.top;
            coverData.position.left = cover.data.position.left;

            $image.css(coverData.position);

            if ( cover.data.css )
            {
                coverData.css = cover.data.css;
                $image.css(coverData.css);
            }
        }

        $info.find(".uh-template-info-role").attr("checked", false);
        if ( cover.roles ) {
            $.each(cover.roles, function(i, roleId) {
                $info.find(".uh-template-info-role[value=" + roleId + "]").attr("checked", true);
            });
        }
        
        $cover.find(".uh-template-flag-default")[cover["default"] ? "show" : "hide"]();
        $info.find(".uh-template-info-default").attr("checked", cover["default"]);
        
        $info.find(".uh-template-info-users").text(cover.users);
        
        if ( cover.canvas && cover.canvas.height ) {
            $cover.height(cover.canvas.height);
            coverData.canvas.height = cover.canvas.height;
        }
        
        switchMode('view');
    };
    
    this.unsetImage = function()
    {
        $image.removeAttr("style");
        $image.attr("src", "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7");
        $coverWrap.removeClass('uh-cover-has-cover');
        $coverWrap.addClass('uh-cover-no-cover');

        $conextMenu.removeClass('ow_photo_context_action'); // Design hack
    };
    
    this.setInfoLine = function( line, text )
    {
        var line = $coverWrap.find(".uh-info-line[data-line=" + line + "]");
        line.html("<span>" + text + "</span>");
        line[text ? "show" : "hide"]();
    };
    
    this.switchMode = switchMode;

    // Simple DOM binds

    var saveInfoTO = 0;
    $info.find(".uh-template-info-default, .uh-template-info-role").click(function() {
        if ( saveInfoTO ) window.clearTimeout(saveInfoTO);
        saveInfoTO = window.setTimeout(saveInfo, 300);
    });

    $('#uh-reposition-cancel').click(function()
    {
        ajaxModel.query('cancelReposition', coverData);
    });


    $('#uh-reposition-save').click(function()
    {
        coverData.canvas.height = $cover.height();
        coverData.canvas.width = $cover.width();

        ajaxModel.query('saveReposition', coverData);
    });
};

UHEADER.GallerySwitcher = (function() {
    
    var node, tabs, contents;
    var activeKey, options, delegate, fb, tabObjects = {};
    
    function init( uniqId, opts ) {
        options = opts;
        node = $("#" + uniqId);
        tabs = $(".uhg-tab", node);
        contents = $(".uhg-tab-content", node);
         activeKey = $(".uhg-active-tab", node).data("key");
        
        tabs.click(function() {
            switchTab($(this).data("key"));
        });
        
        node.find(".uhg-switch-view-mode").click(function() {
            switchMode($(this).data("mode"));
        });
        
        prepareTab(activeKey);
        notifyTab(activeKey, "tabAppeared");
        
        node.find("[data-control]").click(function() {
            var control = $(this).data("control");
            if ( notifyTab(activeKey, "tabControlPressed", [control]) !== false ) {
                controlPressed(control);
            }
        });
    }
    
    function prepareTab( key ) {
        var settings = notifyTab(key, "tabAppearing") || {};
        switchMode(settings.modeSwitcher || false);
        showControls(settings.controls || []);
    }
    
    function switchTab( key ) {
        if ( activeKey === key ) return;
        activeKey = key;
        var tab = tabs.filter("[data-key=" + key + "]");
        tabs.removeClass("uhg-active-tab").removeClass("ow_remark");
        tab.addClass("uhg-active-tab").addClass("ow_remark");
        
        switchContent(key);
    }
    
    function switchContent( key ) {
        contents.stop(true, true).hide();
        prepareTab(key);
        contents.filter("[data-key=" + key + "]").fadeIn("fast", function() {
            notifyTab(key, "tabAppeared");
        });
    }
    
    function showControls( controls ) {
        controls.push("close");
        
        node.find("[data-control]").hide();
        $.each(controls, function(index, control) {
            node.find("[data-control="+ control +"]").show();
        });
    }
    
    function show( userId, opts ) {
        var scope = {
            "delegate": delegate,
            "close": close
        };
        
        fb = OW.ajaxFloatBox("UHEADER_CMP_Gallery", [userId, opts], {
            "layout": "empty",
            "scope": scope
        });
    }
    
    function close() {
        if ( !fb ) return;
        fb.close();
    }
    
    function notifyTab( tabKey, method, params ) {
        if ( tabObjects[tabKey] && tabObjects[tabKey][method] ) {
            return tabObjects[tabKey][method].apply(tabObjects[tabKey], params || []);
        }
        
        return null;
    }
    
    function controlPressed( control ) {
        if ( control === "close" ) {
            close();
        }
    }
    
    var modeSwitcherBusy = false;
    function switchMode( mode ) {
        if ( modeSwitcherBusy ) return;
        
        var sw = node.find(".uhg-mode-switcher");
        if (!mode) {
            sw.hide();
            return;
        }
        
        sw.show();

        if ( $(".uhg-selected-mode", node).data("mode") === mode ) {
            return;
        }
        
        sw.find(".uhg-switch-view-mode").removeClass("uhg-selected-mode");
        sw.find('[data-mode="' + mode + '"]').addClass("uhg-selected-mode");

        modeSwitcherBusy = true;
        notifyTab(activeKey, "tabModeChanged", [mode, contents.filter("[data-key=" + activeKey + "]"), function() {
            modeSwitcherBusy = false;
        }]);
    }
    
    return {
        init: init,
        show: show,
        close: close,
        registerTab: function( tabKey, tab ) {
            tabObjects[tabKey] = tab;
            
            if ( activeKey === tabKey ) {
                prepareTab(tabKey);
                notifyTab(activeKey, "tabAppeared");
            }
        },
        
        setDelegate: function( del ) {
            delegate = del;
        }
    };
})();


UHEADER.TemplateGallery = function( options, gallery ) {
    
    var $list = $('#uhg-tpl-list');
    
    $(document).off("click.uhgt");
    $(document).on("click.uhgt", ".uhg-tpl-item-wrap", function() {
        gallery.delegate.selectTemplate($(this).data("id"));
        gallery.close();
    });
    
    this.tabModeChanged = function( mode, cont, done ) {
        if ( mode === "list" ) return done();
        
        cont.addClass("ow_preloader_content");
        cont.empty();
        OW.loadComponent("UHEADER_CMP_CoverPreviewGallery", [options.userId, options.tabKey, options.dimensions], {
            "scope": gallery,
            "onReady": function( html ) {
                cont.removeClass("ow_preloader_content");
                html.hide();
                cont.html(html);
                html.fadeIn("fast");
                done();
            }
        });
    };
    
    this.tabAppearing = function() {
        return {
            modeSwitcher: "list"
        };
    };
    
    this.tabAppeared = function() {
        OW.addScroll($list);
    };
};

UHEADER.TemplatePreviewGallery = function( options, gallery ) {
    UHEADER.Gallery.call(this, options.uniqId, options.settings);
    
    this.tabModeChanged = function( mode, cont, done ) {
        if ( mode === "preview" ) return done();
        
        cont.addClass("ow_preloader_content");
        cont.empty();
        OW.loadComponent("UHEADER_CMP_CoverGallery", [options.userId, options.tabKey, options.dimensions], {
            "scope": gallery,
            "onReady": function( html ) {
                cont.removeClass("ow_preloader_content");
                html.hide();
                cont.html(html);
                html.fadeIn("fast");
                done();
            }
        });
    };
    
    this.tabControlPressed = function( control ) {
        if ( control === "save" ) {
            gallery.delegate.selectTemplate(this.currentTplId, false);
            gallery.close();
        }
    };
    
    this.tabAppearing = function() {
        return {
            "modeSwitcher": "preview",
            "controls": ["save"] 
        };
    };
    
    this.tabAppeared = function() {
        //pass
    };
};

UHEADER.TemplatePreviewGallery.prototype = UHEADER.Gallery.prototype;