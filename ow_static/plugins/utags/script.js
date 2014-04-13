/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.static
 */

UTAGS = window.UTAGS || {};

function UTAGS_init( uniqId, _options ) {
    var DEBUG_MODE = false;
    var DEFAULT_SELECTION_WIDTH = 100;
    var DEFAULT_SELECTION_HEIGHT = 100;
    var TAG_BORDER_SIZE = 4;
    
    var _tpls = $("#" + uniqId);
    var photoList = {}, currentPhoto = null, waitingTagIds = [], _permissions = _options.permissions;
    
    var _ajax, _mouseOffset, _callbackProxy;
    
    _callbackProxy = function( fnc, frequency ) {
        var args, self, busy = false;
        
        var hold = function() {
            if ( busy ) return;
            busy = true;
            window.setTimeout(function () {
                fnc.apply(self, args);
                busy = false;
            }, frequency);
        };
        
        return function() {
            self = this;
            args = arguments;
            hold();
        };
    };
    
    _ajax = function( command, params, callback ) {
        return $.getJSON(_options.rsp, {
            command: command,
            params: JSON.stringify(params)
        }, function( r ) {
           if ( callback ) {
               callback(r);
           } 
        }).error(function( e ) {
            if ( DEBUG_MODE ) {
                OW.error(e.responseText);
                console.error(e.responseText);
            }
        });
    };
    
    _mouseOffset = function( e ) {
        var target = e.target || e.srcElement,
        rect = target.getBoundingClientRect();

        var offset = {};
        offset.x = e.clientX - rect.left,
        offset.y = e.clientY - rect.top;

        return offset;
    };
    
    var Photo, Tag, Selection;
    
    
    Tag = function( options, photo ) {
        var self = this;
        
        this.view = _tpls.find("[data-tpl=tag]").clone().removeAttr("data-tpl");
        this.tooltip = _tpls.find("[data-tpl=tag-tooltip]").clone().removeAttr("data-tpl");
        this.photo = photo;
        this.area = options.area;
        
        this.tooltip.appendTo(document.body).hide();
        
        this.view.hide();
        
        this.shown = false;
        
        this.top = options.top || 0;
        this.left = options.left || 0;
        this.width = options.width || 0;
        this.height = options.height || 0;
        this.data = options.data || {};
        
        this.removable = options.remove === false ? false : true;
        
        this.forceShow = false;
        this.forceShowTimeout;
        
        this.id = options.id || null;
        this.key = this.data.id;
        
        if ( this.data.id ) {
            this.type = this.data.id.split("_")[0];
        }
        
        this.cid = this.__generateCID(this.type);
        
        this.render();
    };
    
    Tag.prototype = {
        __cidCounter: 0,
        __generateCID: function( prefix ) {
            Tag.prototype.__cidCounter++;
            
            return prefix + Tag.prototype.__cidCounter;
        },
        
        isNew: function() {
            return this.id === null;
        },
        
        onSave: function( isNew ) {
            
        },
        
        render: function() {
            var info = this.tooltip.find(".ut-tag-info");
            var deleteBtn = this.tooltip.find("[data-action=delete]");
            deleteBtn.attr("data-tagcid", this.cid);
            
            deleteBtn[this.removable ? "show" : "hide"]();
            info[this.removable ? "addClass" : "removeClass"]("ut-removable-tag");
            
            if ( this.data.url ) {
                info.html('<a class="hint-no-hint" href="' + this.data.url + '">' + this.data.text + '</a>');
            } else {
                info.html('<span>' + this.data.text + '</span>');
            }
        },
        
        show: function( duration, visible ) {
            var self = this;
            
            duration = duration || 0;
            this.forceShow = true;
            
            this.view.css(this.getPosition());
            this.view.find(".ut-tag").css(this.getSize());
            this.view.css("visibility", visible === false ? "hidden" : "visible");
            this.view.show();
            this.showInfo();
            
            if ( this.forceShowTimeout )
                window.clearTimeout(this.forceShowTimeout);

            if ( duration !== true ) {
                this.forceShowTimeout = window.setTimeout(function() {
                    self.forceShow = false;
                }, duration);
            }
            
            if ( this.photo.floatBox ) {
                this.photo.floatBox.$canvas.off("scroll.tag").on("scroll.tag", function() {
                    if (self.shown)
                        self.showInfo();
                });
            }
            
            this.shown = true;
        },
                
        hide: function( forceStop ) {
            if ( this.forceShow && !forceStop ) return;
    
            this.view.hide();
            this.hideInfo();
            
            this.shown = false;
        },
                
        remove: function() {
            this.view.remove();
            this.tooltip.remove();
        },
                
        showInfo: function() {
            var width, center, left, pos = this.view.offset(), size = this.getSize();
            this.tooltip.show().css("visibility", "hidden");
            width = this.tooltip.width();
            center = pos.left + size.width / 2;
            left = center - width / 2;
            
            this.tooltip.css({
                left: left + TAG_BORDER_SIZE,
                top: pos.top + size.height + TAG_BORDER_SIZE * 2
            });
            
            this.tooltip.css("visibility", "visible");
        },
                
        hideInfo: function() {
            this.tooltip.hide();
        },
                
        getScaleWidth: function() {
            return this.photo.area.width / this.area.width;
        },
                
        getScaleHeight: function() {
            return this.photo.area.height / this.area.height;
        },
        
        getPosition: function() {
            return {
                top: (this.top * this.getScaleHeight()) + this.photo.area.top,
                left: (this.left * this.getScaleWidth()) + this.photo.area.left
            };
        },
                
        getSize: function() {
            return {
                width: this.width * this.getScaleWidth(),
                height: this.height * this.getScaleHeight()
            };
        },
                
        toJSON: function() {
            return {
                area: this.area,
                width: this.width,
                height: this.height,
                top: this.top,
                left: this.left,
                data: this.data,
                id: this.id,
                cid: this.cid,
                type: this.type
            };
        }
    };
    
    
    Selection = function( node, photo ) {
        var self = this;
        
        this.photo = photo;
        this.area = this.photo.area;
        this.select2 = null;
        this.sel = _tpls.find("[data-tpl=selection]").clone().removeAttr("data-tpl");
        this.tags = _tpls.find("[data-tpl=tags]").clone().removeAttr("data-tpl");
        
        this.node = node;
        this.bounds = {
            top: this.area.top,
            left: this.area.left,
            right: this.area.left + this.area.width,
            bottom: this.area.top + this.area.height
        };
        
        this.overlay = this.node.find(".ut-selection-overlay");
        
        this.sel.appendTo(node).hide();
        this.tags.appendTo(document.body).hide();
        this.kw = "";
        this.tag = null;
        this.resizing = false;
        
        
        this.setPosition(this.area.left, this.area.top);
        this.setSize(DEFAULT_SELECTION_WIDTH, DEFAULT_SELECTION_HEIGHT)
        
        this.pressed = false;
        this.inputShown = false;
        this.shown = false;
        
        this.initResize();
        this.initMove();
        
        if ( this.photo.floatBox ) {
            this.photo.floatBox.$canvas.scroll(function() {
                self.refreshInputPosition();
                
                if ( self.shown && self.select2 ) {
                    self.select2.close();
                    self.select2.open();
                }
            });
        }
    };
    
    Selection.prototype = {
        _checkBounds: function (x, y) {
            return !(x < this.bounds.left || x > this.bounds.right 
                        || y < this.bounds.top || y > this.bounds.bottom);
        },
        
        onChange: function() {},
        
        initMove: function() {
            var self = this;
            var startX, startY, startTop, startLeft;
            
            var nodeSize = {
                width: this.node.width(),
                height: this.node.height()
            };
            
            var selectionSize = nodeSize.width > nodeSize.height 
                ? nodeSize.height / 5
                : nodeSize.width / 5;
                
            selectionSize = selectionSize > 100 ? 100 : selectionSize;
        
            var mouseDown = function( e ) {
                self.hideInput();
                self.pressed = true;
                
                if ( self.resizing ) return;
                
                startX = e.pageX;
                startY = e.pageY;
                startTop = self.top + self.height / 2;
                startLeft = self.left + self.width / 2;
            };

            this.overlay.on("mousedown.selection", function( e ) {
                
                if ( !_permissions.isModerator && _permissions.credits.actions["tag_photo"] === false ) {
                    OW.warning(_permissions.credits.messages["tag_photo"]);
                    return false;
                };
                
                if ( !self.photo.active ) return;
                
                var offset = _mouseOffset(e);
                
                if ( !self._checkBounds(offset.x, offset.y) )
                    return;
                
                self.kw = "";

                self.show({
                    left: offset.x,
                    top: offset.y,
                    width: selectionSize,
                    height: selectionSize
                });
                mouseDown.call(this, e);
            });

            this.sel.mousedown(mouseDown);

            $(document.body).on("mousedown.selection", function( e ) {
                if ( !self.photo.active || self.resizing ) return;
                if ( $(e.target).is(".ut-selection-tag-wrap *") ) return;

                if ( !self.pressed && self.shown ) {
                    self.hide();
                }
            });

            $(document.body).on("mouseup.selection", function( e ) {
                if ( !self.photo.active || !self.pressed ) return;
                
                self.pressed = false;
                self.showInput();
            });

            var moveProxy = _callbackProxy(function( e ) {
                self.setPosition(startLeft - (startX - e.pageX), startTop - (startY - e.pageY));
            }, 20);

            $(document.body).on("mousemove.selection", function( e ) {
                if ( self.photo.active && self.pressed && !self.resizing )
                    moveProxy(e);
            });

            $(document.body).on("selectstart.selection", function( e ) {
                if ( !self.photo.active || self.pressed || this.resizing ) e.preventDefault();
            });
        },
        
        initResize: function() {
            var self = this;
            var startX, startY, startWidth, startHeight;
            
            var resize = this.sel.find(".uts-resize");
            resize.mousedown(function( e ) {
                self.resizing = true;
                startX = e.pageX;
                startY = e.pageY;
                startWidth = self.width;
                startHeight = self.height;
                
                self.overlay.addClass("ut_cursor_resize");
                self.sel.addClass("ut_cursor_resize");
            });
            
            $(document.body).on("mouseup.selection", function( e ) {
                if ( !self.resizing ) return;
                
                self.resizing = false;
                self.overlay.removeClass("ut_cursor_resize");
                self.sel.removeClass("ut_cursor_resize");
            });
            
            var moveProxy = _callbackProxy(function( e ) {
                var width = startWidth - ( startX - e.pageX ) * 2;
                var height = startHeight + ( startY - e.pageY ) * 2;
                var left = e.pageX - overlayOffset.left - width;
                var top = e.pageY - overlayOffset.top;
                
                if ( left < self.bounds.left || left + width > self.bounds.right )
                    width = self.width;
                
                if ( top < self.bounds.top || top + height > self.bounds.bottom )
                    height = self.height;
                
                self.setSize(width, height);
            }, 10);
            
            
            var overlayOffset = this.overlay.offset();
            
            $(document.body).on("mousemove.selection", function( e ) {
                if ( !self.resizing ) return;
                
                moveProxy(e);
                e.stopPropagation();
                e.preventDefault();
            });
        },
        
        show: function( options ) {
            this.setSize(options.width, options.height);
            this.setPosition(options.left, options.top);
            
            this.sel.show();
            this.shown = true;
        },
                
        hide: function() {
            this.sel.hide();
            this.hideInput();
            
            this.shown = false;
        },
               
        initInput: function() {
            var self = this, input = this.tags.find(".uts-tag-input");
            
            _options.input.settings.contextId = this.photo.photoId;
            this.select2 = UTAGS.userSelector.init(input, _options.input.settings, _options.input.options, _options.input.data);
            
            input.on("open", function() { 
                self.tags.addClass("uts-open");
            });
            
            input.on("close", function() { 
                self.tags.removeClass("uts-open");
            });
            
            input.on("change", function() { 
                var data = input.select2("data");
                var tagData = data[0];
                
                var tag = new Tag({
                    top: self.top - self.bounds.top,
                    left: self.left - self.bounds.left,
                    width: self.width,
                    height: self.height,
                    data: {
                        text: tagData.text,
                        id: tagData.id,
                        url: tagData.url || null
                    },
                    area: self.area
                }, self.photo);
                
                this.kw = "";
                
                input.select2("val", "");
                self.hide();
                
                self.onChange(tag);
            });
            
            this.select2.search.on("keydown", function() {
                window.setTimeout(function() {
                    self.kw = self.select2.search.val();
                });
            });
        },
           
        refreshInputPosition: function( force ) {
            if ( !force && !this.inputShown ) return;
            
            var width, center, left, pos = this.sel.offset();
            width = this.tags.width();
            center = pos.left + this.width / 2;
            left = center - width / 2;
            
            this.tags.css({
                left: left,
                top: pos.top + this.height
            });
        },
                
        showInput: function() {
            if ( this.pressed ) return;
    
            this.refreshInputPosition(true);
            var self = this;
            
            //this.tags.show();
            this.tags.fadeIn(50, function() {
                self.inputShown = true;
            });
            
            if ( !this.select2 )
                this.initInput();
            
            this.select2.search.val(this.kw);
            this.select2.open();
        },
                
        hideInput: function() {
            this.tags.hide();
            this.inputShown = false;
            /*
            this.tags.fadeOut(50, function() {
                self.inputShown = false;
            });
            */
        },
                
                
        setSize: function( width, height ) {
            var widthDiff,
                heightDiff,
                left, top;
    
            height = height < 30 ? 30 : height;
            height = height > this.area.height ? this.area.height : height;
            heightDiff = this.height - height;
            this.height = height;
    
            width = width < 30 ? 30 : width;
            width = width > this.area.width ? this.area.width : width;
            widthDiff = this.width - width;
            this.width = width;
            
            this.sel.css({
                width: width,
                height: height
            });
            
            if ( isNaN(widthDiff) )
                widthDiff = 0;
            
            if ( isNaN(heightDiff) )
                heightDiff = 0;
            
            left = (this.left + this.width / 2) + widthDiff / 2;
            top = (this.top + this.height / 2) + heightDiff / 2;
            
            this.setPosition(left, top);
        },
                
        setPosition: function( x, y ) {
            var top = y - this.height / 2,
                left = x - this.width / 2;
            
            top = top >= this.bounds.top ? top : this.bounds.top;
            top = top + this.height <= this.bounds.bottom ? top : this.bounds.bottom - this.height;
            this.top = top;
            
            left = left >= this.bounds.left ? left : this.bounds.left;
            left = left + this.width <= this.bounds.right ? left : this.bounds.right - this.width;
            this.left = left;
            
            
            this.sel.css({
                left: left,
                top: top
            });
            
            this.refreshInputPosition();
        },
                
        destroy: function() {
            $(document.body).off(".selection");
            this.overlay.off(".selection");
            this.sel.remove();
            this.tags.remove();
            
            if ( this.select2 )
                this.select2.destroy();
        }
    };
    
    
    Photo = function( photoId ) {
        var self = this;
        
        this.taggingControls = _tpls.find("[data-tpl=tagging-controls]").clone().removeAttr("data-tpl");

        this.active = false;
        this.photoId = photoId;
        this.tags = {};
        
        
        this.selection = null;
        this.tagging = false;
        this.fetched = false;
        
        this.afterFetch = function() {};
        this.afterActivate = function() {};
        this.afterDeactivate = function() {};
        
        var showWaitingTags = function() {
            if ( !waitingTagIds.length ) return;
            $.each( self.tags, function(cid, tag) {
                if ( $.inArray(parseInt(tag.id), waitingTagIds) >= 0 ) {
                    tag.show(2000);
                }
            });
            
            waitingTagIds = [];
        };
        
        _ajax("fetchTags", {
           photoId: photoId
        }, function( r ) {
            $.each(r.tags, function(i, tag) {
                self.addTag(new Tag(tag, self));
            });
            
            self.fetched = true;
            
            if ( self.active ) {
                self.renderTags();
                showWaitingTags();
                self._ajaxCallback(r);
            } else {
                this._afterActivate = function() {
                    showWaitingTags();
                    self._ajaxCallback(r);
                    this._afterActivate = null;
                };
            }
            
            if ( $.isFunction(self.afterFetch) ) self.afterFetch.call(self);
        });
    };
    
    Photo.prototype = {
        activate: function( cont, photoView ) {
            var self = this;
            this.floatBox = window.OWActiveFloatBox ? window.OWActiveFloatBox : null;
            this.photoView = photoView;
            
            this.cont = cont;
            this.stage = this.$(".ow_photo_stage");
            this.holder = this.$(".ow_photo_holder");
            this.img = this.$(".ow_photo_img");
            this.stage.append('<div class="ut-selection-overlay">&nbsp;</div>');
            this.overlay = this.stage.find(".ut-selection-overlay");
            this.tagListWrap = this.cont.find(".ut-tag-list-wrap");
            this.tagList = this.tagListWrap.find(".ut-tag-list");
            
            
            this.customTagListContainer = $(".ut-custom-tags-wrap", this.cont);
                        
            this.area = {
                top: parseInt(this.img.css("top")),
                left: parseInt(this.img.css("left")),
                width: parseInt(this.img.css("width")),
                height: parseInt(this.img.css("height"))
            };
           
            var overlayOffset = this.overlay.offset();
           
            var moveProxy = _callbackProxy(function( e ) {
                var left = e.pageX - overlayOffset.left;
                var top = e.pageY - overlayOffset.top;
                
                if ( self.floatBox )
                    top += self.floatBox.$canvas.scrollTop();
                
                self._onMouseMove(left, top, e.target);
            }, 100);
           
            $(document.body).on("mousemove.photo", ".ow_photo_holder", function(e) {
                if ( self.active ) moveProxy(e);
            });
            
            
            
            this.customTagListContainer.on("mouseenter.photo", "a", function() {
                var tagText = $(this).text().toLowerCase();
                
                $.each(self.tags, function(cid, tag) {
                    if ( tag.data.text.toLowerCase() === tagText ) {
                        tag.show(true, true);
                    }
                });
            });
            
            this.customTagListContainer.on("mouseleave.photo", "a", function() {
                var tagText = $(this).text().toLowerCase();
                
                $.each(self.tags, function(cid, tag) {
                    if ( tag.shown && tag.data.text.toLowerCase() === tagText ) {
                        tag.hide(true);
                    }
                });
            });
            
            $(document.body).on("mouseenter.photo", "[data-tagid]:not([data-action])", function() {
                var tagId = $(this).data("tagid");
                
                $.each(self.tags, function(cid, tag) {
                    if ( tag.id == tagId || tag.cid == tagId ) {
                        tag.show(true, true);
                    }
                });
            });
            
            $(document.body).on("mouseleave.photo", "[data-tagid]:not([data-action])", function() {
                var tagId = $(this).data("tagid");
                
                $.each(self.tags, function(cid, tag) {
                    if ( tag.shown && (tag.id == tagId || tag.cid == tagId) ) {
                        tag.hide(true);
                    }
                });
            });
            
            $(document.body).on("click.photo", "[data-action=delete][data-tagcid], [data-action=delete][data-tagid]", function() {
                var selfNode = $(this);
                var cid = $(this).data("tagcid");
                var tagId = $(this).data("tagid");
                if ( !cid ) {
                    $.each(self.tags, function(cid, tag) {
                        if ( tag.id == tagId ) {
                            self.removeTag(tag);
                            selfNode.parents("[data-tagid]:eq(0)").remove();
                            if ( !self.tagList.children().length )
                                self.tagListWrap.hide();
                        }
                    });
                } else {
                    self.removeTag(self.tags[cid]);
                }   
            });
            
            this.taggingControls.appendTo(this.holder);
            
            this.renderTags();
            
            if ( this.floatBox ) {
                this.floatBox.bind("close", function() {
                    self.deactivate();
                });
            }
            
            this.active = true;

            if ( $.isFunction(this._afterActivate) ) this._afterActivate.call(this);
            if ( $.isFunction(this.afterActivate) ) this.afterActivate.call(this);
        },
                
        destroyPhotoCache: function() {
            if ( this.photoView && this.photoView.checkIsCached(this.photoId) ) {
                this.photoView.cache[this.photoId] = null;
            }
        },
                
        renderTags: function() {
            var self = this;
            
            $.each(this.tags, function( cid, tag ) {
                self.img.after(tag.view);
            });
        },
                
        showTagList: function( html ) {
            if (!this.tagListWrap || !this.tagList) return;
            
            if ( html ) {
                this.tagListWrap.show();
                this.tagList.html(html);
            }
            else {
                this.tagListWrap.hide();
            }
        },
                
        showCustomTagList: function( html ) {
            if (!this.customTagListContainer) return;
            if ( html === undefined ) return;
    
            if ( html ) {
                this.customTagListContainer.html(html);
                this.customTagListContainer.show();
            }
            else {
                this.customTagListContainer.empty();
                this.customTagListContainer.hide();
            }
        },
              
        _onMouseMove: function( x, y, target ) {
            $.each(this.tags, function(cid, tag) {
                var pos = tag.getPosition();
                var size = tag.getSize();
                if ( x >= pos.left && x <= pos.left + size.width
                        && y >= pos.top && y <= pos.top + size.height) {
                    if ( !tag.shown ) {
                        tag.show();
                    }
                } else if ( tag.shown && !$(target).is(".ut-tag-tooltip, .ut-tag-tooltip *") ) {
                    tag.hide();
                }
            });
        },
                
        deactivate: function() {
            this.stopTagging();
            this.active = false;
            $(document.body).off(".photo");
            
            $.each(this.tags, function(cid, tag) {
                tag.hide(true);
            });
            
            if ( $.isFunction(this.afterDeactivate) ) this.afterDeactivate.call(this);
        },
           
        removeTag: function( tag ) {
            var self = this;
    
            tag.remove();
            delete this.tags[tag.cid];
            
            if ( !tag.id ) return;
            
            
            _ajax("deleteTags", {tagIds: [tag.id], photoId: this.photoId}, function( r ) {
                self._ajaxCallback(r);
            });
        },
                
        addTag: function( tag ) {
            this.tags[tag.cid] = tag;
        },
                
        showTags: function( tagIds ) {
            if ( !tagIds ) return;
    
            $.each( this.tags, function(cid, tag) {
                if ( $.inArray(parseInt(tag.id), tagIds) >= 0 )
                    tag.show(2000);
            });
        },
                
        saveTags: function( tags ) {
            var self = this;
            var jsonTags = [];
            var tagsById = {};
            
            $.each(tags, function(i, tag) {
                jsonTags.push(tag.toJSON());
                tagsById[tag.cid] = tag;
            });
    
            _ajax("saveTags", {tags: jsonTags, photoId: this.photoId}, function( r ) {
                $.each(r.tags, function( cid, id ) {
                    var isNew = tagsById[cid].id === null;
                    tagsById[cid].id = id;
                    tagsById[cid].onSave(isNew);
                });
                
                self._ajaxCallback(r);
            });
        },
        
        _ajaxCallback: function( r ) {
            if ( typeof r.list === 'string' )
                this.showTagList(r.list);
            
            if ( r.permissions ) {
                _permissions = r.permissions;
            }
            
            if ( typeof r.customList === 'string' )
                this.showCustomTagList(r.customList);
            
            if ( r.clearCache ) {
                this.destroyPhotoCache();
            }
            
            if ( r.close && this.floatBox ) {
                this.floatBox.close();
            }
            
            if ( r.refresh ) {
                window.location.reload(true);
            }
        },
                
        
                
        startTagging: function() {
            if ( !_permissions.isModerator && _permissions.credits.actions["tag_photo"] === false ) {
                OW.warning(_permissions.credits.messages["tag_photo"]);
                return false;
            };
    
            var self = this;
            this.selection = new Selection(this.stage, this);
            
            this.selection.onChange = function( tag ) {
                if ( tag.data.type == "user" ) {
                    $.each(self.tags, function(cid, t) {
                        if ( t.key === tag.key ) {
                            tag.id = t.id;
                            tag.cid = t.cid;
                            delete self.tags[cid];
                        }
                    });
                }
                
                self.addTag(tag);
                self.saveTags([tag]);
                
                self.renderTags();
                tag.show(1000, true);
            };
            
            this.tagging = true;
            this.cont.addClass("ut-tagging");
            
            return true;
        },
                
        stopTagging: function() {
            if ( !this.tagging ) return;
            
            var self = this;
            this.selection.destroy();
            
            this.cont.removeClass("ut-tagging");
            
            _ajax("stopTagging", {photoId: this.photoId}, function( r ) {
                self._ajaxCallback(r);
            });
        },
                
        $: function( selector ) {
            return $(selector, this.cont);
        }
    };
    
    // Global events bindings
    $(document.body).on("click", ".ut-stop-tagging", function() {
        window.UTAGS.stopTagging();
    });
    
    
    
    
    window.UTAGS = window.UTAGS || {};
    
    var taggingBtn;
    
    $.extend(window.UTAGS, {
        startTagging: function( photoId, btn ) {
            var result = false;
            if ( currentPhoto )
                result = currentPhoto.startTagging();
            else {
                this.setPhoto(photoId);
                this.startTagging();
                
                return;
            }
            
            if ( result ) {
                taggingBtn = btn;
                taggingBtn.parents("li:eq(0)").addClass("ut-stop-tagging-wrap");
                taggingBtn.text(OW.getLanguageText("utags", "stop_tagging_btn"));
                taggingBtn.removeClass("ut-start-tagging").addClass("ut-stop-tagging");
            }
        },
                
        stopTagging: function() {
            if ( currentPhoto )
                currentPhoto.stopTagging();
            
            if ( taggingBtn ) {
                taggingBtn.text(OW.getLanguageText("utags", "start_tagging_btn"));
                taggingBtn.parents("li:eq(0)").removeClass("ut-stop-tagging-wrap");
                taggingBtn.removeClass("ut-stop-tagging").addClass("ut-start-tagging");
            }
        },
                
        setPhoto: function( photoId ) {
            photoList[photoId] = photoList[photoId] || new Photo(photoId);
            
            return photoList[photoId];
        },
                
        activatePhoto: function( photoId, photoView ) {
            this.setPhoto(photoId);
            if ( currentPhoto ) {
                currentPhoto.deactivate();
                if ( $.isFunction(this._afterPhotoDeactivate) ) this._afterPhotoDeactivate.call(this, currentPhoto);
            }
            
            currentPhoto = photoList[photoId];
            currentPhoto.activate($("#ow-photo-view"), photoView);
            
            if ( $.isFunction(this._afterPhotoActivate) ) this._afterPhotoActivate.call(this, currentPhoto);
        },
                
        _afterPhotoActivate: null,
        _afterPhotoDeactivate: null
    });
};


// ----------------------------< User Selector >--------------------------------




UTAGS.Observer = function( context )
{
    this.events = {};
    this.context = context;
};

UTAGS.Observer.PROTO = function()
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
};

UTAGS.Observer.prototype = new UTAGS.Observer.PROTO();

UTAGS.State = function( data )
{
    data = data || {};
    this.state = data;

    this.observer = new UTAGS.Observer(this);
}

UTAGS.State.PROTO = function()
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

UTAGS.State.prototype = new UTAGS.State.PROTO();

UTAGS.UserState = function( data )
{
    data = data || {};
    this.state = data;

    this.observer = new UTAGS.Observer(this);

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

UTAGS.UserState.prototype = new UTAGS.State.PROTO();



UTAGS.userSelector = (function() {

    var _cache = new UTAGS.UserState();
    var ajaxTimeout, syncing = false;

    var node, select2;
    var _settings = {};

    var formatResult, formatSelection, getData, syncData, getDataFromCache, highlightTerm, getGroupSettings, createCustomItem;

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
            return '<div class="uts-message ow_small">' + data.text + '</div>';

        if ( !data.id )
            return '<div class="uts-group ow_small ow_remark">' + data.text + '</div>';
        
        var html = $(data.html);
        html.find(".mc-ddi-text").html("<span>" + highlightTerm(query.term, data.text) + "</span>");
        
        return html;
    };

    createCustomItem = function( term ) {
        
        return {
            id: "custom_" + Math.floor(Math.random() * 1000000), 
            text: term,
            type: "custom"
        }; 
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
        $.getJSON(_settings.rspUrl, {term: term, context: _settings.context, contextId: _settings.contextId}, function( data ) {
            syncing = false;
            if ( $.isFunction(callback) ) callback(data);
            _cache.mergeState(data);
        });
    };

    getDataFromCache = function( term, count ) {
        count = count || 5;

        var out = [], groups = {}, orderedGroups = [], state = _cache.find(function( item ) {
            if ( !item.text ) return false;
            
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
                    return OW.getLanguageText('utags', 'selector_no_matches', {
                        "term": term
                    });
                },
                        
                "formatInputTooShort": function( term, min ) {
                    return OW.getLanguageText('utags', 'selector_too_short', {
                        "term": term
                    });
                },

                "formatSearching": function() {
                    return OW.getLanguageText('utags', 'selector_searching');
                },
                        
                "createCustomItem": createCustomItem
            }));

            node.next(".ut-field-fake").hide();

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
    };
})();

// Photo Launcher

UTAGS.PhotoLauncher = (function() {

    var _photoView;
    var onHashChange;
    var _onSetup = null;

    onHashChange = function( e ) {
        var photoId = $.bbq.getState("view-photo");
        if ( photoId ) {
            if ( window.photoFBLoading || window.photoFB )
                return;
            _photoView.showPhotoCmp(photoId);
        }

        e.stopPropagation();
    };

    return {
        _setuped: false,
        setup: function ( settings ) {
            if ( !window.photoViewObj ) {
                window.photoViewObj = new photoView(settings);
                
                if ( !window.photoPollingEnabled ) {
                    $(window).bind( "hashchange", onHashChange);
                    window.photoPollingEnabled = true;
                }
            }

            _photoView = window.photoViewObj;
            
            if ( $.isFunction(_onSetup) ) _onSetup.call(this, _photoView);
            this._setuped = true;
        },

        setPhoto: function( photoId ) {
            
            if ( this._setuped ) {
                _photoView.setId(photoId, _photoView);
            } else
                _onSetup = function(pv) {
                    pv.setId(photoId, pv);
                    _onSetup = null;
                };
        }
    };
})();


// ----------------------------< Libraries >-----------------------------------------

//Select2
/*
 Copyright 2012 Igor Vaynberg

 Version: 3.2 Timestamp: Mon Sep 10 10:38:04 PDT 2012

 Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in
 compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software distributed under the License is
 distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and limitations under the License.
 */
 (function ($) {
 	if(typeof $.fn.each2 == "undefined"){
 		$.fn.extend({
 			/*
			* 4-10 times faster .each replacement
			* use it carefully, as it overrides jQuery context of element on each iteration
			*/
			each2 : function (c) {
				var j = $([0]), i = -1, l = this.length;
				while (
					++i < l
					&& (j.context = j[0] = this[i])
					&& c.call(j[0], i, j) !== false //"this"=DOM, i=index, j=jQuery object
				);
				return this;
			}
 		});
 	}
})(jQuery);

(function ($, undefined) {
    "use strict";
    /*global document, window, jQuery, console */

    if (window.Select2 !== undefined) {
        return;
    }

    var KEY, AbstractSelect2, SingleSelect2, MultiSelect2, nextUid, sizer;

    KEY = {
        TAB: 9,
        ENTER: 13,
        ESC: 27,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        HOME: 36,
        END: 35,
        BACKSPACE: 8,
        DELETE: 46,
        isArrow: function (k) {
            k = k.which ? k.which : k;
            switch (k) {
            case KEY.LEFT:
            case KEY.RIGHT:
            case KEY.UP:
            case KEY.DOWN:
                return true;
            }
            return false;
        },
        isControl: function (e) {
            var k = e.which;
            switch (k) {
            case KEY.SHIFT:
            case KEY.CTRL:
            case KEY.ALT:
                return true;
            }

            if (e.metaKey) return true;

            return false;
        },
        isFunctionKey: function (k) {
            k = k.which ? k.which : k;
            return k >= 112 && k <= 123;
        }
    };

    nextUid=(function() { var counter=1; return function() { return counter++; }; }());

    function indexOf(value, array) {
        var i = 0, l = array.length, v;

        if (typeof value === "undefined") {
          return -1;
        }

        if (value.constructor === String) {
            for (; i < l; i = i + 1) if (value.localeCompare(array[i]) === 0) return i;
        } else {
            for (; i < l; i = i + 1) {
                v = array[i];
                if (v.constructor === String) {
                    if (v.localeCompare(value) === 0) return i;
                } else {
                    if (v === value) return i;
                }
            }
        }
        return -1;
    }

    /**
     * Compares equality of a and b taking into account that a and b may be strings, in which case localeCompare is used
     * @param a
     * @param b
     */
    function equal(a, b) {
        if (a === b) return true;
        if (a === undefined || b === undefined) return false;
        if (a === null || b === null) return false;
        if (a.constructor === String) return a.localeCompare(b) === 0;
        if (b.constructor === String) return b.localeCompare(a) === 0;
        return false;
    }

    /**
     * Splits the string into an array of values, trimming each value. An empty array is returned for nulls or empty
     * strings
     * @param string
     * @param separator
     */
    function splitVal(string, separator) {
        var val, i, l;
        if (string === null || string.length < 1) return [];
        val = string.split(separator);
        for (i = 0, l = val.length; i < l; i = i + 1) val[i] = $.trim(val[i]);
        return val;
    }

    function getSideBorderPadding(element) {
        return element.outerWidth() - element.width();
    }

    function installKeyUpChangeEvent(element) {
        var key="keyup-change-value";
        element.bind("keydown", function () {
            if ($.data(element, key) === undefined) {
                $.data(element, key, element.val());
            }
        });
        element.bind("keyup", function () {
            var val= $.data(element, key);
            if (val !== undefined && element.val() !== val) {
                $.removeData(element, key);
                element.trigger("keyup-change");
            }
        });
    }

    $(document).delegate("body", "mousemove", function (e) {
        $.data(document, "select2-lastpos", {x: e.pageX, y: e.pageY});
    });

    /**
     * filters mouse events so an event is fired only if the mouse moved.
     *
     * filters out mouse events that occur when mouse is stationary but
     * the elements under the pointer are scrolled.
     */
    function installFilteredMouseMove(element) {
	    element.bind("mousemove", function (e) {
            var lastpos = $.data(document, "select2-lastpos");
            if (lastpos === undefined || lastpos.x !== e.pageX || lastpos.y !== e.pageY) {
                $(e.target).trigger("mousemove-filtered", e);
            }
        });
    }

    /**
     * Debounces a function. Returns a function that calls the original fn function only if no invocations have been made
     * within the last quietMillis milliseconds.
     *
     * @param quietMillis number of milliseconds to wait before invoking fn
     * @param fn function to be debounced
     * @param ctx object to be used as this reference within fn
     * @return debounced version of fn
     */
    function debounce(quietMillis, fn, ctx) {
        ctx = ctx || undefined;
        var timeout;
        return function () {
            var args = arguments;
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function() {
                fn.apply(ctx, args);
            }, quietMillis);
        };
    }

    /**
     * A simple implementation of a thunk
     * @param formula function used to lazily initialize the thunk
     * @return {Function}
     */
    function thunk(formula) {
        var evaluated = false,
            value;
        return function() {
            if (evaluated === false) { value = formula(); evaluated = true; }
            return value;
        };
    };

    function installDebouncedScroll(threshold, element) {
        var notify = debounce(threshold, function (e) { element.trigger("scroll-debounced", e);});
        element.bind("scroll", function (e) {
            if (indexOf(e.target, element.get()) >= 0) notify(e);
        });
    }

    function killEvent(event) {
        event.preventDefault();
        event.stopPropagation();
    }

    function measureTextWidth(e) {
        if (!sizer){
        	var style = e[0].currentStyle || window.getComputedStyle(e[0], null);
        	sizer = $("<div></div>").css({
	            position: "absolute",
	            left: "-10000px",
	            top: "-10000px",
	            display: "none",
	            fontSize: style.fontSize,
	            fontFamily: style.fontFamily,
	            fontStyle: style.fontStyle,
	            fontWeight: style.fontWeight,
	            letterSpacing: style.letterSpacing,
	            textTransform: style.textTransform,
	            whiteSpace: "nowrap"
	        });
        	$("body").append(sizer);
        }
        sizer.text(e.val());
        return sizer.width();
    }

    function markMatch(text, term, markup) {
        var match=text.toUpperCase().indexOf(term.toUpperCase()),
            tl=term.length;

        if (match<0) {
            markup.push(text);
            return;
        }

        markup.push(text.substring(0, match));
        markup.push("<span class='select2-match'>");
        markup.push(text.substring(match, match + tl));
        markup.push("</span>");
        markup.push(text.substring(match + tl, text.length));
    }

    /**
     * Produces an ajax-based query function
     *
     * @param options object containing configuration paramters
     * @param options.transport function that will be used to execute the ajax request. must be compatible with parameters supported by $.ajax
     * @param options.url url for the data
     * @param options.data a function(searchTerm, pageNumber, context) that should return an object containing query string parameters for the above url.
     * @param options.dataType request data type: ajax, jsonp, other datatatypes supported by jQuery's $.ajax function or the transport function if specified
     * @param options.traditional a boolean flag that should be true if you wish to use the traditional style of param serialization for the ajax request
     * @param options.quietMillis (optional) milliseconds to wait before making the ajaxRequest, helps debounce the ajax function if invoked too often
     * @param options.results a function(remoteData, pageNumber) that converts data returned form the remote request to the format expected by Select2.
     *      The expected format is an object containing the following keys:
     *      results array of objects that will be used as choices
     *      more (optional) boolean indicating whether there are more results available
     *      Example: {results:[{id:1, text:'Red'},{id:2, text:'Blue'}], more:true}
     */
    function ajax(options) {
        var timeout, // current scheduled but not yet executed request
            requestSequence = 0, // sequence used to drop out-of-order responses
            handler = null,
            quietMillis = options.quietMillis || 100;

        return function (query) {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function () {
                requestSequence += 1; // increment the sequence
                var requestNumber = requestSequence, // this request's sequence number
                    data = options.data, // ajax data function
                    transport = options.transport || $.ajax,
                    traditional = options.traditional || false,
                    type = options.type || 'GET'; // set type of request (GET or POST)

                data = data.call(this, query.term, query.page, query.context);

                if( null !== handler) { handler.abort(); }

                handler = transport.call(null, {
                    url: options.url,
                    dataType: options.dataType,
                    data: data,
                    type: type,
                    traditional: traditional,
                    success: function (data) {
                        if (requestNumber < requestSequence) {
                            return;
                        }
                        // TODO 3.0 - replace query.page with query so users have access to term, page, etc.
                        var results = options.results(data, query.page);
                        query.callback(results);
                    }
                });
            }, quietMillis);
        };
    }

    /**
     * Produces a query function that works with a local array
     *
     * @param options object containing configuration parameters. The options parameter can either be an array or an
     * object.
     *
     * If the array form is used it is assumed that it contains objects with 'id' and 'text' keys.
     *
     * If the object form is used ti is assumed that it contains 'data' and 'text' keys. The 'data' key should contain
     * an array of objects that will be used as choices. These objects must contain at least an 'id' key. The 'text'
     * key can either be a String in which case it is expected that each element in the 'data' array has a key with the
     * value of 'text' which will be used to match choices. Alternatively, text can be a function(item) that can extract
     * the text.
     */
    function local(options) {
        var data = options, // data elements
            dataText,
            text = function (item) { return ""+item.text; }; // function used to retrieve the text portion of a data item that is matched against the search

        if (!$.isArray(data)) {
            text = data.text;
            // if text is not a function we assume it to be a key name
            if (!$.isFunction(text)) {
              dataText = data.text; // we need to store this in a separate variable because in the next step data gets reset and data.text is no longer available
              text = function (item) { return item[dataText]; };
            }
            data = data.results;
        }

        return function (query) {
            var t = query.term, filtered = { results: [] }, process;
            if (t === "") {
                query.callback({results: data});
                return;
            }

            process = function(datum, collection) {
                var group, attr;
                datum = datum[0];
                if (datum.children) {
                    group = {};
                    for (attr in datum) {
                        if (datum.hasOwnProperty(attr)) group[attr]=datum[attr];
                    }
                    group.children=[];
                    $(datum.children).each2(function(i, childDatum) { process(childDatum, group.children); });
                    if (group.children.length) {
                        collection.push(group);
                    }
                } else {
                    if (query.matcher(t, text(datum))) {
                        collection.push(datum);
                    }
                }
            };

            $(data).each2(function(i, datum) { process(datum, filtered.results); });
            query.callback(filtered);
        };
    }

    // TODO javadoc
    function tags(data) {
        // TODO even for a function we should probably return a wrapper that does the same object/string check as
        // the function for arrays. otherwise only functions that return objects are supported.
        if ($.isFunction(data)) {
            return data;
        }

        // if not a function we assume it to be an array

        return function (query) {
            var t = query.term, filtered = {results: []};
            $(data).each(function () {
                var isObject = this.text !== undefined,
                    text = isObject ? this.text : this;
                if (t === "" || query.matcher(t, text)) {
                    filtered.results.push(isObject ? this : {id: this, text: this});
                }
            });
            query.callback(filtered);
        };
    }

    /**
     * Checks if the formatter function should be used.
     *
     * Throws an error if it is not a function. Returns true if it should be used,
     * false if no formatting should be performed.
     *
     * @param formatter
     */
    function checkFormatter(formatter, formatterName) {
        if ($.isFunction(formatter)) return true;
        if (!formatter) return false;
        throw new Error("formatterName must be a function or a falsy value");
    }

    function evaluate(val) {
        return $.isFunction(val) ? val() : val;
    }

    function countResults(results) {
        var count = 0;
        $.each(results, function(i, item) {
            if (item.children) {
                count += countResults(item.children);
            } else {
                count++;
            }
        });
        return count;
    }

    /**
     * Default tokenizer. This function uses breaks the input on substring match of any string from the
     * opts.tokenSeparators array and uses opts.createSearchChoice to create the choice object. Both of those
     * two options have to be defined in order for the tokenizer to work.
     *
     * @param input text user has typed so far or pasted into the search field
     * @param selection currently selected choices
     * @param selectCallback function(choice) callback tho add the choice to selection
     * @param opts select2's opts
     * @return undefined/null to leave the current input unchanged, or a string to change the input to the returned value
     */
    function defaultTokenizer(input, selection, selectCallback, opts) {
        var original = input, // store the original so we can compare and know if we need to tell the search to update its text
            dupe = false, // check for whether a token we extracted represents a duplicate selected choice
            token, // token
            index, // position at which the separator was found
            i, l, // looping variables
            separator; // the matched separator

        if (!opts.createSearchChoice || !opts.tokenSeparators || opts.tokenSeparators.length < 1) return undefined;

        while (true) {
            index = -1;

            for (i = 0, l = opts.tokenSeparators.length; i < l; i++) {
                separator = opts.tokenSeparators[i];
                index = input.indexOf(separator);
                if (index >= 0) break;
            }

            if (index < 0) break; // did not find any token separator in the input string, bail

            token = input.substring(0, index);
            input = input.substring(index + separator.length);

            if (token.length > 0) {
                token = opts.createSearchChoice(token, selection);
                if (token !== undefined && token !== null && opts.id(token) !== undefined && opts.id(token) !== null) {
                    dupe = false;
                    for (i = 0, l = selection.length; i < l; i++) {
                        if (equal(opts.id(token), opts.id(selection[i]))) {
                            dupe = true; break;
                        }
                    }

                    if (!dupe) selectCallback(token);
                }
            }
        }

        if (original.localeCompare(input) != 0) return input;
    }

    /**
     * blurs any Select2 container that has focus when an element outside them was clicked or received focus
     *
     * also takes care of clicks on label tags that point to the source element
     */
    $(document).ready(function () {
        $(document).delegate("body", "mousedown touchend", function (e) {
            var target = $(e.target).closest("div.select2-container").get(0), attr;
            if (target) {
                $(document).find("div.select2-container-active").each(function () {
                    if (this !== target) $(this).data("select2").blur();
                });
            } else {
                target = $(e.target).closest("div.select2-drop").get(0);
                $(document).find("div.select2-drop-active").each(function () {
                    if (this !== target) $(this).data("select2").blur();
                });
            }

            target=$(e.target);
            attr = target.attr("for");
            if ("LABEL" === e.target.tagName && attr && attr.length > 0) {
                target = $("#"+attr);
                target = target.data("select2");
                if (target !== undefined) { target.focus(); e.preventDefault();}
            }
        });
    });

    /**
     * Creates a new class
     *
     * @param superClass
     * @param methods
     */
    function clazz(SuperClass, methods) {
        var constructor = function () {};
        constructor.prototype = new SuperClass;
        constructor.prototype.constructor = constructor;
        constructor.prototype.parent = SuperClass.prototype;
        constructor.prototype = $.extend(constructor.prototype, methods);
        return constructor;
    }

    AbstractSelect2 = clazz(Object, {

        // abstract
        bind: function (func) {
            var self = this;
            return function () {
                func.apply(self, arguments);
            };
        },

        // abstract
        init: function (opts) {
            var results, search, resultsSelector = ".select2-results";

            // prepare options
            this.opts = opts = this.prepareOpts(opts);

            this.id=opts.id;

            // destroy if called on an existing component
            if (opts.element.data("select2") !== undefined &&
                opts.element.data("select2") !== null) {
                this.destroy();
            }

            this.enabled=true;
            this.container = this.createContainer();

            this.containerId="s2id_"+(opts.element.attr("id") || "autogen"+nextUid());
            this.containerSelector="#"+this.containerId.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
            this.container.attr("id", this.containerId);

            // cache the body so future lookups are cheap
            this.body = thunk(function() { return opts.element.closest("body"); });

            if (opts.element.attr("class") !== undefined) {
                this.container.addClass(opts.element.attr("class").replace(/validate\[[\S ]+] ?/, ''));
            }

            this.container.css(evaluate(opts.containerCss));
            this.container.addClass(evaluate(opts.containerCssClass));

            // swap container for the element
            this.opts.element
                .data("select2", this)
                .hide()
                .before(this.container);
            this.container.data("select2", this);

            this.dropdown = this.container.find(".select2-drop");
            this.dropdown.addClass(evaluate(opts.dropdownCssClass));
            this.dropdown.data("select2", this);

            this.results = results = this.container.find(resultsSelector);
            this.search = search = this.container.find("input.select2-input");

            search.attr("tabIndex", this.opts.element.attr("tabIndex"));

            this.resultsPage = 0;
            this.context = null;

            // initialize the container
            this.initContainer();
            this.initContainerWidth();

            installFilteredMouseMove(this.results);
            this.dropdown.delegate(resultsSelector, "mousemove-filtered", this.bind(this.highlightUnderEvent));

            installDebouncedScroll(80, this.results);
            this.dropdown.delegate(resultsSelector, "scroll-debounced", this.bind(this.loadMoreIfNeeded));

            // if jquery.mousewheel plugin is installed we can prevent out-of-bounds scrolling of results via mousewheel
            if ($.fn.mousewheel) {
                results.mousewheel(function (e, delta, deltaX, deltaY) {
                    var top = results.scrollTop(), height;
                    if (deltaY > 0 && top - deltaY <= 0) {
                        results.scrollTop(0);
                        killEvent(e);
                    } else if (deltaY < 0 && results.get(0).scrollHeight - results.scrollTop() + deltaY <= results.height()) {
                        results.scrollTop(results.get(0).scrollHeight - results.height());
                        killEvent(e);
                    }
                });
            }

            installKeyUpChangeEvent(search);
            search.bind("keyup-change", this.bind(this.updateResults));
            search.bind("focus", function () { search.addClass("select2-focused"); if (search.val() === " ") search.val(""); });
            search.bind("blur", function () { search.removeClass("select2-focused");});

            this.dropdown.delegate(resultsSelector, "mouseup", this.bind(function (e) {
                if ($(e.target).closest(".select2-result-selectable:not(.select2-disabled)").length > 0) {
                    this.highlightUnderEvent(e);
                    this.selectHighlighted(e);
                } else {
                    this.focusSearch();
                }
                killEvent(e);
            }));

            // trap all mouse events from leaving the dropdown. sometimes there may be a modal that is listening
            // for mouse events outside of itself so it can close itself. since the dropdown is now outside the select2's
            // dom it will trigger the popup close, which is not what we want
            this.dropdown.bind("click mouseup mousedown", function (e) { e.stopPropagation(); });

            if ($.isFunction(this.opts.initSelection)) {
                // initialize selection based on the current value of the source element
                this.initSelection();

                // if the user has provided a function that can set selection based on the value of the source element
                // we monitor the change event on the element and trigger it, allowing for two way synchronization
                this.monitorSource();
            }

            if (opts.element.is(":disabled") || opts.element.is("[readonly='readonly']")) this.disable();
        },

        // abstract
        destroy: function () {
            var select2 = this.opts.element.data("select2");
            if (select2 !== undefined) {
                select2.container.remove();
                select2.dropdown.remove();
                select2.opts.element
                    .removeData("select2")
                    .unbind(".select2")
                    .show();
            }
        },

        // abstract
        prepareOpts: function (opts) {
            var element, select, idKey, ajaxUrl;

            element = opts.element;

            if (element.get(0).tagName.toLowerCase() === "select") {
                this.select = select = opts.element;
            }

            if (select) {
                // these options are not allowed when attached to a select because they are picked up off the element itself
                $.each(["id", "multiple", "ajax", "query", "createSearchChoice", "initSelection", "data", "tags"], function () {
                    if (this in opts) {
                        throw new Error("Option '" + this + "' is not allowed for Select2 when attached to a <select> element.");
                    }
                });
            }

            opts = $.extend({}, {
                populateResults: function(container, results, query) {
                    var populate,  data, result, children, id=this.opts.id, self=this;

                    populate=function(results, container, depth) {

                        var i, l, result, selectable, compound, node, label, innerContainer, formatted;
                        for (i = 0, l = results.length; i < l; i = i + 1) {

                            result=results[i];
                            selectable=id(result) !== undefined;
                            compound=result.children && result.children.length > 0;

                            node=$("<li></li>");
                            node.addClass("select2-results-dept-"+depth);
                            node.addClass("select2-result");
                            node.addClass(selectable ? "select2-result-selectable" : "select2-result-unselectable");
                            if (compound) { node.addClass("select2-result-with-children"); }
                            node.addClass(self.opts.formatResultCssClass(result));

                            label=$("<div></div>");
                            label.addClass("select2-result-label");

                            formatted=opts.formatResult(result, label, query);
                            if (formatted!==undefined) {
                                label.html(self.opts.escapeMarkup(formatted));
                            }

                            node.append(label);

                            if (compound) {

                                innerContainer=$("<ul></ul>");
                                innerContainer.addClass("select2-result-sub");
                                populate(result.children, innerContainer, depth+1);
                                node.append(innerContainer);
                            }

                            node.data("select2-data", result);
                            container.append(node);
                        }
                    };

                    populate(results, container, 0);
                }
            }, $.fn.select2.defaults, opts);

            if (typeof(opts.id) !== "function") {
                idKey = opts.id;
                opts.id = function (e) { return e[idKey]; };
            }

            if (select) {
                opts.query = this.bind(function (query) {
                    var data = { results: [], more: false },
                        term = query.term,
                        children, firstChild, process;

                    process=function(element, collection) {
                        var group;
                        if (element.is("option")) {
                            if (query.matcher(term, element.text(), element)) {
                                collection.push({id:element.attr("value"), text:element.text(), element: element.get(), css: element.attr("class")});
                            }
                        } else if (element.is("optgroup")) {
                            group={text:element.attr("label"), children:[], element: element.get(), css: element.attr("class")};
                            element.children().each2(function(i, elm) { process(elm, group.children); });
                            if (group.children.length>0) {
                                collection.push(group);
                            }
                        }
                    };

                    children=element.children();

                    // ignore the placeholder option if there is one
                    if (this.getPlaceholder() !== undefined && children.length > 0) {
                        firstChild = children[0];
                        if ($(firstChild).text() === "") {
                            children=children.not(firstChild);
                        }
                    }

                    children.each2(function(i, elm) { process(elm, data.results); });

                    query.callback(data);
                });
                // this is needed because inside val() we construct choices from options and there id is hardcoded
                opts.id=function(e) { return e.id; };
                opts.formatResultCssClass = function(data) { return data.css; }
            } else {
                if (!("query" in opts)) {
                    if ("ajax" in opts) {
                        ajaxUrl = opts.element.data("ajax-url");
                        if (ajaxUrl && ajaxUrl.length > 0) {
                            opts.ajax.url = ajaxUrl;
                        }
                        opts.query = ajax(opts.ajax);
                    } else if ("data" in opts) {
                        opts.query = local(opts.data);
                    } else if ("tags" in opts) {
                        opts.query = tags(opts.tags);
                        opts.createSearchChoice = function (term) { return {id: term, text: term}; };
                        opts.initSelection = function (element, callback) {
                            var data = [];
                            $(splitVal(element.val(), opts.separator)).each(function () {
                                var id = this, text = this, tags=opts.tags;
                                if ($.isFunction(tags)) tags=tags();
                                $(tags).each(function() { if (equal(this.id, id)) { text = this.text; return false; } });
                                data.push({id: id, text: text});
                            });

                            callback(data);
                        };
                    }
                }
            }
            if (typeof(opts.query) !== "function") {
                throw "query function not defined for Select2 " + opts.element.attr("id");
            }

            return opts;
        },

        /**
         * Monitor the original element for changes and update select2 accordingly
         */
        // abstract
        monitorSource: function () {
            this.opts.element.bind("change.select2", this.bind(function (e) {
                if (this.opts.element.data("select2-change-triggered") !== true) {
                    this.initSelection();
                }
            }));
        },

        /**
         * Triggers the change event on the source element
         */
        // abstract
        triggerChange: function (details) {

            details = details || {};
            details= $.extend({}, details, { type: "change", val: this.val() });
            // prevents recursive triggering
            this.opts.element.data("select2-change-triggered", true);
            this.opts.element.trigger(details);
            this.opts.element.data("select2-change-triggered", false);

            // some validation frameworks ignore the change event and listen instead to keyup, click for selects
            // so here we trigger the click event manually
            this.opts.element.click();

            // ValidationEngine ignorea the change event and listens instead to blur
            // so here we trigger the blur event manually if so desired
            if (this.opts.blurOnChange)
                this.opts.element.blur();
        },


        // abstract
        enable: function() {
            if (this.enabled) return;

            this.enabled=true;
            this.container.removeClass("select2-container-disabled");
        },

        // abstract
        disable: function() {
            if (!this.enabled) return;

            this.close();

            this.enabled=false;
            this.container.addClass("select2-container-disabled");
        },

        // abstract
        opened: function () {
            return this.container.hasClass("select2-dropdown-open");
        },

        // abstract
        positionDropdown: function() {
            var offset = this.container.offset(),
                height = this.container.outerHeight(),
                width = this.container.outerWidth(),
                dropHeight = this.dropdown.outerHeight(),
                viewportBottom = $(window).scrollTop() + document.documentElement.clientHeight,
                dropTop = offset.top + height,
                dropLeft = offset.left,
                enoughRoomBelow = dropTop + dropHeight <= viewportBottom,
                enoughRoomAbove = (offset.top - dropHeight) >= this.body().scrollTop(),
                aboveNow = this.dropdown.hasClass("select2-drop-above"),
                bodyOffset,
                above,
                css;

            // console.log("below/ droptop:", dropTop, "dropHeight", dropHeight, "sum", (dropTop+dropHeight)+" viewport bottom", viewportBottom, "enough?", enoughRoomBelow);
            // console.log("above/ offset.top", offset.top, "dropHeight", dropHeight, "top", (offset.top-dropHeight), "scrollTop", this.body().scrollTop(), "enough?", enoughRoomAbove);

            // fix positioning when body has an offset and is not position: static

            if (this.body().css('position') !== 'static') {
                bodyOffset = this.body().offset();
                dropTop -= bodyOffset.top;
                dropLeft -= bodyOffset.left;
            }

            // always prefer the current above/below alignment, unless there is not enough room

            if (aboveNow) {
                above = true;
                if (!enoughRoomAbove && enoughRoomBelow) above = false;
            } else {
                above = false;
                if (!enoughRoomBelow && enoughRoomAbove) above = true;
            }

            if (above) {
                dropTop = offset.top - dropHeight;
                this.container.addClass("select2-drop-above");
                this.dropdown.addClass("select2-drop-above");
            }
            else {
                this.container.removeClass("select2-drop-above");
                this.dropdown.removeClass("select2-drop-above");
            }

            css = $.extend({
                top: dropTop,
                left: dropLeft,
                width: width
            }, evaluate(this.opts.dropdownCss));

            this.dropdown.css(css);
        },

        // abstract
        shouldOpen: function() {
            var event;

            if (this.opened()) return false;

            event = $.Event("open");
            this.opts.element.trigger(event);
            return !event.isDefaultPrevented();
        },

        // abstract
        clearDropdownAlignmentPreference: function() {
            // clear the classes used to figure out the preference of where the dropdown should be opened
            this.container.removeClass("select2-drop-above");
            this.dropdown.removeClass("select2-drop-above");
        },

        /**
         * Opens the dropdown
         *
         * @return {Boolean} whether or not dropdown was opened. This method will return false if, for example,
         * the dropdown is already open, or if the 'open' event listener on the element called preventDefault().
         */
        // abstract
        open: function () {

            if (!this.shouldOpen()) return false;

            window.setTimeout(this.bind(this.opening), 1);

            return true;
        },

        /**
         * Performs the opening of the dropdown
         */
        // abstract
        opening: function() {
            var cid = this.containerId, selector = this.containerSelector,
                scroll = "scroll." + cid, resize = "resize." + cid;

            this.container.parents().each(function() {
                $(this).bind(scroll, function() {
                    var s2 = $(selector);
                    if (s2.length == 0) {
                        $(this).unbind(scroll);
                    }
                    s2.select2("close");
                });
            });

            $(window).bind(resize, function() {
                var s2 = $(selector);
                if (s2.length == 0) {
                    $(window).unbind(resize);
                }
                s2.select2("close");
            });

            this.clearDropdownAlignmentPreference();

            if (this.search.val() === " ") { this.search.val(""); }

            this.container.addClass("select2-dropdown-open").addClass("select2-container-active");

            this.updateResults(true);

            if(this.dropdown[0] !== this.body().children().last()[0]) {
                this.dropdown.detach().appendTo(this.body());
            }

            this.dropdown.show();

            this.positionDropdown();
            this.dropdown.addClass("select2-drop-active");

            this.ensureHighlightVisible();

            this.focusSearch();
        },

        // abstract
        close: function () {
            if (!this.opened()) return;

            var self = this;

            this.container.parents().each(function() {
                $(this).unbind("scroll." + self.containerId);
            });
            $(window).unbind("resize." + this.containerId);

            this.clearDropdownAlignmentPreference();

            this.dropdown.hide();
            this.container.removeClass("select2-dropdown-open").removeClass("select2-container-active");
            this.results.empty();
            this.clearSearch();

            this.opts.element.trigger($.Event("close"));
        },

        // abstract
        clearSearch: function () {

        },

        // abstract
        ensureHighlightVisible: function () {
            var results = this.results, children, index, child, hb, rb, y, more;

            index = this.highlight();

            if (index < 0) return;

            if (index == 0) {

                // if the first element is highlighted scroll all the way to the top,
                // that way any unselectable headers above it will also be scrolled
                // into view

                results.scrollTop(0);
                return;
            }

            children = results.find(".select2-result-selectable");

            child = $(children[index]);

            hb = child.offset().top + child.outerHeight();

            // if this is the last child lets also make sure select2-more-results is visible
            if (index === children.length - 1) {
                more = results.find("li.select2-more-results");
                if (more.length > 0) {
                    hb = more.offset().top + more.outerHeight();
                }
            }

            rb = results.offset().top + results.outerHeight();
            if (hb > rb) {
                results.scrollTop(results.scrollTop() + (hb - rb));
            }
            y = child.offset().top - results.offset().top;

            // make sure the top of the element is visible
            if (y < 0) {
                results.scrollTop(results.scrollTop() + y); // y is negative
            }
        },

        // abstract
        moveHighlight: function (delta) {
            var choices = this.results.find(".select2-result-selectable"),
                index = this.highlight();

            while (index > -1 && index < choices.length) {
                index += delta;
                var choice = $(choices[index]);
                if (choice.hasClass("select2-result-selectable") && !choice.hasClass("select2-disabled")) {
                    this.highlight(index);
                    break;
                }
            }
        },

        // abstract
        highlight: function (index) {
            var choices = this.results.find(".select2-result-selectable").not(".select2-disabled");

            if (arguments.length === 0) {
                return indexOf(choices.filter(".select2-highlighted")[0], choices.get());
            }

            if (index >= choices.length) index = choices.length - 1;
            if (index < 0) index = 0;

            choices.removeClass("select2-highlighted");

            $(choices[index]).addClass("select2-highlighted");
            this.ensureHighlightVisible();

        },

        // abstract
        countSelectableResults: function() {
            return this.results.find(".select2-result-selectable").not(".select2-disabled").length;
        },

        // abstract
        highlightUnderEvent: function (event) {
            var el = $(event.target).closest(".select2-result-selectable");
            if (el.length > 0 && !el.is(".select2-highlighted")) {
        		var choices = this.results.find('.select2-result-selectable');
                this.highlight(choices.index(el));
            } else if (el.length == 0) {
                // if we are over an unselectable item remove al highlights
                this.results.find(".select2-highlighted").removeClass("select2-highlighted");
            }
        },

        // abstract
        loadMoreIfNeeded: function () {
            var results = this.results,
                more = results.find("li.select2-more-results"),
                below, // pixels the element is below the scroll fold, below==0 is when the element is starting to be visible
                offset = -1, // index of first element without data
                page = this.resultsPage + 1,
                self=this,
                term=this.search.val(),
                context=this.context;

            if (more.length === 0) return;
            below = more.offset().top - results.offset().top - results.height();

            if (below <= 0) {
                more.addClass("select2-active");
                this.opts.query({
                        term: term,
                        page: page,
                        context: context,
                        matcher: this.opts.matcher,
                        callback: this.bind(function (data) {

                    // ignore a response if the select2 has been closed before it was received
                    if (!self.opened()) return;


                    self.opts.populateResults.call(this, results, data.results, {term: term, page: page, context:context});

                    if (data.more===true) {
                        more.detach().appendTo(results).text(self.opts.formatLoadMore(page+1));
                        window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                    } else {
                        more.remove();
                    }
                    self.positionDropdown();
                    self.resultsPage = page;
                })});
            }
        },

        /**
         * Default tokenizer function which does nothing
         */
        tokenize: function() {

        },

        /**
         * @param initial whether or not this is the call to this method right after the dropdown has been opened
         */
        // abstract
        updateResults: function (initial) {
            var search = this.search, results = this.results, opts = this.opts, data, self=this, input;

            // if the search is currently hidden we do not alter the results
            if (initial !== true && (this.showSearchInput === false || !this.opened())) {
                return;
            }

            search.addClass("select2-active");

            function postRender() {
                results.scrollTop(0);
                search.removeClass("select2-active");
                self.positionDropdown();
            }

            function render(html) {
                results.html(self.opts.escapeMarkup(html));
                postRender();
            }

            if (opts.maximumSelectionSize >=1) {
                data = this.data();
                if ($.isArray(data) && data.length >= opts.maximumSelectionSize && checkFormatter(opts.formatSelectionTooBig, "formatSelectionTooBig")) {
            	    render("<li class='select2-selection-limit'>" + opts.formatSelectionTooBig(opts.maximumSelectionSize) + "</li>");
            	    return;
                }
            }

            this.dropdown.removeClass('select2-dropdown-too-short'); //MCOMPOSE
            if (search.val().length < opts.minimumInputLength && checkFormatter(opts.formatInputTooShort, "formatInputTooShort")) {
                this.dropdown.addClass('select2-dropdown-too-short');
                render("<li class='select2-no-results'>" + opts.formatInputTooShort(search.val(), opts.minimumInputLength) + "</li>");
                return;
            }
            else {
                render("<li class='select2-searching'>" + opts.formatSearching() + "</li>");
            }

            // give the tokenizer a chance to pre-process the input
            input = this.tokenize();
            if (input != undefined && input != null) {
                search.val(input);
            }

            this.resultsPage = 1;
            opts.query({
                    term: search.val(),
                    page: this.resultsPage,
                    context: null,
                    matcher: opts.matcher,
                    callback: this.bind(function (data) {
                var def; // default choice

                // ignore a response if the select2 has been closed before it was received
                if (!this.opened()) return;

                // save context, if any
                this.context = (data.context===undefined) ? null : data.context;

                // create a default choice and prepend it to the list
                if (this.opts.createSearchChoice && search.val() !== "") {
                    def = this.opts.createSearchChoice.call(null, search.val(), data.results);
                    if (def !== undefined && def !== null && self.id(def) !== undefined && self.id(def) !== null) {
                        if ($(data.results).filter(
                            function () {
                                return equal(self.id(this), self.id(def));
                            }).length === 0) {
                            data.results.unshift(def);
                        }
                    }
                }

                this.dropdown.removeClass('select2-dropdown-no-results'); //MCOMPOSE
                if (data.results.length === 0 && checkFormatter(opts.formatNoMatches, "formatNoMatches")) {
                    this.dropdown.addClass('select2-dropdown-no-results');
                    render("<li class='select2-no-results'>" + opts.formatNoMatches(search.val()) + "</li>");
                    return;
                }

                results.empty();
                self.opts.populateResults.call(this, results, data.results, {term: search.val(), page: this.resultsPage, context:null});

                if (data.more === true && checkFormatter(opts.formatLoadMore, "formatLoadMore")) {
                    results.append("<li class='select2-more-results'>" + self.opts.escapeMarkup(opts.formatLoadMore(this.resultsPage)) + "</li>");
                    window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                }

                this.postprocessResults(data, initial);

                postRender();
            })});
        },

        // abstract
        cancel: function () {
            this.close();
        },

        // abstract
        blur: function () {
            this.close();
            this.container.removeClass("select2-container-active");
            this.dropdown.removeClass("select2-drop-active");
            // synonymous to .is(':focus'), which is available in jquery >= 1.6
            if (this.search[0] === document.activeElement) { this.search.blur(); }
            this.clearSearch();
            this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
        },

        // abstract
        focusSearch: function () {
            // need to do it here as well as in timeout so it works in IE
            this.search.show();
            this.search.focus();

            /* we do this in a timeout so that current event processing can complete before this code is executed.
             this makes sure the search field is focussed even if the current event would blur it */
            window.setTimeout(this.bind(function () {
                // reset the value so IE places the cursor at the end of the input box
                this.search.show();
                this.search.focus();
                this.search.val(this.search.val());
            }), 10);
        },

        // abstract
        selectHighlighted: function () {
            var index=this.highlight(),
                highlighted=this.results.find(".select2-highlighted").not(".select2-disabled"),
                data = highlighted.closest('.select2-result-selectable').data("select2-data");
        
            if (data) {
                highlighted.addClass("select2-disabled");
                this.highlight(index);
                this.onSelect(data);
            } else { // UTAGS
                if ( this.opts.createCustomItem ) {
                    data = this.opts.createCustomItem.call(this, this.search.val());
                    if ( data )
                        this.onSelect(data);
                }
            }
        },

        // abstract
        getPlaceholder: function () {
            return this.opts.element.attr("placeholder") ||
                this.opts.element.attr("data-placeholder") || // jquery 1.4 compat
                this.opts.element.data("placeholder") ||
                this.opts.placeholder;
        },

        /**
         * Get the desired width for the container element.  This is
         * derived first from option `width` passed to select2, then
         * the inline 'style' on the original element, and finally
         * falls back to the jQuery calculated element width.
         */
        // abstract
        initContainerWidth: function () {
            function resolveContainerWidth() {
                var style, attrs, matches, i, l;

                if (this.opts.width === "off") {
                    return null;
                } else if (this.opts.width === "element"){
                    return this.opts.element.outerWidth() === 0 ? 'auto' : this.opts.element.outerWidth() + 'px';
                } else if (this.opts.width === "copy" || this.opts.width === "resolve") {
                    // check if there is inline style on the element that contains width
                    style = this.opts.element.attr('style');
                    if (style !== undefined) {
                        attrs = style.split(';');
                        for (i = 0, l = attrs.length; i < l; i = i + 1) {
                            matches = attrs[i].replace(/\s/g, '')
                                .match(/width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/);
                            if (matches !== null && matches.length >= 1)
                                return matches[1];
                        }
                    }

                    if (this.opts.width === "resolve") {
                        // next check if css('width') can resolve a width that is percent based, this is sometimes possible
                        // when attached to input type=hidden or elements hidden via css
                        style = this.opts.element.css('width');
                        if (style.indexOf("%") > 0) return style;

                        // finally, fallback on the calculated width of the element
                        return (this.opts.element.outerWidth() === 0 ? 'auto' : this.opts.element.outerWidth() + 'px');
                    }

                    return null;
                } else if ($.isFunction(this.opts.width)) {
                    return this.opts.width();
                } else {
                    return this.opts.width;
               }
            };

            var width = resolveContainerWidth.call(this);
            if (width !== null) {
                this.container.attr("style", "width: "+width);
            }
        }
    });

    SingleSelect2 = clazz(AbstractSelect2, {

        // single

		createContainer: function () {
            var container = $("<div></div>", {
                "class": "select2-container"
            }).html([
                "    <a href='#' onclick='return false;' class='select2-choice'>",
                "   <span></span><abbr class='select2-search-choice-close' style='display:none;'></abbr>",
                "   <div><b></b></div>" ,
                "</a>",
                "    <div class='select2-drop select2-offscreen'>" ,
                "   <div class='select2-search'>" ,
                "       <input type='text' autocomplete='off' class='select2-input'/>" ,
                "   </div>" ,
                "   <ul class='select2-results'>" ,
                "   </ul>" ,
                "</div>"].join(""));
            return container;
        },

        // single
        opening: function () {
            this.search.show();
            this.parent.opening.apply(this, arguments);
            this.dropdown.removeClass("select2-offscreen");
        },

        // single
        close: function () {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);
            this.dropdown.removeAttr("style").addClass("select2-offscreen").insertAfter(this.selection).show();
        },

        // single
        focus: function () {
            this.close();
            this.selection.focus();
        },

        // single
        isFocused: function () {
            return this.selection[0] === document.activeElement;
        },

        // single
        cancel: function () {
            this.parent.cancel.apply(this, arguments);
            this.selection.focus();
        },

        // single
        initContainer: function () {

            var selection,
                container = this.container,
                dropdown = this.dropdown,
                clickingInside = false;

            this.selection = selection = container.find(".select2-choice");

            this.search.bind("keydown", this.bind(function (e) {
                if (!this.enabled) return;

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                    return;
                }

                if (this.opened()) {
                    switch (e.which) {
                        case KEY.UP:
                        case KEY.DOWN:
                            this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                            killEvent(e);
                            return;
                        case KEY.TAB:
                        case KEY.ENTER:
                            this.selectHighlighted();
                            killEvent(e);
                            return;
                        case KEY.ESC:
                            this.cancel(e);
                            killEvent(e);
                            return;
                    }
                } else {

                    if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e) || e.which === KEY.ESC) {
                        return;
                    }

                    if (this.opts.openOnEnter === false && e.which === KEY.ENTER) {
                        return;
                    }

                    this.open();

                    if (e.which === KEY.ENTER) {
                        // do not propagate the event otherwise we open, and propagate enter which closes
                        return;
                    }
                }
            }));

            this.search.bind("focus", this.bind(function() {
                this.selection.attr("tabIndex", "-1");
            }));
            this.search.bind("blur", this.bind(function() {
                if (!this.opened()) this.container.removeClass("select2-container-active");
                window.setTimeout(this.bind(function() { this.selection.attr("tabIndex", this.opts.element.attr("tabIndex")); }), 10);
            }));

            selection.bind("mousedown", this.bind(function (e) {
                clickingInside = true;

                if (this.opened()) {
                    this.close();
                    this.selection.focus();
                } else if (this.enabled) {
                    this.open();
                }

                clickingInside = false;
            }));

            dropdown.bind("mousedown", this.bind(function() { this.search.focus(); }));

            selection.bind("focus", this.bind(function() {
                this.container.addClass("select2-container-active");
                // hide the search so the tab key does not focus on it
                this.search.attr("tabIndex", "-1");
            }));

            selection.bind("blur", this.bind(function() {
                if (!this.opened()) {
                    this.container.removeClass("select2-container-active");
                }
                window.setTimeout(this.bind(function() { this.search.attr("tabIndex", this.opts.element.attr("tabIndex")); }), 10);
            }));

            selection.bind("keydown", this.bind(function(e) {
                if (!this.enabled) return;

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                    return;
                }

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e)
                 || e.which === KEY.ESC) {
                    return;
                }

                if (this.opts.openOnEnter === false && e.which === KEY.ENTER) {
                    return;
                }

                if (e.which == KEY.DELETE) {
                    if (this.opts.allowClear) {
                        this.clear();
                    }
                    return;
                }

                this.open();

                if (e.which === KEY.ENTER) {
                    // do not propagate the event otherwise we open, and propagate enter which closes
                    killEvent(e);
                    return;
                }

                // do not set the search input value for non-alpha-numeric keys
                // otherwise pressing down results in a '(' being set in the search field
                if (e.which < 48 ) { // '0' == 48
                    killEvent(e);
                    return;
                }

                var keyWritten = String.fromCharCode(e.which).toLowerCase();

                if (e.shiftKey) {
                    keyWritten = keyWritten.toUpperCase();
                }

                // focus the field before calling val so the cursor ends up after the value instead of before
                this.search.focus();
                this.search.val(keyWritten);

                // prevent event propagation so it doesnt replay on the now focussed search field and result in double key entry
                killEvent(e);
            }));

            selection.delegate("abbr", "mousedown", this.bind(function (e) {
                if (!this.enabled) return;
                this.clear();
                killEvent(e);
                this.close();
                this.triggerChange();
                this.selection.focus();
            }));

            this.setPlaceholder();

            this.search.bind("focus", this.bind(function() {
                this.container.addClass("select2-container-active");
            }));
        },

        // single
        clear: function() {
            this.opts.element.val("");
            this.selection.find("span").empty();
            this.selection.removeData("select2-data");
            this.setPlaceholder();
        },

        /**
         * Sets selection based on source element's value
         */
        // single
        initSelection: function () {
            var selected;
            if (this.opts.element.val() === "") {
                this.close();
                this.setPlaceholder();
            } else {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(selected){
                    if (selected !== undefined && selected !== null) {
                        self.updateSelection(selected);
                        self.close();
                        self.setPlaceholder();
                    }
                });
            }
        },

        // single
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments);

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install the selection initializer
                opts.initSelection = function (element, callback) {
                    var selected = element.find(":selected");
                    // a single select box always has a value, no need to null check 'selected'
                    if ($.isFunction(callback))
                        callback({id: selected.attr("value"), text: selected.text()});
                };
            }

            return opts;
        },

        // single
        setPlaceholder: function () {
            var placeholder = this.getPlaceholder();

            if (this.opts.element.val() === "" && placeholder !== undefined) {

                // check for a first blank option if attached to a select
                if (this.select && this.select.find("option:first").text() !== "") return;

                this.selection.find("span").html(this.opts.escapeMarkup(placeholder));

                this.selection.addClass("select2-default").addClass('invitation');

                this.selection.find("abbr").hide();
            }
        },

        // single
        postprocessResults: function (data, initial) {
            var selected = 0, self = this, showSearchInput = true;

            // find the selected element in the result list

            this.results.find(".select2-result-selectable").each2(function (i, elm) {
                if (equal(self.id(elm.data("select2-data")), self.opts.element.val())) {
                    selected = i;
                    return false;
                }
            });

            // and highlight it

            this.highlight(selected);

            // hide the search box if this is the first we got the results and there are a few of them

            if (initial === true) {
                showSearchInput = this.showSearchInput = countResults(data.results) >= this.opts.minimumResultsForSearch;
                this.dropdown.find(".select2-search")[showSearchInput ? "removeClass" : "addClass"]("select2-search-hidden");

                //add "select2-with-searchbox" to the container if search box is shown
                $(this.dropdown, this.container)[showSearchInput ? "addClass" : "removeClass"]("select2-with-searchbox");
            }

        },

        // single
        onSelect: function (data) {
            var old = this.opts.element.val();

            this.opts.element.val(this.id(data));
            this.updateSelection(data);
            this.close();
            this.selection.focus();

            if (!equal(old, this.id(data))) { this.triggerChange(); }
        },

        // single
        updateSelection: function (data) {

            var container=this.selection.find("span"), formatted;

            this.selection.data("select2-data", data);

            container.empty();
            formatted=this.opts.formatSelection(data, container);
            if (formatted !== undefined) {
                container.append(this.opts.escapeMarkup(formatted));
            }

            this.selection.removeClass("select2-default").removeClass('invitation');

            if (this.opts.allowClear && this.getPlaceholder() !== undefined) {
                this.selection.find("abbr").show();
            }
        },

        // single
        val: function () {
            var val, data = null, self = this;

            if (arguments.length === 0) {
                return this.opts.element.val();
            }

            val = arguments[0];

            if (this.select) {
                this.select
                    .val(val)
                    .find(":selected").each2(function (i, elm) {
                        data = {id: elm.attr("value"), text: elm.text()};
                        return false;
                    });
                this.updateSelection(data);
                this.setPlaceholder();
            } else {
                if (this.opts.initSelection === undefined) {
                    throw new Error("cannot call val() if initSelection() is not defined");
                }
                // val is an id. !val is true for [undefined,null,'']
                if (!val) {
                    this.clear();
                    return;
                }
                this.opts.element.val(val);
                this.opts.initSelection(this.opts.element, function(data){
                    self.opts.element.val(!data ? "" : self.id(data));
                    self.updateSelection(data);
                    self.setPlaceholder();
                });
            }
        },

        // single
        clearSearch: function () {
            this.search.val("");
        },

        // single
        data: function(value) {
            var data;

            if (arguments.length === 0) {
                data = this.selection.data("select2-data");
                if (data == undefined) data = null;
                return data;
            } else {
                if (!value || value === "") {
                    this.clear();
                } else {
                    this.opts.element.val(!value ? "" : this.id(value));
                    this.updateSelection(value);
                }
            }
        }
    });

    MultiSelect2 = clazz(AbstractSelect2, {

        // multi
        createContainer: function () {
            var container = $("<div></div>", {
                "class": "select2-container select2-container-multi"
            }).html([
                "    <ul class='select2-choices'>",
                //"<li class='select2-search-choice'><span>California</span><a href="javascript:void(0)" class="select2-search-choice-close"></a></li>" ,
                "  <li class='select2-search-field'>" ,
                "    <input type='text' autocomplete='off' class='select2-input'>" ,
                "  </li>" ,
                "</ul>" ,
                "<div class='select2-drop select2-drop-multi' style='display:none;'>" ,
                "   <ul class='select2-results'>" ,
                "   </ul>" ,
                "</div>"].join(""));
			return container;
        },

        // multi
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments);

            // TODO validate placeholder is a string if specified

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install sthe selection initializer
                opts.initSelection = function (element,callback) {

                    var data = [];
                    element.find(":selected").each2(function (i, elm) {
                        data.push({id: elm.attr("value"), text: elm.text()});
                    });

                    if ($.isFunction(callback))
                        callback(data);
                };
            }

            return opts;
        },

        // multi
        initContainer: function () {

            var selector = ".select2-choices", selection;

            this.searchContainer = this.container.find(".select2-search-field");
            this.selection = selection = this.container.find(selector);

            this.search.bind("keydown", this.bind(function (e) {
                if (!this.enabled) return;

                if (e.which === KEY.BACKSPACE && this.search.val() === "") {
                    this.close();

                    var choices,
                        selected = selection.find(".select2-search-choice-focus");
                    /*
                     * MCOMPOSE
                     *
                     *if (selected.length > 0) {
                        this.unselect(selected.first());
                        this.search.width(10);
                        killEvent(e);
                        return;
                    }*/

                    choices = selection.find(".select2-search-choice");
                    if (choices.length > 0) {
                        //choices.last().addClass("select2-search-choice-focus");
                        this.unselect(choices.last()); // MCOMPOSE
                    }
                } else {
                    selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
                }

                if (this.opened()) {
                    switch (e.which) {
                    case KEY.UP:
                    case KEY.DOWN:
                        this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                        killEvent(e);
                        return;
                    case KEY.ENTER:
                    case KEY.TAB:
                        this.selectHighlighted();
                        killEvent(e);
                        return;
                    case KEY.ESC:
                        this.cancel(e);
                        killEvent(e);
                        return;
                    }
                }

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e)
                 || e.which === KEY.BACKSPACE || e.which === KEY.ESC) {
                    return;
                }

                if (this.opts.openOnEnter === false && e.which === KEY.ENTER) {
                    return;
                }

                this.open();

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                }
            }));

            this.search.bind("keyup", this.bind(this.resizeSearch));

            this.search.bind("blur", this.bind(function(e) {
                this.container.removeClass("select2-container-active");
                this.search.removeClass("select2-focused");
                this.clearSearch();
                e.stopImmediatePropagation();
            }));

            this.container.delegate(selector, "mousedown", this.bind(function (e) {
                if (!this.enabled) return;
                if ($(e.target).closest(".select2-search-choice").length > 0) {
                    // clicked inside a select2 search choice, do not open
                    return;
                }
                this.clearPlaceholder();
                this.open();
                this.focusSearch();
                e.preventDefault();
            }));

            this.container.delegate(selector, "focus", this.bind(function () {
                if (!this.enabled) return;
                this.container.addClass("select2-container-active");
                this.dropdown.addClass("select2-drop-active");
                this.clearPlaceholder();
            }));

            // set the placeholder if necessary
            this.clearSearch();
        },

        // multi
        enable: function() {
            if (this.enabled) return;

            this.parent.enable.apply(this, arguments);

            this.search.removeAttr("disabled");
        },

        // multi
        disable: function() {
            if (!this.enabled) return;

            this.parent.disable.apply(this, arguments);

            this.search.attr("disabled", true);
        },

        // multi
        initSelection: function () {
            var data;
            if (this.opts.element.val() === "") {
                this.updateSelection([]);
                this.close();
                // set the placeholder if necessary
                this.clearSearch();
            }
            if (this.select || this.opts.element.val() !== "") {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(data){
                    if (data !== undefined && data !== null) {
                        self.updateSelection(data);
                        self.close();
                        // set the placeholder if necessary
                        self.clearSearch();
                    }
                });
            }
        },

        // multi
        clearSearch: function () {
            var placeholder = this.getPlaceholder();

            if (placeholder !== undefined  && this.getVal().length === 0 && this.search.hasClass("select2-focused") === false) {
                this.search.val(placeholder).addClass("select2-default").addClass('invitation');
                // stretch the search box to full width of the container so as much of the placeholder is visible as possible
                this.resizeSearch();
            } else {
                // we set this to " " instead of "" and later clear it on focus() because there is a firefox bug
                // that does not properly render the caret when the field starts out blank
                this.search.val(" ").width(10);
            }
        },

        // multi
        clearPlaceholder: function () {
            if (this.search.hasClass("select2-default")) {
                this.search.val("").removeClass("select2-default").removeClass('invitation');
            } else {
                // work around for the space character we set to avoid firefox caret bug
                if (this.search.val() === " ") this.search.val("");
            }
        },

        // multi
        opening: function () {
            this.parent.opening.apply(this, arguments);

            this.clearPlaceholder();
			this.resizeSearch();
            this.focusSearch();
        },

        // multi
        close: function () {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);
        },

        // multi
        focus: function () {
            this.close();
            this.search.focus();
        },

        // multi
        isFocused: function () {
            return this.search.hasClass("select2-focused");
        },

        // multi
        updateSelection: function (data) {
            var ids = [], filtered = [], self = this;

            // filter out duplicates
            $(data).each(function () {
                if (indexOf(self.id(this), ids) < 0) {
                    ids.push(self.id(this));
                    filtered.push(this);
                }
            });
            data = filtered;

            this.selection.find(".select2-search-choice").remove();
            $(data).each(function () {
                self.addSelectedChoice(this);
            });
            self.postprocessResults();
        },

        tokenize: function() {
            var input = this.search.val();
            input = this.opts.tokenizer(input, this.data(), this.bind(this.onSelect), this.opts);
            if (input != null && input != undefined) {
                this.search.val(input);
                if (input.length > 0) {
                    this.open();
                }
            }

        },

        // multi
        onSelect: function (data) {
            this.addSelectedChoice(data);
            if (this.select) { this.postprocessResults(); }

            if (this.opts.closeOnSelect) {
                this.close();
                this.search.width(10);
            } else {
                if (this.countSelectableResults()>0) {
                    this.search.width(10);
                    this.resizeSearch();
                    this.positionDropdown();
                } else {
                    // if nothing left to select close
                    this.close();
                }
            }

            // since its not possible to select an element that has already been
            // added we do not need to check if this is a new element before firing change
            this.triggerChange({ added: data });

            this.focusSearch();
        },

        // multi
        cancel: function () {
            this.close();
            this.focusSearch();
        },

        // multi
        addSelectedChoice: function (data) {
            var choice=$(
                    "<li class='select2-search-choice'>" +
                    "    <div></div>" +
                    "    <a href='#' onclick='return false;' class='select2-search-choice-close' tabindex='-1'></a>" +
                    "</li>"),
                id = this.id(data),
                val = this.getVal(),
                formatted;

            formatted=this.opts.formatSelection(data, choice);
            choice.find("div").replaceWith("<div>"+this.opts.escapeMarkup(formatted)+"</div>");
            choice.find(".select2-search-choice-close")
                .bind("mousedown", killEvent)
                .bind("click dblclick", this.bind(function (e) {
                if (!this.enabled) return;

                $(e.target).closest(".select2-search-choice").fadeOut('fast', this.bind(function(){
                    this.unselect($(e.target));
                    this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
                    this.close();
                    this.focusSearch();
                })).dequeue();
                killEvent(e);
            })).bind("focus", this.bind(function () {
                if (!this.enabled) return;
                this.container.addClass("select2-container-active");
                this.dropdown.addClass("select2-drop-active");
            }));

            choice.data("select2-data", data);
            choice.insertBefore(this.searchContainer);

            val.push(id);
            this.setVal(val);
        },

        // multi
        unselect: function (selected) {
            var val = this.getVal(),
                data,
                index;

            selected = selected.closest(".select2-search-choice");

            if (selected.length === 0) {
                throw "Invalid argument: " + selected + ". Must be .select2-search-choice";
            }

            data = selected.data("select2-data");

            index = indexOf(this.id(data), val);

            if (index >= 0) {
                val.splice(index, 1);
                this.setVal(val);
                if (this.select) this.postprocessResults();
            }
            selected.remove();
            this.triggerChange({ removed: data });
        },

        // multi
        postprocessResults: function () {
            var val = this.getVal(),
                choices = this.results.find(".select2-result-selectable"),
                compound = this.results.find(".select2-result-with-children"),
                self = this;

            choices.each2(function (i, choice) {
                var id = self.id(choice.data("select2-data"));
                if (indexOf(id, val) >= 0) {
                    choice.addClass("select2-disabled").removeClass("select2-result-selectable");
                } else {
                    choice.removeClass("select2-disabled").addClass("select2-result-selectable");
                }
            });

            compound.each2(function(i, e) {
                if (e.find(".select2-result-selectable").length==0) {
                    e.addClass("select2-disabled");
                } else {
                    e.removeClass("select2-disabled");
                }
            });

            choices.each2(function (i, choice) {
                if (!choice.hasClass("select2-disabled") && choice.hasClass("select2-result-selectable")) {
                    self.highlight(0);
                    return false;
                }
            });

        },

        // multi
        resizeSearch: function () {

            var minimumWidth, left, maxWidth, containerLeft, searchWidth,
            	sideBorderPadding = getSideBorderPadding(this.search);

            minimumWidth = measureTextWidth(this.search) + 10;

            left = this.search.offset().left;

            maxWidth = this.selection.width();
            containerLeft = this.selection.offset().left;

            searchWidth = maxWidth - (left - containerLeft) - sideBorderPadding;
            if (searchWidth < minimumWidth) {
                searchWidth = maxWidth - sideBorderPadding;
            }

            if (searchWidth < 40) {
                searchWidth = maxWidth - sideBorderPadding;
            }
            this.search.width(searchWidth);
        },

        // multi
        getVal: function () {
            var val;
            if (this.select) {
                val = this.select.val();
                return val === null ? [] : val;
            } else {
                val = this.opts.element.val();
                return splitVal(val, this.opts.separator);
            }
        },

        // multi
        setVal: function (val) {
            var unique;
            if (this.select) {
                this.select.val(val);
            } else {
                unique = [];
                // filter out duplicates
                $(val).each(function () {
                    if (indexOf(this, unique) < 0) unique.push(this);
                });
                this.opts.element.val(unique.length === 0 ? "" : unique.join(this.opts.separator));
            }
        },

        // multi
        val: function () {
            var val, data = [], self=this;

            if (arguments.length === 0) {
                return this.getVal();
            }

            val = arguments[0];

            if (!val) {
                this.opts.element.val("");
                this.updateSelection([]);
                this.clearSearch();
                return;
            }

            // val is a list of ids
            this.setVal(val);

            if (this.select) {
                this.select.find(":selected").each(function () {
                    data.push({id: $(this).attr("value"), text: $(this).text()});
                });
                this.updateSelection(data);
            } else {
                if (this.opts.initSelection === undefined) {
                    throw new Error("val() cannot be called if initSelection() is not defined")
                }

                this.opts.initSelection(this.opts.element, function(data){
                    var ids=$(data).map(self.id);
                    self.setVal(ids);
                    self.updateSelection(data);
                    self.clearSearch();
                });
            }
            this.clearSearch();
        },

        // multi
        onSortStart: function() {
            if (this.select) {
                throw new Error("Sorting of elements is not supported when attached to <select>. Attach to <input type='hidden'/> instead.");
            }

            // collapse search field into 0 width so its container can be collapsed as well
            this.search.width(0);
            // hide the container
            this.searchContainer.hide();
        },

        // multi
        onSortEnd:function() {

            var val=[], self=this;

            // show search and move it to the end of the list
            this.searchContainer.show();
            // make sure the search container is the last item in the list
            this.searchContainer.appendTo(this.searchContainer.parent());
            // since we collapsed the width in dragStarted, we resize it here
            this.resizeSearch();

            // update selection

            this.selection.find(".select2-search-choice").each(function() {
                val.push(self.opts.id($(this).data("select2-data")));
            });
            this.setVal(val);
            this.triggerChange();
        },

        // multi
        data: function(values) {
            var self=this, ids;
            if (arguments.length === 0) {
                 return this.selection
                     .find(".select2-search-choice")
                     .map(function() { return $(this).data("select2-data"); })
                     .get();
            } else {
                if (!values) { values = []; }
                ids = $.map(values, function(e) { return self.opts.id(e)});
                this.setVal(ids);
                this.updateSelection(values);
                this.clearSearch();
            }
        }
    });

    $.fn.select2 = function () {

        var args = Array.prototype.slice.call(arguments, 0),
            opts,
            select2,
            value, multiple, allowedMethods = ["val", "destroy", "opened", "open", "close", "focus", "isFocused", "container", "onSortStart", "onSortEnd", "enable", "disable", "positionDropdown", "data"];

        this.each(function () {
            if (args.length === 0 || typeof(args[0]) === "object") {
                opts = args.length === 0 ? {} : $.extend({}, args[0]);
                opts.element = $(this);

                if (opts.element.get(0).tagName.toLowerCase() === "select") {
                    multiple = opts.element.attr("multiple");
                } else {
                    multiple = opts.multiple || false;
                    if ("tags" in opts) {opts.multiple = multiple = true;}
                }

                select2 = multiple ? new MultiSelect2() : new SingleSelect2();
                select2.init(opts);
            } else if (typeof(args[0]) === "string") {

                if (indexOf(args[0], allowedMethods) < 0) {
                    throw "Unknown method: " + args[0];
                }

                value = undefined;
                select2 = $(this).data("select2");
                if (select2 === undefined) return;
                if (args[0] === "container") {
                    value=select2.container;
                } else {
                    value = select2[args[0]].apply(select2, args.slice(1));
                }
                if (value !== undefined) {return false;}
            } else {
                throw "Invalid arguments to select2 plugin: " + args;
            }
        });
        return (value === undefined) ? this : value;
    };

    // plugin defaults, accessible to users
    $.fn.select2.defaults = {
        width: "copy",
        closeOnSelect: true,
        openOnEnter: true,
        containerCss: {},
        dropdownCss: {},
        containerCssClass: "",
        dropdownCssClass: "",
        formatResult: function(result, container, query) {
            var markup=[];
            markMatch(result.text, query.term, markup);
            return markup.join("");
        },
        formatSelection: function (data, container) {
            return data ? data.text : undefined;
        },
        formatResultCssClass: function(data) {return undefined;},
        formatNoMatches: function () { return "No matches found"; },
        formatInputTooShort: function (input, min) { return "Please enter " + (min - input.length) + " more characters"; },
        formatSelectionTooBig: function (limit) { return "You can only select " + limit + " item" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Loading more results..."; },
        formatSearching: function () { return "Searching..."; },
        minimumResultsForSearch: 0,
        minimumInputLength: 0,
        maximumSelectionSize: 0,
        id: function (e) { return e.id; },
        matcher: function(term, text) {
            return text.toUpperCase().indexOf(term.toUpperCase()) >= 0;
        },
        separator: ",",
        tokenSeparators: [],
        tokenizer: defaultTokenizer,
        escapeMarkup: function (markup) {
            if (markup && typeof(markup) === "string") {
                return markup.replace(/&/g, "&amp;");
            }
            return markup;
        },
        blurOnChange: false
    };

    // exports
    window.Select2 = {
        query: {
            ajax: ajax,
            local: local,
            tags: tags
        }, util: {
            debounce: debounce,
            markMatch: markMatch
        }, "class": {
            "abstract": AbstractSelect2,
            "single": SingleSelect2,
            "multi": MultiSelect2
        }
    };

}(jQuery));

