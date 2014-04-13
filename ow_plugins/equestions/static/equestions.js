/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

if ( !window.QUESTIONS_Loaded )
{




// ------------------------------ < CORE > ------------------------------

    CORE = {};

    CORE.ObjectRegistry = {};

    /**
     * View
     */
    CORE.View = function( node )
    {
        this.node = node;
    };

    CORE.View.PROTO = function()
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

    CORE.View.prototype = new CORE.View.PROTO();

    /**
     * Model
     */
    CORE.AjaxModel = function( rsp, delegate )
    {
        this.rsp = rsp;
        this.delegate = delegate;

        this.delegate.ajaxEnd = this.delegate.ajaxEnd || function(){};
        this.delegate.ajaxSuccess = this.delegate.ajaxSuccess || function(){};
        this.delegate.ajaxStart = this.delegate.ajaxStart || function(){};
    };

    CORE.AjaxModel.PROTO = function()
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

    CORE.AjaxModel.prototype = new CORE.AjaxModel.PROTO();



    CORE.UploadModel = function( rsp, delegate )
    {
        this.rsp = rsp;
        this.delegate = delegate;
        this.fakeIframe = null;
        this.uniqId = CORE.uniqId('uploadModel');

        CORE.ObjectRegistry[this.uniqId] = this;
    };

    CORE.UploadModel.PROTO = function()
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

    CORE.UploadModel.prototype = new CORE.UploadModel.PROTO();


    CORE.State = function( data )
    {
        data = data || {};
        this.state = data;

        this.observer = new CORE.Observer(this);
    }

    CORE.State.PROTO = function()
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

    CORE.State.prototype = new CORE.State.PROTO();


    CORE.Observer = function( context )
    {
        this.events = {};
        this.context = context;
    };

    CORE.Observer.PROTO = function()
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

    CORE.Observer.prototype = new CORE.Observer.PROTO();


    CORE.uniqId = function( prefix )
    {
        prefix = prefix || '';

        return prefix + (Math.ceil(Math.random() * 1000000000)).toString();
    }


    UTILS = {};

    UTILS.addInvitation = function( nodes )
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


    UTILS.Credits = {};

    UTILS.CreditsConstructor = function( permission, messages )
    {
        var shownMessages = {};

        this.setPermissions = function( perms )
        {
            $.extend(permission, perms);
        }

        this.check = function( action )
        {
            return permission[action] == true;
        };

        this.getMessage = function( action )
        {
            return messages[action];
        };

        this.showMessage = function( action, once )
        {
            once = once || false;

            if ( once  )
            {
                if ( shownMessages[action] )
                {
                    return false;
                }

                shownMessages[action] = true;
            }

            OW.warning(this.getMessage(action));
        }
    };




    CORE.Interval = function( period )
    {
        var fncMap = [], ping, interval;

        ping = function()
        {
            $.each(fncMap, function(i, o)
            {
                o.context = o.context || this;
                o.fnc.call(o.context);
            });
        };

        this.addJob = function( fnc, context )
        {
            fncMap.push({fnc: fnc, context: context});
        };

        this.run = function()
        {
            interval = window.setInterval(ping, period);
        };

        this.stop = function()
        {
            window.clearInterval(interval);
        };

        this.forceRun = function()
        {
            this.stop();
            ping();
            this.run();
        };
    };

// ------------------------------ </ CORE > ------------------------------

// ------------------------------ < Main Script > ------------------------------

    QUESTIONS_QuestionColletction = {};
    QUESTIONS_AnswerListCollection = {};
    QUESTIONS_RelationCollection = {};

    QUESTIONS_QuestionList = function( uniqId, data )
    {
        window.QUESTIONS_ListObject = this;

        var self = this;

        this.node = document.getElementById(uniqId);
        this.$list = null;
        this.data = data;
        this.rsp = null;

        this.initQuestionList = function( questions )
        {
            $(questions).each(function()
            {
                var $q = $(this);
                self.initQuestion($q.attr("rel"), $q)
            });
        };

        this.initQuestion = function( question )
        {

        };

        this.setResponder = function(rsp)
        {
            this.rsp = rsp;
        };

        this.ajax = function(query, callback)
        {
            var cf = function()
            {
                this.ajaxSuccess.apply(this, arguments);
                if (callback)
                {
                    callback.apply(this, arguments);
                }
            };

            this.startAjax();

            $.ajax({
                type: 'POST',
                url: this.rsp,
                data: {
                    "data": JSON.stringify(this.data),
                    "query": JSON.stringify(query)
                },
                context: this,
                success: cf,
                dataType: 'json',
                complete: this.endAjax
            });
        };

        this.startAjax = function() {};
        this.endAjax = function() {};

        this.ajaxSuccess = function(r)
        {
            if ( r.data )
            {
                this.data = r.data;
            }

            if ( r.error )
            {
                OW.error(r.error);
            }

            if ( r.warning )
            {
                OW.warning(r.warning);
            }

            if ( r.info )
            {
                OW.info(r.info);
            }

            if ( r.markup )
            {
                if ( r.markup.html )
                {
                    var newQuestions = $(r.markup.html);

                    if ( r.markup.position == 'append' )
                    {
                        newQuestions.appendTo(this.$list);
                    }

                    if ( r.markup.position == 'prepend' )
                    {
                        newQuestions.prependTo(this.$list);
                    }

                    if ( r.markup.position == 'replace' )
                    {
                        this.$list.empty().append(newQuestions);
                    }

                    this.initQuestionList(newQuestions);
                }

                if ( r.markup.script )
                {
                    OW.addScript(r.markup.script);
                }

                this.$('.ql_delimiter').show();
                this.$('.ql_delimiter:last').hide();
            }

            if ( this.data.viewMore === false )
            {
                this.$viewMoreC.hide();
            }

            if ( this.data.viewMore === true )
            {
                this.$viewMoreC.show();
            }

            if ( r.loadMore && this.data.viewMore )
            {
                this.loadMore(r.loadMore);
            }
        };

        this.$ = function (sel)
        {
            return $(sel, this.node);
        };

        this.loadMore = function( count )
        {
            if ( this.data.displayedCount >= this.data.totalCount )
            {
                return;
            }

            var btn = this.$viewMoreC.find('input.ql_view_more');

            OW.inProgressNode(btn);
            self.ajax({
                "command": "more",
                "count": count
            }, function()
            {
                OW.activateNode(btn);
            });
        };

        this.reload = function( order )
        {
            order = order || 'latest';
            var activeMenuItem = $('.ql-menu .active span');
            activeMenuItem.addClass('q_ic_preloader');

            self.ajax({
                "command": "order",
                "order": order
            }, function()
            {
                activeMenuItem.removeClass('q_ic_preloader');
            });
        };

        this.$list = this.$(".ql-items");
        this.$orderSelect = $('.ql-sort-wrap');
        this.$viewMoreC = this.$(".ql_view_more_c");

        this.initQuestionList(this.$list);

        this.$viewMoreC.find('input.ql_view_more').click(function()
        {
            self.loadMore();
        });

        /*this.$order.click(function()
         {
         self.$orderSelect.show();
         self.$orderWrap.addClass('ql-sort-wrap-active');

         return false;
         });*/

        this.$orderSelect.find('.ql-sort-item').click(function()
        {
            var $self = $(this);

            if ( $self.hasClass('ql-sort-item-checked') )
            {
                return false;
            }

            self.$orderSelect.find('.ql-sort-item').removeClass('ql-sort-item-checked');
            self.reload($self.attr('qorder'));
            $self.addClass('ql-sort-item-checked');

            $('.ql-sort-btn').text($self.find('span').text());

            return false;
        });

        /*$(document).click(function( e )
         {
         self.$orderSelect.hide();
         self.$orderWrap.removeClass('ql-sort-wrap-active');
         });*/
    };


    /*QUESTIONS_QuestionStatus = function( node, pc, vc )
     {
     this.init(node, pc, vc);
     };

     QUESTIONS_QuestionStatus.prototype = new (function(){

     var self = this, node;

     this.init = function(node)
     {

     }

     this.$ = function (sel)
     {
     return $(sel, this.node);
     };
     })();*/

    QUESTIONS_Question = function(uniqId, questionId)
    {
        this.answerList = null;
        this.questionId = questionId;
        this.node = document.getElementById(uniqId);
        OW.bindAutoClicks(this.node);
        var self = this;

        OW.bind('base.comments_list_init', function(p)
        {
            if ( p.entityType == 'question' && p.entityId == self.questionId )
            {
                self.answerList.refreshStatus(this.totalCount, false, false);
                if ( self.answerList.relation )
                {
                    self.answerList.relation.refreshStatus(this.totalCount, false, false);
                }
            }
        });
    }

    QUESTIONS_Question.prototype = new (function(){

        this.$ = function (sel)
        {
            return $(sel, this.node);
        };

        this.setAnswerList = function(answerList)
        {
            this.answerList = answerList;

            this.answerList.setBehavior(QUESTIONS_InQuestionBehavior);
        };

        this.focusOnPostInput = function()
        {
            this.$('.q-comments textarea').focus();
        }

    })();


    QUESTIONS_InQuestionBehavior =
    {
        initViewMore: function()
        {
            var self = this;
            this.$('.qa-view-more').click(function()
            {
                self.loadMore();

                return false;
            });
        }
    };

    QUESTIONS_UserList = function(node, users, voteCount, userId, optionId)
    {
        this.users = users;

        this.userId = userId;
        this.maxCount = 3;
        this.node = node;
        this.voteCount = voteCount;
        this.optionId = optionId;

        this.init();
    }
    QUESTIONS_UserList.prototype =
    {
        $: function (sel)
        {
            return $(sel, this.node);
        },

        init: function()
        {
            var self = this, usersFBOpened = false;

            this.initTooltip()

            this.$('.qaa-view-more-btn').click(function()
            {
                if ( usersFBOpened )
                {
                    return false;
                }

                if ( QUESTIONS_UserList.floatBox )
                {
                    QUESTIONS_UserList.floatBox.close();
                }

                QUESTIONS_UserList.floatBox = OW.ajaxFloatBox('EQUESTIONS_CMP_UserList', [self.optionId, self.getUsers()],
                    {
                        width: 450,
                        iconClass: "ow_ic_user",
                        title: OW.getLanguageText('equestions', 'users_fb_title')
                    });

                if ( QUESTIONS_UserList.floatBox.$preloader )
                {
                    QUESTIONS_UserList.floatBox.$preloader.addClass('q-floatbox-preloader');
                }

                usersFBOpened = true;

                QUESTIONS_UserList.floatBox.bind('close', function(){
                    QUESTIONS_UserList.floatBox = false;
                    usersFBOpened = false;
                });
            });
        },

        getUsers: function()
        {
            var out = [], items;
            items = this.$('.qa-users-c .qa-user');

            for ( var i = 0; i < this.maxCount; i++ )
            {
                out.push($(items[i]).attr("rel"));
            }

            return out;
        },

        add: function()
        {
            this.$('.user-' + this.userId).prependTo(this.$('.qa-users-c'));
            this.voteCount++;

            this.changed();
        },

        remove: function()
        {
            this.$('.user-' + this.userId).appendTo(this.$('.qa-hidden-users-c'));
            this.voteCount--;

            this.changed();
        },

        changed: function()
        {
            if ( this.hasViewMore() )
            {
                this.$('.qaa-view-more-btn').show();
            }
            else
            {
                this.$('.qaa-view-more-btn').hide();
            }
        },

        hasViewMore: function()
        {
            return this.getOtherVoteCount() > 0;
        },

        getUserCount: function()
        {
            var length = this.$('.qa-users-c .qa-user').length;

            return length > this.maxCount ? this.maxCount : length;
        },

        getOtherVoteCount: function()
        {
            return this.voteCount - this.getUserCount();
        },

        initTooltip: function()
        {
            var self = this, $t = this.$('*[title], *[eq-title]');

            $t.unbind();

            $t.hover(function()
            {
                var hasVM = self.hasViewMore();

                var params = {
                    side: 'top'
                };

                if ( hasVM && $(this).is('.qaa-view-more-btn img') )
                {
                    params.side = 'right';

                    if ( !$(this).data('owTip') )
                    {
                        var t = $(this).attr('eq-title');
                        params.show = t.replace('[count]', self.getOtherVoteCount());
                    }
                }

                if ( !hasVM && self.$('.qa-users-c .qa-user:last .qa-user_avatar img').is(this) )
                {
                    params.side = 'right';
                }

                $(this).data('owTipStatus', false);
                OW.showTip($(this), params);
            },
            function()
            {
                OW.hideTip($(this));
            });
        }
    }



    QUESTIONS_AnswersProto = function()
    {
        this.init = function(uniqId, options, data, disabled)
        {
            var self = this;

            this.uniqId = uniqId;

            if ( !data.inPopupMode )
            {
                QUESTIONS_RelationCollection[data.questionId] = this.uniqId;
            }

            this.userId = data.userId;
            this.data = data;
            this.options = {};
            this.activeCommands = 0;

            this.questionFloatBox = false;

            this.totalAnswers = data.totalAnswers;
            this.node = document.getElementById(uniqId);

            this.inProgress = false;
            this.fbMode = OW_FloatBox.version && OW_FloatBox.version > 1;

            this.refreshStatus(false, false, false);

            this.$('.questions-answer').each(function(){
                var opt = $(this).attr('rel');
                self.options['opt_' + opt] = self.bindOption(this, disabled);
            });

            $.each(options, function(i, o)
            {
                var opt = self.getOption(o.id);
                opt.users = new QUESTIONS_UserList(opt.node.find('.qa-users .qa-avatar'), o.users, o.voteCount, self.userId, o.id);
                opt.checked = o.checked;
                opt.newOption = false;
            });


            this.$listFollows = $('.q-' + this.uniqId + '-status-follows');
            this.$listFollowsBtn = this.$listFollows.find('.newsfeed-feature-button-control');

            if ( this.$listFollows.length )
            {
                this.$listFollowsBtn.hover(function()
                {
                    var followTitle = self.$listFollowsBtn.is('.active')
                        ? OW.getLanguageText('equestions', 'toolbar_unfollow_btn')
                        : OW.getLanguageText('equestions', 'toolbar_follow_btn')

                    self.refreshFollowLabel();

                    OW.showTip(self.$listFollows, {
                        side: 'right',
                        show: followTitle
                    });

                }, function()
                {
                    OW.hideTip(self.$listFollows);
                });
            }
        };

        this.refreshFollowLabel = function()
        {
            var followTitle = this.$listFollowsBtn.is('.active')
                ? OW.getLanguageText('equestions', 'toolbar_unfollow_btn')
                : OW.getLanguageText('equestions', 'toolbar_follow_btn')

            if ( this.$listFollows.data('owTip') )
            {
                this.$listFollows.data('owTip').find('.ow_tip_box').html(followTitle);
            }
        };

        this.followQuestion = function()
        {
            this.showUnfollow();

            this.ajax({
                "command": "follow"
            });
        };

        this.unfollowQuestion = function()
        {
            this.showFollow();

            this.ajax({
                "command": "unfollow"
            });
        };

        this.showFollow = function()
        {
            var self = this;

            $('#' + this.uniqId + '-follow').show();
            $('#' + this.uniqId + '-unfollow').hide();

            if ( !this.$listFollows.length ) return;

            this.$listFollowsBtn.removeClass('active').find('.newsfeed-feature-button')
                .get(0).onclick = function() { self.followQuestion(); };

            this.refreshFollowLabel();
        };

        this.showUnfollow = function()
        {
            var self = this;

            $('#' + this.uniqId + '-unfollow').show();
            $('#' + this.uniqId + '-follow').hide();

            if ( !this.$listFollows.length ) return;

            this.$listFollowsBtn.addClass('active').find('.newsfeed-feature-button')
                .get(0).onclick = function() { self.unfollowQuestion(); };

            this.refreshFollowLabel();
        };

        this._showSelectorinProcess = false;
        this.showUserSelector = function()
        {
            if ( this._showSelectorinProcess )
            {
                return false;
            }

            this._showSelectorinProcess = true;

            if ( !UTILS.Credits.check('ask_friend') )
            {
                UTILS.Credits.showMessage('ask_friend');

                this._showSelectorinProcess = false;

                return false;
            }

            var self = this, ull = new UI.UserSelectorLuncher();

            ull.bind('save', function(e){
                this.close();

                self.ajax({
                    "command": "askUsers",
                    'ids': e.ids,
                    'all': e.all ? true : false
                });
            });

            ull.bind('close', function(e)
            {
                self._showSelectorinProcess = false;
            });

            ull.show(this.data.questionId);
        };

        this.showFollowers = function()
        {
            QUESTIONS.showQuestionFollowers(this.data.questionId, this.data.userContext, [this.data.ownerId]);
        };

        this.unvote = function()
        {
            var self = this;

            $('#' + this.uniqId + '-unvote').parent().hide();

            this.$('.qa-check input:checked').each(function(){
                this.checked = false;
                self.answer(this.value);
                self.calculate();
            });
        };

        this.showUnvote = function()
        {
            $('#' + this.uniqId + '-unvote').parent().show();
        }

        this.setRelation = function( rel )
        {
            this.relation = QUESTIONS_AnswerListCollection[rel];
        };

        this.refresh = function()
        {
            this.ajax({
                "command": "reload"
            });
        };

        this.redraw = function( draw )
        {
            $('#' + this.uniqId).replaceWith(draw.markup);
            OW.addScript(draw.script);
        };

        this.deleteQuestion = function()
        {
            if ( this.relation )
            {
                this.relation.deleteQuestion();

                return;
            }

            if ( this.questionFloatBox )
            {
                this.questionFloatBox.close();
            }

            this.ajax({
                "command": "deleteQuestion"
            });

            this.removeQuestionNode();
        };

        this.removeQuestionNode = function()
        {
            $(this.node).parents('li:eq(0)').remove();
            /*$(this.node).parents('li:eq(0)').animate({opacity: 'hide', height: 'hide'}, 'normal', function()
             {
             $(this).remove();
             });*/
        };

        this.refreshStatus = function(pc, vc, fc)
        {
            var self = this;

            var newsfeedStatusChange = function()
            {
                var $comments = $('.q-' + self.uniqId + '-status-comments .newsfeed-feature-label'),
                    $votes = $('.q-' + self.uniqId + '-status-votes .newsfeed-feature-label'),
                    $follows = $('.q-' + self.uniqId + '-status-follows .newsfeed-feature-label');

                vc = vc === false ? parseInt($votes.text()) : parseInt(vc);
                pc = pc === false ? parseInt($comments.text()) : parseInt(pc);
                fc = fc === false ? parseInt($follows.text()) : parseInt(fc);

                $votes.text(vc);
                $comments.text(pc);
                $follows.text(fc);
            }

            var questionStatusChange = function()
            {
                var status = $('#' + self.uniqId + '-status'),
                    $vc = status.find('.q-status-votes'),
                    $pc = status.find('.q-status-posts'),
                    $fc = status.find('.q-status-follows'),
                    $d1 = status.find('.qsd-1'),
                    $d2 = status.find('.qsd-2');

                pc = pc === false ? parseInt($pc.find('.qs-number').text()) : parseInt(pc);
                vc = vc === false ? parseInt($vc.find('.qs-number').text()) : parseInt(vc);
                fc = fc === false ? parseInt($fc.find('.qs-number').text()) : parseInt(fc);

                $d1[ pc && vc ? "show" : "hide" ]();
                $d2[ pc && fc || vc && fc  ? "show" : "hide" ]();

                status.parents('.ql_control:eq(0)')[ pc || vc || fc ? "show" : "hide" ]();
                status[ pc || vc || fc ? "show" : "hide" ]();


                $vc[ vc ? "show" : "hide" ]();
                $pc[ pc ? "show" : "hide" ]();
                $fc[ fc ? "show" : "hide" ]();

                $pc.find('.qs-number').text(pc);
                $vc.find('.qs-number').text(vc);
                $fc.find('.qs-number').text(fc);
            };

            if ( this.data.expandedView )
            {
                questionStatusChange();
            }
            else
            {
                newsfeedStatusChange();
            }
        };

        this.beforeRemove = function() {};

        this.afterRemove = function(optionId, newOption)
        {
            if (!newOption)
            {
                this.data.offset--;
            }

            this.data.optionTotal--;
            this.data.displayedCount--;

            this.controlRemoveBtns();
        };

        this.startCommand = function()
        {
            this.activeCommands++;
        };

        this.endCommand = function()
        {
            this.activeCommands--;
        };

        this.isBusy = function()
        {
            return this.activeCommands > 3;
        };

        this.controlRemoveBtns = function() {};

        this.removeOption = function(optionId)
        {
            var self = this, optionCount, warning, opt = this.getOption(optionId);

            if ( this.beforeRemove(optionId) === false )
            {
                return;
            }

            warning = (opt.users.voteCount == 1 && !opt.checked) || opt.users.voteCount > 1;
            if ( warning && !confirm(OW.getLanguageText('equestions', 'option_not_empty_delete_warning') ))
            {
                return false;
            }

            optionCount = this.data.displayedCount;
            opt.node.fadeTo(100, 0, function(){
                opt.node.slideUp('fast', function(){
                    var lmc, vm = (self.data.optionTotal - self.data.displayedCount) > 0;
                    opt.node.remove();
                    optionCount--;

                    lmc = Math.round((self.data.st.displayedCount * 50) / 100);

                    if ( vm && ( optionCount <= (lmc < 2 ? 2 : lmc) ) )
                    {
                        self.loadMore(self.data.st.displayedCount - lmc);
                    }
                });
            });

            this.ajax({
                "command": "removeOption",
                "opt": optionId,
                "newOption": opt.newOption
            });

            delete this.options['opt_' + optionId];
            this.afterRemove(optionId, opt.newOption);
        };

        this.initViewMore = function()
        {
            var self = this;

            this.$('.qa-view-more').click(function()
            {
                var moreOffset = self.data.optionTotal - self.data.displayedCount;

                if ( moreOffset > 3 )
                {
                    if ( self.fbMode )
                    {
                        self.openQuestion();
                    }
                    else
                    {
                        return true;
                    }
                }
                else
                {
                    self.loadMore();
                }

                return false;
            });
        };

        this.loadMore = function(inc, fnc)
        {
            var self = this,
                $vm = this.$('.qa-view-more'),
                offset = this.data.offset,
                $text;

            inc = inc || false;

            $vm.addClass('ow_preloader');
            $text = $vm.find('.qa-vm-content').hide()

            this.ajax({
                "command": 'more',
                'offset': offset,
                'inc': inc
            }, function() {
                $vm.removeClass('ow_preloader');
                $text.show();

                self.updateViewMore(true);

                if (fnc) fnc.apply(this);
            });
        };

        this.openQuestion = function( focusToPost )
        {
            focusToPost = focusToPost || false;
            var self = this;

            this.questionFloatBox = QUESTIONS.openQuestion({
                userContext: this.data.userContext,
                questionId: this.data.questionId,
                relationUniqId: this.uniqId,
                focusToPost: focusToPost
            });

            this.questionFloatBox.bind('close', function()
            {
                self.questionFloatBox = false;
            });
        };

        this.openQuestionDelegate = function( focusToPost )
        {
            if ( this.fbMode )
            {
                this.openQuestion(focusToPost);

                return false;
            }

            return true;
        };

        this.initAddNew = function()
        {
            var $qadd, $input, $form, $button, inv, self = this;

            $form = this.$('.qaa-form');
            $input = this.$('.qaa-input');
            $button = this.$('.qaa-button');
            $qadd = this.$('.questions-add-answer');


            $button.unbind('mouseover.owtip');
            $button.unbind('mouseout.owtip');

            $button.bind('mouseover.owtip', function()
            {
                var params = {
                    hideEvent: 'mouseout',
                    side: 'right',
                    show: $(this).attr('eq-title')
                };

                OW.showTip($(this), params);
            });

            $button.bind('mouseout.owtip', function()
            {
                $(this).data('owTipHide', true);
            });


            inv = $input.val();

            $button.click(function()
            {
                $form.submit();
            });

            $input.keyup(function()
            {
                if ( !this.value )
                {
                    $(this).data('upperCased', false);
                }

                if ( !$(this).data('upperCased') && this.value )
                {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    $(this).data('upperCased', true);
                }
            });

            $input.focus(function()
            {
                if ( !self.data.ownerMode && !UTILS.Credits.check('add_answer') )
                {
                    UTILS.Credits.showMessage('add_answer', true);
                }
            });

            $input.focus(function()
            {
                if ( this.value == inv )
                {
                    this.value = '';
                    $(this).removeClass('invitation');
                }
            });

            $input.blur(function()
            {
                if ( !this.value )
                {
                    this.value = inv;
                    $(this).addClass('invitation');
                }
            });

            $form.submit(function()
            {
                if ( !self.data.ownerMode && !UTILS.Credits.check('add_answer') )
                {
                    UTILS.Credits.showMessage('add_answer');

                    return false;
                }

                var v = $.trim($input.val()), _return;

                self.$('.questions-answer').each(function()
                {
                    if ( $.trim($('.qa-text', this).text()) == v )
                    {
                        self.hlOption($(this).attr('rel'));
                        _return = true;

                        return false;
                    }
                });

                if ( _return )
                {
                    $input.val('');
                    return false;
                }

                if (v && v != inv)
                {
                    self.ajax({
                        "command": "addAnswer",
                        "text": v
                    }, function(){
                        $qadd.removeClass('ow_preloader').addClass('ow_ic_add');
                        $input.val('');
                        $input.attr("disabled", false);
                    });

                    $input.attr("disabled", "disabled");
                    $qadd.removeClass('ow_ic_add').addClass('ow_preloader');
                }

                return false;
            });
        };

        this.hlOption = function( opt )
        {
            var qal = this.$('.qa-list');
            qal.css('min-height', qal.height());
            qal.append(this.getOption(opt).node);
            qal.css('min-height', 'auto');
        };

        this.bindOption = function(option, disabled)
        {
            var self = this, $opt, $check, $result, out = {};

            disabled = disabled || false

            $opt = $(option);

            $check = $opt.find('.qa-check input');
            $result = $('.qa-result', $opt);
            if ( !$result.width() )
            {
                $result.hide();
            }

            out.id = $opt.attr('rel');
            out.node = $opt;
            out.result = $result;
            out.check = $check;
            out.content = $opt.find('.qa-content');

            var hoverC = out.content.find('.qa-hover-c'),
                cliper = out.content.find('.qa-content-clip'),
                wrapper = out.content.find('.qa-content-wrap'),
                wrapperWidth = 0;

            out.content.hover(function()
                {
                    if ( !wrapperWidth )
                    {
                        wrapperWidth = wrapper.width();
                        wrapper.width(wrapperWidth);
                    }

                    cliper.width(wrapperWidth - hoverC.width());
                },
                function()
                {
                    cliper.width('100%');
                });

            out.remove = $opt.find('.qa-delete-option');
            out.remove.click(function()
            {
                self.removeOption(out.id);
                return false;
            });

            if ( !disabled )
            {
                var chageDelegate = function()
                {
                    self.answer(this.value);
                    self.calculate();
                };

                $check.click(function()
                {
                    if ( !self.data.ownerMode && $check.get(0).checked && !UTILS.Credits.check('answer_question') )
                    {
                        OW.warning(UTILS.Credits.getMessage('answer_question'));

                        return false;
                    }
                });

                $check.change(chageDelegate);

                out.content.click(function()
                {
                    var checkNode = $check.get(0), newChecked;
                    newChecked = $check.is(':radio') ? true : ! checkNode.checked;

                    if ( !self.data.ownerMode && !checkNode.checked && !UTILS.Credits.check('answer_question') )
                    {
                        OW.warning(UTILS.Credits.getMessage('answer_question'));

                        return false;
                    }

                    if ( newChecked != checkNode.checked )
                    {
                        checkNode.checked = newChecked;
                        chageDelegate.apply(checkNode);
                    }
                });
            }

            return out;
        };

        this.getOption = function(opt)
        {
            return this.options['opt_' + opt];
        };

        this.$ = function (sel)
        {
            return $(sel, this.node);
        };

        this.setResponder = function(rsp)
        {
            this.rsp = rsp;
        };

        this.ajax = function(query, callback)
        {
            var relation = null;

            if ( this.isBusy() )
            {
                return false;
            }

            var cf = function()
            {
                this.ajaxSuccess.apply(this, arguments);
                if (callback)
                {
                    callback.apply(this, arguments);
                }
            };

            this.beforeAjax();

            if ( this.relation )
            {
                relation = {
                    uniqId: this.relation.uniqId,
                    data: this.relation.data
                };
            }

            this.startCommand();
            $.ajax({
                type: 'POST',
                url: this.rsp,
                data: {
                    "data": JSON.stringify(this.data),
                    "query": JSON.stringify(query),
                    "relation": JSON.stringify(relation)
                },
                context: this,
                success: cf,
                dataType: 'json',
                complete: function()
                {
                    this.endCommand();
                }
            });
        };

        this.ajaxSuccess = function(r)
        {
            var self = this;

            this.inProgress = false;

            if ( r.permissions )
            {
                UTILS.Credits.setPermissions(r.permissions);
            }

            if ( r.forceNotifications )
            {

            }

            if ( r.listing && window.QUESTIONS_ListObject )
            {
                window.QUESTIONS_ListObject.ajaxSuccess(r.listing);
            }

            if ( r.relation && this.relation )
            {
                this.relation.ajaxSuccess(r.relation);
            }

            if ( r.status )
            {
                this.refreshStatus(r.status.posts, r.status.votes, r.status.follows);
            }

            if ( r.message )
            {
                OW.info(r.message);
            }

            if ( r.error )
            {
                OW.error(r.error);
            }

            if ( r.warning )
            {
                OW.warning(r.warning);
            }

            if ( r.reload )
            {
                this.redraw(r.reload);

                return;
            }

            if ( r.data )
            {
                this.data = r.data;
                this.updateViewMore(true);
            }

            if(r.options)
            {
                this.addOptionList(r.options);
                /*$.each(r.options, function(i, opt)
                 {
                 self.addOption($(opt.markup)[0], opt.data);
                 });
                 this.actionComplete();*/
            }

            if ( r.unvote )
            {
                this.showUnvote();
            }

            if ( r.call )
            {
                if ( typeof r.call == 'string' && $.isFunction(self[r.call]) )
                {
                    self[r.call].apply(self);
                }

                if ( $.isPlainObject(r.call) && $.isFunction(self[r.call.name]) )
                {
                    self[r.call.name].apply(self, r.call.args || []);
                }

                if ( $.isArray(r.call) )
                {
                    $.each(r.call, function(i, fnc)
                    {
                        if ( typeof self[fnc.name] == "function" )
                        {
                            self[fnc.name].apply(self, fnc.args || []);
                        }
                    });
                }
            }
        };

        this.updateViewMore = function( onlyText )
        {
            var more = this.data.optionTotal - this.data.displayedCount,
                vm = this.$('.qa-view-more');

            onlyText = onlyText || false;
            vm.find('.qa-vm-count').text(more);

            if ( more > 0 )
            {
                if (!onlyText) vm.show();
                vm.removeClass('qvm-empty');
            }

            if ( more <= 0 )
            {
                if (!onlyText) vm.hide();
                vm.addClass('qvm-empty');
            }
        };

        this.beforeAjax = function()
        {

        };

        this.answer = function(opt)
        {
            var answers = {"yes": [], "no": []};

            $.each(this.options, function(i, o)
            {
                var checked = o.check.get(0).checked;
                if ( checked != o.checked )
                {
                    answers[checked ? "yes" : "no"].push(o.id);
                    o.users[checked ? "add" : "remove"]();
                    o.checked = checked;
                }
            });

            this.ajax({
                "command": 'answer',
                "answers": answers,
                "optionId": opt
            });
        };

        this.getVoteCount = function(opt)
        {
            var $opt, $c, $r, vn;

            $opt = this.getOption(opt).node;
            $c = this.getOption(opt).check;
            $r = this.getOption(opt).result;

            vn = parseInt($r.attr('rel')) + ($c[0].checked ? 1 : 0);
            vn -= parseInt($c.attr('rel'));

            return vn;
        };

        this.addOption = function(option, data, addTo)
        {
            var o, dublicateOpt;

            dublicateOpt = this.$('.questions-answer[rel=' + data.id + ']');

            if (dublicateOpt.length)
            {
                dublicateOpt.replaceWith(option);
            }
            else
            {
                addTo = addTo || this.$('.qa-list');
                addTo.append(option);
            }
            o = this.bindOption(option);
            o.users = new QUESTIONS_UserList(o.node.find('.qa-users .qa-avatar'), data.users, data.voteCount, this.userId, data.id);
            o.checked = data.checked;
            o.newOption = data.newOption;

            this.options['opt_' + o.id] = o;

            this.afterOptionAdd(o);
        };

        this.afterOptionAdd = function()
        {
            this.controlRemoveBtns();
        };

        this.addOptionList = function(options)
        {
            var self = this, c;

            if (options.length == 1)
            {
                self.addOption($(options[0].markup)[0], options[0].data);
                self.actionComplete();

                return;
            }

            c = $('<div class="qa-option-animate-c"></div>').hide();
            this.$('.qa-list').append(c);

            $.each(options, function(i, opt)
            {
                self.addOption($(opt.markup)[0], opt.data, c);
            });

            if ( options.length > 5 )
            {
                c.after(c.children()).remove();
            }
            else
            {
                c.slideDown(100, function(){
                    c.after(c.children()).remove();
                    self.actionComplete();
                });
            }
        };

        this.actionComplete = function()
        {
            if ( this.data.optionTotal - this.data.displayedCount <= 0 )
            {
                this.$('.questions-add-answer').show();
            }

            this.updateViewMore();
        };

        this.deleteOption = function(opt)
        {
            var option = this.options['opt_' + opt];
            option.node.remove();

            delete this.options['opt_' + opt];
        };

        this.animate = function(option, vn, p)
        {
            var result;
            result = $('.qa-result', option);
            $('.qa-vote-n', option).text(vn);


            if ( p > 0 )
            {
                result.show();
            }
            else
            {
                result.hide();
            }

            if ( p >= 100 )
            {
                result.addClass('q-result-full');
            }
            else
            {
                result.removeClass('q-result-full');
            }

            $('.qa-result', option).css({width: p + '%'});

            /*$('.qa-result', option).animate({width: p + '%'},
             {
             duration: 'fast',
             queue: "global",
             complete: function(){
             if ( p <= 0 )
             {
             $(this).hide();
             }
             }
             });*/

        };

        this.setBehavior = function( beh )
        {
            $.extend(this, beh);
        };
    }


    QUESTIONS_PollAnswers = function()
    {
        var self = this;

        this.calculate = function()
        {
            var ta = this.totalAnswers;

            this.$('.qa-check input').each(function()
            {
                var opt = self.getOption(this.value);
                ta += this.checked ? 1 : 0;
                ta -= parseInt($(this).attr('rel'));
            });

            this.$('.questions-answer').each(function()
            {
                var vn, p;

                vn = self.getVoteCount($(this).attr('rel'));
                p = ta ? (vn * 100 / ta) : 0;

                self.animate(this, vn, p);
            });
        };

        this.controlRemoveBtns = function()
        {
            var options = this.$('.questions-answer');

            if ( this.data.optionTotal <= 3 )
            {
                options.find('.qa-delete-option').hide();
            }
            else
            {
                options.find('.qa-delete-option').show();
            }
        };

    };
    QUESTIONS_PollAnswers.prototype = new QUESTIONS_AnswersProto();


    QUESTIONS_QuestionAnswers = function()
    {
        var self = this;

        this.calculate = function()
        {
            var self = this, ta = 0;

            this.$('.questions-answer').each(function()
            {
                var vn = self.getVoteCount($(this).attr('rel'));
                ta = ta > vn ? ta : vn;
            });

            this.$('.questions-answer').each(function()
            {
                var vn, p;
                vn = self.getVoteCount($(this).attr('rel'));

                p = ta ? (vn * 100 / ta) : 0;

                self.animate(this, vn, p);
            });
        };
    };
    QUESTIONS_QuestionAnswers.prototype = new QUESTIONS_AnswersProto();




    QUESTIONS_Tabs = function( uniqId )
    {
        this.node = document.getElementById(uniqId);

        var $tabs = this.$('.gtabs-tab'),
            $contents = this.$('.gtabs-contents'),
            self = this;

        $tabs.click(function(){
            var $s = $(this), key, $current;
            $tabs.removeClass('gtabs-active');
            $s.addClass('gtabs-active');
            key = $s.data("key");
            $contents.hide();
            $current = $contents.filter('[data-key=' + key + ']').show();
            OW.trigger('questions.tabs_changed', [{
                newTab: $current
            }], self);
        });
    };

    QUESTIONS_Tabs.prototype =
    {
        $: function (sel)
        {
            return $(sel, this.node);
        }
    }

    QUESTIONS_QuestionAdd = function(uniqId, formName, params, attachId)
    {
        var form = owForms[formName],
            questionField = form.elements["question"],
            answersField = form.elements["answers"],
            allowAddNewField = form.elements["allowAddOprions"],
            node = document.getElementById(uniqId),
            attachments;

        attachments = CORE.ObjectRegistry[attachId] ? CORE.ObjectRegistry[attachId] : null;

        $(questionField.input).focus(function()
        {
            if ( !UTILS.Credits.check('ask_question') )
            {
                UTILS.Credits.showMessage('answer_question', true);
            }
        });

        function initQuestionInput( input )
        {
            if ( attachments )
            {
                QUESTIONS.observeLinks(input, function(link)
                {
                    attachments.queryLinkResult(link);
                });
            }

            $('.questions-add .questions-input', node).keyup(function()
            {
                if ( !this.value )
                {
                    $(this).data('upperCased', false);
                }

                if ( !$(this).data('upperCased') && this.value )
                {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                    $(this).data('upperCased', true);
                }
            });
        };


        $(questionField.input).focus(function()
        {
            var input = $(this).unbind().autoResize({
                extraSpace: 0
            });

            initQuestionInput(input);
        });


        if ( attachments )
        {
            attachments.bind('onShowResult', function()
            {
                $(node).addClass('eqa-attachments-shown');
            });

            attachments.bind('onHideResult', function()
            {
                $(node).removeClass('eqa-attachments-shown');
            });


            $('.EQ_AttachmentPhoto', node).click(function()
            {
                attachments.sowImagePanel();
            });

            $('.EQ_AttachmentVideo', node).click(function()
            {
                attachments.showVideoPanel();
            });

            $('.EQ_AttachmentLink', node).click(function()
            {
                attachments.showLinkPanel();
            });

            attachments.bind('onChange', function( state )
            {
                var inp;

                inp = $('input[name=attachment]', form.form);
                if ( !inp.length )
                {
                    inp = $('<input type="hidden" name="attachment" />');
                    inp.appendTo(form.form);
                }

                if ( state.result )
                {
                    inp.val(JSON.stringify(state.result));
                }
                else
                {
                    inp.val('');
                }

            });
        }

        questionField.validate = function()
        {
            var val = questionField.getValue();
            val = $.trim(val);

            if ( !val )
            {
                QUESTIONS_Feedback.error(OW.getLanguageText('equestions', 'feedback_question_empty'), questionField.input);
                throw 'SubmitError';
            }

            if ( val.length < params.minQuestionLength )
            {
                QUESTIONS_Feedback.error(OW.getLanguageText('equestions', 'feedback_question_min_length'), questionField.input);
                throw 'SubmitError';
            }

            if ( val.length > params.maxQuestionLength )
            {
                QUESTIONS_Feedback.error(OW.getLanguageText('equestions', 'feedback_question_max_length'), questionField.input);
                throw 'SubmitError';
            }
        };

        answersField.form = form;
        answersField.maxAnswerLength = params.maxAnswerLength;

        answersField.onValidate = function()
        {
            if ( !allowAddNewField.input.checked && answersField.getValue().length < 2 )
            {
                this.showError(OW.getLanguageText('equestions', 'feedback_question_two_apt_required'), $(allowAddNewField.input));
                throw 'SubmitError';
            }
        };

        $('.questions-add-answers-btn', node).click(function()
        {
            $(node).addClass('eqa-answers-shown');

            $('.questions-add-answers', node).show();
            $(this).hide();
            $('.questions-add-answers-options', node).show();
        });

        OW.bind("questions.after_question_add", function( r )
        {
            if ( r.permissions )
            {
                UTILS.Credits.setPermissions(r.permissions);
            }

            $('.questions-add-answers', node).slideUp('fast');
            $('.questions-add-answers-options', node).hide();
            $('.questions-add-answers-btn', node).show();

            if ( attachments )
            {
                attachments.reset();
            }
        });
    };

    QUESTIONS_AnswersField = function(id, name, inv)
    {
        this.form = null;
        this.invMsg = inv;
        this.maxAnswerLength = 150;

        OwFormElement.call(this, id, name);

        var self = this;

        this.delegates =
        {
            onLastFocus: function()
            {
                self.addItem();
            },

            onFocus: function()
            {
                if ( this.value == inv )
                {
                    this.value = '';
                    $(this).removeClass('invitation');
                }
            },

            onKeyUp: function()
            {
                if ( this.value != inv )
                {
                    if ( !this.value )
                    {
                        $(this).data('upperCased', false);
                    }

                    if ( !$(this).data('upperCased') && this.value )
                    {
                        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                        $(this).data('upperCased', true);
                    }
                }
            },

            onBlur: function(){
                if ( !this.value )
                {
                    this.value = inv;
                    $(this).addClass('invitation');
                }
            }
        };

        function bindInput(input)
        {
            input.unbind('focus').bind('focus', self.delegates.onFocus);
            input.unbind('blur').bind('blur', self.delegates.onBlur);
            input.unbind('keyup').bind('keyup', self.delegates.onKeyUp);
        }

        function bindLastInput(input)
        {
            input.unbind('focus.add_new').bind('focus.add_new', self.delegates.onLastFocus);
        }

        bindInput($('.mt-item-input', this.input));
        bindLastInput($('.mt-item-input:last', this.input));

        this.getValue = function()
        {
            var self = this, out = [];

            this.removeErrors();

            $('.mt-item-input', this.input).each(function(i, o)
            {
                var val = $(o).val();
                if ( val && self.invMsg != val )
                {
                    out.push(val);
                }
            });

            return out;
        };

        this.setValue = function( value )
        {
            $('.mt-item-input', this.input).each(function(i, o)
            {
                $(o).val(value[i-1] || '');
            });
        };

        this.resetValue = function()
        {
            var self = this;

            this.removeErrors();

            $('.mt-added-item', this.input).remove();
            $('.mt-item-input', this.input).each(function(i, o)
            {
                o.value = '';
                self.delegates.onBlur.call(o);
            });

            bindLastInput($('.mt-item-input:last', this.input));
        };

        this.addItem = function()
        {
            $('.mt-item-input').unbind('focus.add_new');
            var input = $('.mt-item:eq(0)', this.input).clone().show();
            input.addClass('mt-added-item');
            input.appendTo(this.input);
            bindInput(input.find('.mt-item-input'));
            bindLastInput(input.find('.mt-item-input'));
        };

        this.validate = function()
        {
            var dub = {};

            $('.mt-item', this.input).removeClass('mt-incorrect-option').each(function(i, o)
            {
                var $input = $('.mt-item-input', o);
                var val = $input.val();
                if ( val && self.invMsg != val )
                {
                    if ( val.length > self.maxAnswerLength )
                    {
                        self.showError(OW.getLanguageText('equestions', 'feedback_option_max_length'), $input);
                        throw 'SubmitError';
                    }

                    if (dub[val])
                    {
                        $(o).addClass('mt-incorrect-option');
                        $input.one('keydown.incorrect', function()
                        {
                            $(o).removeClass('mt-incorrect-option');
                        });

                        $input.get(0).focus();
                        self.showError( OW.getLanguageText('equestions', 'feedback_question_dublicate_option'), $input);
                        throw 'SubmitError';
                    }
                    else
                    {
                        dub[val] = true;
                    }
                }
            });

            this.onValidate(this)
        };

        this.onValidate = function(){};

        this.removeErrors = function()
        {
            QUESTIONS_Feedback.removeErrors();
        };

        this.showError = function( msg, node )
        {
            QUESTIONS_Feedback.error(msg, node);
        };
    };

    QUESTIONS_AnswersField.prototype = OwFormElement.prototype;

    QUESTIONS_Feedback = {};
    QUESTIONS_Feedback.errors = [];

    QUESTIONS_Feedback.error = function( msg, node )
    {
        node = $(node);

        if ( node.data('owTip') )
        {
            node.data('owTip').find('.ow_tip_box').html(msg);
        }

        var params = {
            side: 'left',
            show: msg,
            width: 150
        };

        OW.showTip(node, params);
        QUESTIONS_Feedback.errors.push(node);

        window.setTimeout(function()
        {
            OW.hideTip(node);
        }, 8000);
    };

    QUESTIONS_Feedback.removeErrors = function()
    {
        for ( var i = 0; i < QUESTIONS_Feedback.errors.length; i++ )
        {
            OW.hideTip(QUESTIONS_Feedback.errors.pop());
        }
    };


    QUESTIONS_LinkObserver = function( input, callBack )
    {
        this.callback = callBack;
        this.input = $(input);

        this.startObserve();
    };

    QUESTIONS_LinkObserver.prototype =
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

    QUESTIONS = new (function(){

        this.observeLinks = function( input, callBack )
        {
            return new QUESTIONS_LinkObserver(input, callBack);
        };

        this.openQuestion = function( params )
        {
            params.focusToPost = params.focusToPost || false;

            if ( params.relationId && !params.relationUniqId && QUESTIONS_RelationCollection[params.relationId])
            {
                params.relationUniqId = QUESTIONS_RelationCollection[params.relationId];
            }

            var fb;

            fb = OW.ajaxFloatBox('EQUESTIONS_CMP_Question', [params.questionId, params.userContext, null,
                {
                    "relation": params.relationUniqId,
                    "focusToPost" : params.focusToPost,
                    "loadStatic": false,
                    "inPopup": true
                }],
                {
                    width: 550,
                    top: 50,
                    iconClass: "ow_ic_lens"
                    //, title: OW.getLanguageText('equestions', 'question_fb_title')
                });

            return fb;
        };

        this.showQuestionFollowers = function( questionId, userContext, ignoreUsers )
        {
            QUESTIONS_UserList.floatBox = OW.ajaxFloatBox('EQUESTIONS_CMP_FollowList', [questionId, userContext, ignoreUsers],
                {
                    width: 450,
                    iconClass: "ow_ic_user",
                    title: OW.getLanguageText('equestions', 'followers_fb_title')
                });

            if ( QUESTIONS_UserList.floatBox.$preloader )
            {
                QUESTIONS_UserList.floatBox.$preloader.addClass('q-floatbox-preloader');
            }

            QUESTIONS_UserList.floatBox.bind('close', function(){
                QUESTIONS_UserList.floatBox = false;
            });
        };

    })();

    QUESTIONS.friendMode = false;

    /*
     * jQuery autoResize (textarea auto-resizer)
     * @copyright James Padolsey http://james.padolsey.com
     * @version 1.04
     */

    (function(a){a.fn.autoResize=function(j){var b=a.extend({onResize:function(){},animate:true,animateDuration:150,animateCallback:function(){},extraSpace:20,limit:1000},j);this.filter('textarea').each(function(){var c=a(this).css({resize:'none','overflow-y':'hidden'}),k=c.height(),f=(function(){var l=['height','width','lineHeight','textDecoration','letterSpacing'],h={};a.each(l,function(d,e){h[e]=c.css(e)});return c.clone().removeAttr('id').removeAttr('name').css({position:'absolute',top:0,left:-9999}).css(h).attr('tabIndex','-1').insertBefore(c)})(),i=null,g=function(){f.height(0).val(a(this).val()).scrollTop(10000);var d=Math.max(f.scrollTop(),k)+b.extraSpace,e=a(this).add(f);if(i===d){return}i=d;if(d>=b.limit){a(this).css('overflow-y','');return}b.onResize.call(this);b.animate&&c.css('display')==='block'?e.stop().animate({height:d},b.animateDuration,b.animateCallback):e.height(d)};c.unbind('.dynSiz').bind('keyup.dynSiz',g).bind('keydown.dynSiz',g).bind('change.dynSiz',g)});return this}})(jQuery);



// ------------------------------ </ Main Script > ------------------------------

// ------------------------------ < Attachments > ------------------------------

    $(function(){
        UTILS.addInvitation($('input:text[inv],textarea[inv]'));
    });


    ATTACHMENTS = {};

    ATTACHMENTS.Attachments = function(uniqId, opt)
    {
        this.uniqId = uniqId;

        this.result = '';
        this.content = '';
        this.contentType = null;

        this.view = new CORE.View(document.getElementById(this.uniqId));

        this.view.$result = this.view.$('.ATT_Result');
        this.view.$resultContent = this.view.$('.ATT_ResultContent');
        this.view.$close = this.view.$('.ATT_BodyClose');
        this.view.$self = $(this.view.node);

        this.observer = new CORE.Observer(this);
        this.floatBox = null;

        this.init();
    };

    ATTACHMENTS.Attachments.PROTO = function()
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

        this.showResult = function()
        {
            this.trigger('onShowResult');
            this.view.$result.show();

            this.view.$self.removeClass('eqa-result-full-width');
        };

        this.hideResult = function()
        {
            this.trigger('onHideResult');
            this.view.$result.hide();
        };

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
                    icon_class: 'ow_ic_picture',
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
                    icon_class: 'ow_ic_video',
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

            var parentWidth = this.view.$self.width();

            this.view.$resultContent.empty().append($contents);
            this.showResult();
            this.view.$result.find('.AL_Input').focus();

            this.view.$self.addClass('eqa-result-full-width');

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
                this.showResult();
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
            this.hideResult();

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

    ATTACHMENTS.Attachments.prototype = new ATTACHMENTS.Attachments.PROTO();


    ATTACHMENTS.ImagePanel = function(uniqId, glob)
    {
        var self = this;

        this.delegate = CORE.ObjectRegistry[glob.delegate];

        this.uniqId = uniqId;
        this.glob = glob;

        this.busy = false;

        this.ajax = new CORE.AjaxModel(this.glob.rsp, this);
        this.uploader = new CORE.UploadModel(this.glob.uploader, this);

        this.view = new CORE.View(document.getElementById(this.uniqId));

        this.view.$result = this.view.$('.AI_Result');
        this.view.$uploadPanel = this.view.$('.AI_UploadPanel');
        this.view.$uploadButton = this.view.$('input.IA_UploadButton');
        this.view.$uploadInput = this.view.$('.AI_UploadInput');

        this.view.$takePanel = this.view.$('.AI_TakePanel');
        this.view.$takeControls = this.view.$('.AI_TakePhotoControls');
        this.view.$takeShootBtn = this.view.$('.AI_TakePhotoBtn');
        this.view.$takeResetBtn = this.view.$('.AI_ResetPhotoBtn');

        this.myPanelLoaded = false;
        this.myPanelOffset = 0;
        this.view.$mySwitch = this.view.$('.AI_SwitchToMyPhotos');
        this.view.$myPanel = this.view.$('.AI_MyPanel');
        this.view.$myPanelList = this.view.$('.AI_MyPanelList');
        this.view.$myPanelListVM = this.view.$('.AI_MyPanelListViewMore');
        this.view.$myPanelListVMLabel = this.view.$('.AI_MyPanelListViewMoreLabel');

        this.view.$mySave = this.view.$('.AI_MySave');

        this.view.$takeScreen = this.view.$('.AI_TakeScreen');

        this.view.$cancelBtn = this.view.$('.AI_Cancel');
        this.view.$uploadSaveBtn = this.view.$('.AI_UploadSave');
        this.view.$takeSaveBtn = this.view.$('.AI_TakeSave');
        this.view.$closeBtn = this.view.$('.AI_Close');

        this.view.$mainControls = this.view.$('.AI_MainControl');
        this.view.$views = this.view.$('.AI_View');

        this.view.$mySwitch.click(function()
        {
            if ( self.myPanelLoaded )
            {
                self.showMyPanel();

                return false;
            }

            self.ajax.query('getMyPhotos', {
                'offset': self.myPanelOffset
            });

            return false;
        });

        this.view.$myPanelListVM.click(function()
        {
            self.ajax.query('getMyPhotos', {
                'offset': self.myPanelOffset
            });

            return false;
        });

        this.view.$mySave.click(function()
        {
            var selected = self.view.$myPanelList.find('.aim-active');

            var oembed = selected.find('.AIM_Oembed').val();
            self.ajax.query('saveMyPhoto', {
               "oembed": oembed
            });
        });

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

    ATTACHMENTS.ImagePanel.PROTO = function()
    {
        this.ajaxStart = function( command, params )
        {
            if ( command == 'saveMyPhoto' )
            {
                OW.inProgressNode(this.view.$mySave.find('input'));
            }

            if ( command == 'getMyPhotos' )
            {
                this.view.$mySwitch.removeClass('ow_ic_picture');
                this.view.$mySwitch.addClass('ow_preloader');
                OW.inProgressNode(this.view.$mySwitch);

                this.view.$myPanelListVMLabel.addClass('ow_preloader');
                OW.inProgressNode(this.view.$myPanelListVM);
            }
        };

        this.ajaxEnd = function( command, response )
        {
            if ( command == 'saveMyPhoto' )
            {
                OW.activateNode(this.view.$mySave.find('input'));
            }

            if ( command == 'getMyPhotos' )
            {
                this.view.$mySwitch.addClass('ow_ic_picture');
                this.view.$mySwitch.removeClass('ow_preloader');
                OW.activateNode(this.view.$mySwitch);

                OW.activateNode(this.view.$myPanelListVM);
                this.view.$myPanelListVMLabel.removeClass('ow_preloader');
            }
        };

        this.ajaxSuccess = function( command, response )
        {
            if ( response.content )
            {

                this.view.$views.hide();
                this.view.renderContent(response.content, this.view.$result);
                this.view.$result.show();
                this.view.$mySwitch.hide();

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

                if ( command == 'saveMyPhoto' )
                {
                    this.delegate.save();
                    this.delegate.floatBox.close();
                }
            }

            if ( response.myPanel )
            {
                this.showMyPanel(response.myPanel);
            }
        };

        this.fitWindow = function(params)
        {
            params = params || {};
            params.width = params.width || 420;

            this.delegate.floatBox.fitWindow(params);
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
            this.view.$mySwitch.show();

            this.fitWindow();
        };

        /* My Panel */

        this.showMyPanel = function( panel )
        {
            if ( panel )
            {
                if ( panel.html )
                {
                    var items = $(panel.html);
                    if ( this.myPanelLoaded )
                    {
                        this.view.$myPanelList.append(items);
                    }
                    else
                    {
                        this.view.$myPanelList.html(items);
                    }

                    if ( panel.itemsCount )
                    {
                        OW.bindTips(this.view.$myPanelList);
                        this.myPanelLoaded = true;
                        this.bindMyPhoto(items);
                    }
                }

                if ( panel.offset )
                {
                    this.myPanelOffset = panel.offset;
                }

                OW.activateNode(this.view.$myPanelListVM);

                if ( panel.viewMore )
                {
                    this.view.$myPanelListVM.show();
                }
                else
                {
                    this.view.$myPanelListVM.hide();
                }
            }

            this.view.$myPanelList.find('.aim-active').removeClass('aim-active');
            this.view.$views.hide();
            this.view.$myPanel.show()

            var winHeight = jQuery(window).height();

            var height = winHeight - 200;

            height = height > 570 ? 570 : height;
            this.view.$myPanel.css('max-height', height);

            this.view.$mainControls.hide();
            this.view.$cancelBtn.show();

            OW.activateNode(this.view.$mySwitch);
            this.view.$mySwitch.hide();
            this.fitWindow({width: 600});

            this.updateMyScroll();
        };

        this.updateMyScroll = function( toTop )
        {
            var self = this;

            this.view.$myPanel.css("overflow", "hidden");
            window.setTimeout(function()
            {
                var scrollApi;

                self.view.$myPanel.css("overflow", "auto");
                scrollApi = OW.addScroll(self.view.$myPanel);
                if ( toTop )
                {
                    scrollApi.scrollToY(0, false);
                }
            });
        };

        this.bindMyPhoto = function( $items )
        {
            var self = this;

            $items.each(function()
            {
                var $node = $(this);

                $node.click(function()
                {
                    $items.removeClass('aim-active')
                    $node.addClass('aim-active');
                    self.view.$mySave.show();
                });
            });
        };

        /* Take a photo*/

        this.showTakePanel = function()
        {
            this.view.$views.hide();
            this.view.$takePanel.show();

            this.view.$mainControls.hide();
            this.view.$mySwitch.hide();
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
                        "type": "file",
                        "fileId": r.find('fileId').text(),
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

    ATTACHMENTS.ImagePanel.prototype = new ATTACHMENTS.ImagePanel.PROTO();




    ATTACHMENTS.VideoPanel = function(uniqId, glob)
    {
        var self = this;

        this.busy = false;
        this.delegate = CORE.ObjectRegistry[glob.delegate];

        this.oembed = false;

        this.uniqId = uniqId;
        this.glob = glob;

        this.ajax = new CORE.AjaxModel(this.glob.rsp, this);
        this.view = new CORE.View(document.getElementById(this.uniqId));

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

        this.myPanelLoaded = false;
        this.myPanelOffset = 0;
        this.view.$mySwitch = this.view.$('.AV_SwitchToMyVideos');
        this.view.$myPanel = this.view.$('.AV_MyPanel');
        this.view.$myPanelList = this.view.$('.AV_MyPanelList');
        this.view.$myPanelListVM = this.view.$('.AV_MyPanelListViewMore');
        this.view.$myPanelListVMLabel = this.view.$('.AV_MyPanelListViewMoreLabel');
        this.view.$mySave = this.view.$('.AV_MySave');

        this.view.$mySwitch.click(function()
        {
            if ( self.myPanelLoaded )
            {
                self.showMyPanel();

                return false;
            }

            self.ajax.query('getMyVideos', {
                'offset': self.myPanelOffset
            });

            return false;
        });

        this.view.$myPanelListVM.click(function()
        {
            self.ajax.query('getMyVideos', {
                'offset': self.myPanelOffset
            });

            return false;
        });

        this.view.$mySave.click(function()
        {
            var selected = self.view.$myPanelList.find('.avm-active');

            var oembed = selected.find('.AVM_Oembed').val();
            self.ajax.query('saveMyVideo', {
               "oembed": oembed
            });
        });

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

    ATTACHMENTS.VideoPanel.PROTO = function()
    {
        this.goHome = function()
        {
            this.view.$views.hide();
            this.view.$mainControls.hide();
            this.view.$home.show();
            this.view.$closeBtn.show();
            this.view.$YTSearchHomeInput.val('').blur();
            this.view.$embedHomeInput.val('').blur();

            this.view.$mySwitch.show();

            this.view.$result.empty();

            this.fitWindow({
                width: 420
            });
        };

        /* My Panel */

        this.showMyPanel = function( panel )
        {
            if ( panel )
            {
                if ( panel.html )
                {
                    var items = $(panel.html);
                    if ( this.myPanelLoaded )
                    {
                        this.view.$myPanelList.append(items);
                    }
                    else
                    {
                        this.view.$myPanelList.html(items);
                    }

                    if ( panel.itemsCount )
                    {
                        this.myPanelLoaded = true;
                        OW.bindTips(this.view.$myPanelList);
                        this.bindMyVideos(items);
                    }
                }

                if ( panel.offset )
                {
                    this.myPanelOffset = panel.offset;
                }

                OW.activateNode(this.view.$myPanelListVM);

                if ( panel.viewMore )
                {
                    this.view.$myPanelListVM.show();
                }
                else
                {
                    this.view.$myPanelListVM.hide();
                }
            }

            this.view.$myPanelList.find('.avm-active').removeClass('avm-active');
            this.view.$views.hide();
            this.view.$myPanel.show()

            var winHeight = jQuery(window).height();

            var height = winHeight - 250;
            this.view.$myPanel.css('max-height', height);

            this.view.$mainControls.hide();
            this.view.$cancelBtn.show();

            OW.activateNode(this.view.$mySwitch);
            this.view.$mySwitch.hide();
            this.fitWindow({width: 600});

            this.updateMyScroll();
        };

        this.updateMyScroll = function( toTop )
        {
            var self = this;

            this.view.$myPanel.css("overflow", "hidden");
            window.setTimeout(function()
            {
                var scrollApi;

                self.view.$myPanel.css("overflow", "auto");
                scrollApi = OW.addScroll(self.view.$myPanel);
                if ( toTop )
                {
                    scrollApi.scrollToY(0, false);
                }
            });
        };

        this.bindMyVideos = function( $items )
        {
            var self = this;

            $items.each(function()
            {
                var $node = $(this);

                if ( !$node.is('.AVM_Video') ) return;

                $node.find('.AVM_ItemThumb').click(function()
                {
                    self.showMyItemVideo($node);
                });

                $node.click(function()
                {
                    self.resetMyItems($node);
                    $node.addClass('avm-active').addClass('AVM_Active');
                    self.view.$mySave.show();
                });
            });
        };

        this.resetMyItems = function( item )
        {
            if ( item )
            {
                this.view.$myPanelList.find('.AVM_Video').not(item).removeClass('avm-active').removeClass('avm-item-video-view').removeClass('AVM_Active');
                this.view.$myPanelList.find('.AVM_ItemVideo').not(item.find('.AVM_ItemVideo')).empty().hide();
                this.view.$myPanelList.find('.AVM_ItemThumb').not(item.find('.AVM_ItemThumb')).show();
            }
            else
            {
                this.view.$myPanelList.find('.AVM_Video').removeClass('avm-active').removeClass('avm-item-video-view').removeClass('AVM_Active');
                this.view.$myPanelList.find('.AVM_ItemVideo').empty().hide();
                this.view.$myPanelList.find('.AVM_ItemThumb').show();
            }
        };

        this.showMyItemVideo = function( item )
        {
            var embed, video, $video;
            $video = item.find('.AVM_ItemVideo');
            embed = item.find('.AVM_Embed').val();
            item.addClass("avm-item-video-view");
            item.find('.AVM_ItemThumb').hide();
            $video.html(embed).show();
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

            if ( command == 'saveMyVideo' )
            {
                OW.inProgressNode(this.view.$mySave.find('input'));
            }

            if ( command == 'getMyVideos' )
            {
                this.view.$mySwitch.removeClass('ow_ic_video');
                this.view.$mySwitch.addClass('ow_preloader');
                OW.inProgressNode(this.view.$mySwitch);

                this.view.$myPanelListVMLabel.addClass('ow_preloader');
                OW.inProgressNode(this.view.$myPanelListVM);
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

            if ( command == 'saveMyVideo' )
            {
                OW.activateNode(this.view.$mySave.find('input'));
            }

            if ( command == 'getMyVideos' )
            {
                this.view.$mySwitch.addClass('ow_ic_video');
                this.view.$mySwitch.removeClass('ow_preloader');
                OW.activateNode(this.view.$mySwitch);

                OW.activateNode(this.view.$myPanelListVM);
                this.view.$myPanelListVMLabel.removeClass('ow_preloader');
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

            if ( command == 'saveMyVideo' )
            {
                this.delegate.setResult(response.oembed);
                this.delegate.setContent(response.content);
                this.delegate.save();

                this.delegate.floatBox.close();

                return;
            }

            if ( response.content )
            {
                this.view.renderContent(response.content, this.view.$result);

                this.view.$views.hide();
                this.view.$result.show();

                this.view.$mySwitch.hide();
                this.view.$mainControls.hide();
                this.view.$cancelBtn.show();

                this.fitWindow(response.fb || {});
            }

            if ( response.content && command == 'videoRenderEmbed' )
            {
                this.view.$saveBtn.show();
            }

            if ( response.myPanel )
            {
                this.showMyPanel(response.myPanel);
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

    ATTACHMENTS.VideoPanel.prototype = new ATTACHMENTS.VideoPanel.PROTO();


    ATTACHMENTS.YouTubeList = function( uniqId, glob )
    {
        var self = this,
            data = glob.data, searchDelegate,
            busy = false;

        this.uniqId = uniqId;
        this.delegate = CORE.ObjectRegistry[glob.delegate];

        this.ajax = new CORE.AjaxModel(glob.rsp, this);
        this.view = new CORE.View(document.getElementById(this.uniqId));

        UTILS.addInvitation(this.view.$('input:text[inv],textarea[inv]'));

        this.view.$list = this.view.$('.YT_List');

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



    ATTACHMENTS.LinkPanel = function(uniqId, glob)
    {
        var self = this;

        this.processedLink = false;
        this.shown = false;
        this.busy = false;
        this.delegate = CORE.ObjectRegistry[glob.delegate];

        this.delegate.linkPanel = this;

        this.uniqId = uniqId;
        this.glob = glob;

        this.ajax = new CORE.AjaxModel(this.glob.rsp, this);
        this.view = new CORE.View(document.getElementById(this.uniqId));

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

        //Manual add
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

    ATTACHMENTS.LinkPanel.PROTO = function()
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

    ATTACHMENTS.LinkPanel.prototype = new ATTACHMENTS.LinkPanel.PROTO();



    ATTACHMENTS.Attachment = function(uniqId, delegate)
    {
        var self = this;

        this.delegate = CORE.ObjectRegistry[delegate];
        this.uniqId = uniqId;
        this.node = document.getElementById(this.uniqId);
        this.onChange = function(){};

        //OW.resizeImg(this.$('.EQ_AttachmentImageC'),{width:'150'});

        this.$('.ATT_SelectPicture').click(function()
        {
            self.showImageSelector();
        });
    };


    ATTACHMENTS.Attachment.PROTO = function()
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
    ATTACHMENTS.Attachment.prototype = new ATTACHMENTS.Attachment.PROTO();

    ATTACHMENTS.playVideoCallback = function( node )
    {
        var v = $(node).hide().next('.ATT-Video-Player');
        v.show().html(v.find('textarea').val());
    };

// ------------------------------ </ Attachments > ------------------------------


// ------------------------------ < User Selector > ------------------------------

    UI = {};

    UI.UserSelectorLuncher = function()
    {
        this.uniqId = CORE.uniqId('ull');
        CORE.ObjectRegistry[this.uniqId] = this;

        this.observer = new CORE.Observer(this);

        this.fb = null;
    };

    UI.UserSelectorLuncher.PROTO = function()
    {
        this.show = function( entityId, allMode, fb )
        {
            entityId = entityId || null;
            allMode = allMode || null;
            fb = fb || {};

            var self = this, fbSettings = $.extend({},{
                iconClass: "ow_ic_user",
                addClass: 'ul_floatbox_canvas',
                title: QUESTIONS.friendMode ? OW.getLanguageText('equestions', 'selector_title_friends') : OW.getLanguageText('equestions', 'selector_title_users'),
                width: 600
            }, fb );

            this.fb = OW.ajaxFloatBox('EQUESTIONS_CMP_UserSelector', [this.uniqId, entityId, allMode], fbSettings);

            this.fb.$container.addClass('ul-floatbox');

            var winHeight = jQuery(window).height();
            var top = jQuery(window).height() < 700 ? (winHeight / 10) + 10 : false;

            if ( top )
            {
                this.fb.$container.css("margin-top", top);
            }

            if ( this.fb.$preloader )
            {
                this.fb.$preloader.addClass('q-floatbox-preloader');
            }

            this.trigger('show', {
                floatBox: this.fb
            });

            this.fb.bind('close', function()
            {
                self.trigger('close');
            });
        };

        this.close = function()
        {
            if ( this.fb )
            {
                this.fb.close();
            }
        };

        this.bind = function( eventName, callback, context )
        {
            this.observer.bind(eventName, callback, context);
        };

        this.trigger = function( eventName, eventObj )
        {
            this.observer.trigger(eventName, eventObj);
        };
    };

    UI.UserSelectorLuncher.prototype = new UI.UserSelectorLuncher.PROTO();



    UI.UserSelector = function( uniqId, data, glob, cache )
    {
        var self = this;

        this.uniqId = uniqId;
        this.glob = glob;
        this.data = data;
        this.searching = false;
        this.searchTimeOut = false;

        UI.UserSelector.CACHE.mergeState(cache);

        this.delegate = CORE.ObjectRegistry[glob.delegate];

        this.ajax = new CORE.AjaxModel(glob.rsp, this)
        this.view = new CORE.View(document.getElementById(this.uniqId));
        this.view.$filterInput = this.view.$('.UL_FilterInput');
        this.view.$filterC = this.view.$('.UL_FilterC');
        this.view.$listWrap = this.view.$('.UL_ListWrap');
        this.view.$list = this.view.$('.UL_List');

	var winHeight = jQuery(window).height();
        var height = winHeight - 300;
        this.view.$listWrap.css('height', height);
        this.view.$overlay = this.view.$('.UL_Overlay');

        this.view.$items = function()
        {
            return self.view.$('.UL_List .UL_Item');
        };

        this.view.$kw = this.view.$('.UL_ResultKW');
        this.view.$itemProto = this.view.$('.UL_Templates .UL_Item');
        this.view.$noResult = this.view.$('.UL_NoResult');
        this.view.$count = this.view.$('.UL_SelectedCount');
        this.view.$save = this.view.$('.UL_Save input');
        this.view.$clear = this.view.$('.UL_Clear');
        this.view.$switch = this.view.$('.ON_AllUsers');
        this.view.$selectAll = this.view.$('.UL_SelectAll');

        UTILS.addInvitation(this.view.$filterInput);

        this.bindItems(this.view.$items());

        this.view.$filterInput.on('input keyup', function() //TODO Refactor when IE will support onInput event
        {
            self.filterList();
        });

        this.view.$save.click(function()
        {
            if( !self.save() )
            {
                OW.warning('No users selected');
            }
        });

        this.view.$clear.click(function()
        {
            self.view.$filterInput.val('');
            self.filterList();
            self.view.$filterInput.focus();
        });

        this.delegate.trigger('load', {
            userSelector: this
        });

        this.view.$switch.click(function(){
            var height = self.delegate.fb.$body.height();
            self.delegate.close();
            self.delegate.show(self.data.entityId, true);
        });

        UI.UserSelector.CACHE.observer.bind('change', function()
        {
            self.filterList();
        });

        var listHeight = self.view.$list.height();


        this.updateScroll();

        this.view.$listWrap.on('jsp-arrow-change', function( event, isAtTop, isAtBottom, isAtLeft, isAtRight )
        {
            if ( self.isListFull )
            {
                return;
            }

            if ( isAtBottom )
            {
                self.showFromCache(30);
            }
        });

        /*this.view.$list.scroll(function( e )
        {
            if ( !this.scrollTop ) return;

            if ( this.scrollTop + this.clientHeight == this.scrollHeight )
            {
                self.showFromCache(30);
            }
        });*/

        this.view.$selectAll.click(function()
        {
            self.lockSelector(this.checked);
        });
    };

    UI.UserSelector.PROTO = function()
    {
        this.updateScroll = function( toTop )
        {
            var self = this;

            this.view.$listWrap.css("overflow", "hidden");
            window.setTimeout(function()
            {
                var scrollApi;

                self.view.$listWrap.css("overflow", "auto");
                scrollApi = OW.addScroll(self.view.$listWrap);
                if ( toTop )
                {
                    scrollApi.scrollToY(0, false);
                }
            });
        };


        this.queryState = function(kw)
        {
            if ( !this.data.ajaxMode )
            {
                return;
            }

            if ( !$.trim(kw) || UI.UserSelector.CACHE.isSearched(kw) )
            {
                return;
            }

            var self = this;

            this.searching = true;

            this.searchTimeOut = window.setTimeout(function()
            {
                self.ajax.query('userSearch', {
                    "data": self.data,
                    "kw": kw
                });

            }, 300);
        };

        this.ajaxStart = function( command, query )
        {
            if ( command == 'userSearch' )
            {
                UI.UserSelector.CACHE.addKeyword(query.kw);
                this.searching = true;
                this.view.$filterC.removeClass('ow_ic_lens').addClass('ow_preloader');
            }
        };

        this.ajaxEnd = function( command )
        {
            if ( command == 'userSearch' )
            {
                this.searching = false;
                this.view.$filterC.addClass('ow_ic_lens').removeClass('ow_preloader');
            }
        };

        this.ajaxSuccess = function( command, responce )
        {
            if ( command == 'userSearch' )
            {
                this.searching = false;
                UI.UserSelector.CACHE.mergeState(responce);
            }
        };

        this.bindItems = function( items )
        {
            var self = this;

            items.click(function()
            {
                if ( $(this).hasClass('UL_ItemSelected') )
                {
                    self.deselectItem($(this));
                }
                else
                {
                    self.selectItem($(this));
                }
            });
        };

        this.save = function()
        {
            if ( this.view.$selectAll.length && this.view.$selectAll.get(0).checked )
            {
                this.delegate.trigger('save', {
                    ids: [],
                    all: true
                });

                return true;
            }

            var ids = [];
            this.view.$('.UL_ItemCheck:checked').each(function(){
                ids.push(this.value);
            });

            if ( !ids.length )
            {
                return false;
            }

            this.delegate.trigger('save', {
                ids: ids
            });

            return true;
        };

        this.getItem = function( data )
        {
            var node;

            node = document.getElementById('li-' + data.userId);

            if ( node )
            {
                return $(node);
            }

            var item = this.view.$itemProto.clone();
            item.attr('id', 'li-' + data.userId);
            item.find('.UL_ItemCheck').attr('value', data.userId);
            item.attr('id', 'li-' + data.userId);
            item.find('.UI_ItemAvatar img').attr('src', data.src).attr('title', data.title);
            item.find('.UI_ItemTitle').text(data.title);

            this.view.$list.append(item);

            this.bindItems(item);

            return item;
        };

        this.filterList = function()
        {
            var val = this.view.$filterInput.val();
            var inv = this.view.$filterInput.attr('inv');
            var $items = this.view.$items();
            val = ( !val || val == inv ) ? '' : val.toLowerCase();

            if ( this.searchTimeOut )
            {
                window.clearTimeout(this.searchTimeOut);
                //this.searching = false;
            }

            if ( val )
            {
                this.view.$clear.show();
            }
            else
            {
                this.view.$clear.hide();
                $items.show();
                this.updateScroll();
                this.view.$noResult.hide();

                return;
            }

            $items.hide();

            var kw = val.replace(/"/g, " ").replace(/'/g, " ");

            this.queryState(kw);

            var searchResult = this.findInCache(kw);

            for ( var i = 0; i < searchResult.length; i++ )
            {
                this.getItem(searchResult[i]).show();
            }

            this.view.$kw.text(val);

            if ( searchResult.length )
            {
                this.view.$noResult.hide();
            }
            else if ( !this.searching )
            {
                this.view.$noResult.show();
            }

            this.updateScroll();
        };

        this.findInCache = function( kw )
        {
            var out = [], cache;
            cache = UI.UserSelector.CACHE.getState();

            $.each(cache, function(id, item)
            {
                if ( item.kw.search(kw) === 0 )
                {
                    out.push(item);
                }
            });

            return out;
        };

        this.showFromCache = function( count )
        {
            var self = this, i = 0, cache = UI.UserSelector.CACHE.getState();

            $.each(cache, function(id, item)
            {
                var node = document.getElementById('li-' + item.userId);
                if ( !node )
                {
                    i++;
                }

                self.getItem(item).show();

                if ( i >= count ) return false;
            });

            this.updateScroll();
        };

        this.selectItem = function( item )
        {
            item.addClass('ul-item-selected').addClass('UL_ItemSelected');
            var id = item.find('.UL_ItemCheck').attr('checked', true).attr('value');

            this.delegate.trigger('select', {
                id: id
            });

            this.countSelected();
        };

        this.deselectItem = function( item )
        {
            item.removeClass('ul-item-selected').removeClass('UL_ItemSelected');
            item.find('.UL_ItemCheck').attr('checked', false);

            var id = item.find('.UL_ItemCheck').attr('checked', false).attr('value');

            this.delegate.trigger('deselect', {
                id: id
            });

            this.countSelected();
        };

        this.countSelected = function()
        {
            var count = this.view.$('.UL_ItemSelected').length;
            this.view.$count.text(count);

            if ( count > 0 )
            {
                this.view.$count.show();
            }
            else
            {
                this.view.$count.hide();
            }
        };

        this.lockSelector = function( lock )
        {
            if ( lock )
            {
                this.view.$overlay.show();
                this.view.$filterInput.attr('disabled', true);
            }
            else
            {
                this.view.$overlay.hide();
                this.view.$filterInput.attr('disabled', false);
            }
        }
    };

    UI.UserSelector.prototype = new UI.UserSelector.PROTO();


    UI.UserSelector.State = function( data )
    {
        data = data || {};
        this.state = data;

        this.observer = new CORE.Observer(this);

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
    };

    UI.UserSelector.State.prototype = new CORE.State.PROTO();

    UI.UserSelector.CACHE = new UI.UserSelector.State();

// ------------------------------ </ User Selector > ------------------------------


// ------------------------------ < Webcam plugin > ------------------------------
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
	swf_url: 'webcam.swf', // URI to webcam.swf movie (defaults to cwd)
	shutter_url: 'shutter.mp3', // URI to shutter.mp3 sound
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

// ------------------------------ </ Webcam plugin > ------------------------------

    window.QUESTIONS_Loaded = true;
}

window.EQAjaxLoadCallbacksRun = function()
{
    if ( window.ATTPAjaxLoadCallbackQueue )
    {
        $.each(window.ATTPAjaxLoadCallbackQueue, function(i, fnc)
        {
            fnc.call();
        })
    }
}
