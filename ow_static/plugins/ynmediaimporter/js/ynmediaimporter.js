var YnMediaImporter = {
    request : null,
    emptyAjaxQueue : function() {
        if (YnMediaImporter.request) {
            YnMediaImporter.request.cancel();
        }
    },
    getHash : function() {
        var match = document.location.href.match(/#!.+$/);
        if (match) {
            return match.pop().replace('#!', '').parseQueryString();
        }
        return false;
    },
    toHash : function(params) {
        document.location.href = document.location.href.replace(/#!.+$/, '') + '#!' + (new Hash(params)).toQueryString();
    },
    openSmoothBox : function(href) {
        // create an element then bind to object
        var a = new Element('a', {
            href : href,
            'style' : 'display:none'
        });
        var body = document.getElementsByTagName('body')[0];
        a.inject(body);
        Smoothbox.open(a);
    },
    selectAll : function() {
    	$('.ynmediaimporter_checkbox').attr("checked","checked");
    },
    unselectAll : function() {
    	$('.ynmediaimporter_checkbox').removeAttr("checked");
    },
    getSelected : function() {
        var rows = [];
        $('.ynmediaimporter_checkbox').each(function() {
        	var elm = $(this);
            if (elm.is(':checked')) {
                rows.push({
                    id : elm.attr('value'),
                    data : elm.attr('data-cache'),
                    media : elm.attr('media'),
                    provider : elm.attr('provider')
                });
            }
        });
        return rows;
    },
    importMedia : function(rows) {
    	function do_cancel(){
		   
		}
		function do_callback(json) {
			
		    
		}
    		
        if ( typeof rows == 'undefined') {
            rows = this.getSelected();
        }
        if (!rows.length) {
            alert("There is no selected!");
            return 0;
        }
        //console.log(JSON.stringify(rows)); return;
        var $contents = "";
        $("#ynmediaimporter_btn_import_selected").removeClass("ow_ic_save").css("background-image", "url(" + YnMediaImporter.ajaxImageUrl + ")");
    	$.ajax({
        	url: YnMediaImporter.moduleUrl + '/postimport',
        	type: 'post',
        	dataType: "json",
        	data : {
    			json : JSON.stringify(rows)
    		},
        	success: function(text){
        		var json = text;
        		//$('#buttons-wrapper').style.display = 'block';
    		    if(json.numphoto==0){
    		        window.setTimeout(do_cancel,3000 );
    		        //$('#buttons-wrapper').style.display = 'none';
    		    }else if (json.album_count) {
    				//$('#form-check').action = en4.core.baseUrl + 'ynmediaimporter/import/albums/format/smoothbox';
    			} else if (json.photo_count) {
    				// alert('photo count' + json.photo_count);
    			} else {
    			    //$('#buttons-wrapper').style.display = 'none';
    				window.setTimeout(do_cancel, 3000);
    			}
    		    //$contents += json.message + "<br />";
    		    $contents += json.form;
    		    
    		    window.edit_photo_floatbox = new OW_FloatBox({
    	            $title: 'Adding photos',
    	            $contents: $contents,
    	            icon_class: 'ow_ic_edit',
    	            width: 600
    	        });
    		    $("#ynmediaimporter_add_photo_message").html(json.message);
    		    
	            var $input = $("#ynmediaimporter_album_suggest");
	            $input.val(json.album_name);
	            var initialLabel = $input.val();
	            $input.parent().find(".ow_suggest_invitation").click(function(){ $input.focus(); });
	            
	            $input.suggest(json.responder_url, {
	            	autoSuggest: true, minchars: '1', 
	            	
	            	onFocus: function(first){
	                    $(this).removeClass("ow_inputready");
	                    if ($(this).val() == initialLabel) {
	                        $(this).val("");
	                    }
	                }, 
	                
	                onBlur: function(){
	                    var v = $(this).val();
	                    
	                    if ( !$.trim(v) ) {
	                        $(this).val(initialLabel);
	                        $(this).addClass("ow_inputready");
	                        return 0;
	                    }
	                }, 
    	                
	                onAutoSuggest: function(v){
	                    if (v) {
	                        return false;
	                    }
	                }
	            });

    		    
    		    
    		    return false;
    			//$('#ynmediaimporter_json_data').value =  JSON.stringify(json);
    			//$('#message_stage').innerHTML = json.message;
    			//$('#message_stage').style.display = 'block';
        	}
        }).done(function(data) {
        	$("#ynmediaimporter_btn_import_selected").css("background-image", "").addClass("ow_ic_save");
        });

        

        
        
        
        
        //console.log(url);
        //console.log(url + '?format=smoothbox');
        // open waiting box.
        //this.openSmoothBox(url + '?' + hash.toQueryString());
    },
    updatePage : function(url) {
        url = YnMediaImporter.getDataUrl + '?' + url;
        this.updateBrowse({}, url);
    },
    refresh : function(cache) {
        var data = this.lastData.json;
        if ( typeof cache != 'undefined' && cache) {
            data['remove-cache'] = 1;
        }
        this.updateBrowse(data, this.lastData.url);
    },
    viewMore : function(json, url) {
        json.noControl = 1;
        function loading() {
            var wrapper = $('#feed_viewmore');
            if ( typeof wrapper != 'undefined') {
                var html = '<div class="ynmediaimporter_viewmore_loading">{loading}</div>';
                html = html.replace('{loading}', 'Loading ...');
                wrapper.html(html);
            }
        }
        
        loading();
        if ( typeof url == 'undefined') {
            url = YnMediaImporter.getDataUrl;
        }
        if ( typeof json.offset != 'undefined') {
            json.offset = parseInt(json.offset) + parseInt(json.limit);
        }
        $.ajax({
        	url: url,
        	type: 'get',
        	dataType: "json",
        	data : json,
        	success: function(text){
        		try {
                    if (text != '') {
                        if (text.message != '') 
                        {
                            // in most case of unix time we must to reload page for some thing
                            window.location.href = YnMediaImporter.moduleUrl + '/connect/facebook';
                        } else {
                            var wrapper = $('.ynmeidaimporter_result_holder').last();
                            if ( typeof wrapper != 'undefined') {
                                wrapper.html(text.html);
                            }
                        }
                    }
                } catch(e) {
                    alert('There are an error occur, please refresh(F5) this page!');
                }
        	}
        }).done(function(data) {
        	//do s.t
        });
        
       /*
        var request = new Request({
            url : url,
            method : 'get',
            data : json,
            onSuccess : function(text) {
                try {
                    if (text != '') {
                        var json = JSON.decode(text);
                        if (json.message != '') 
                        {
                            // in most case of unix time we must to reload page for some thing
                            window.location.href = en4.core.baseUrl+'media-importer/connect/service/facebook';
                        } else {
                            var wrapper = $$('.ynmeidaimporter_result_holder').pop();
                            if ( typeof wrapper != 'undefined') {
                                wrapper.innerHTML = json.html;
                            }
                        }
                    }
                } catch(e) {
                    alert('There are an error occur, please refresh(F5) this page!');
                }
            }
        });
        this.emptyAjaxQueue();
        this.request = request;
        request.send();
        */
    },
    lastData : {
        url : null,
        json : null
    },
    updateBrowse : function(json, url) {
        function loading() {
            var wrapper = $('.layout_ynmediaimporter_media_browse');
            if ( typeof wrapper != 'undefined') {
                var html = '<div class="ynmediaimporter_loading_image" style="display:block;">&nbsp;</div><div><center>{loading}</center></div>';
                html = html.replace('{loading}', 'Loading ...');
                wrapper.html(html);
            }
        }
        
        loading();
        
        if ( typeof url == 'undefined') {
            url = YnMediaImporter.getDataUrl;
        }
		
        this.lastData = {
            url : url,
            json : json
        };
        
        /*
        var request = new Request({
            url : url,
            method : 'get',
            data : json,
            onSuccess : function(text) {
                
            }
        });
        */
        
        //this.emptyAjaxQueue();
        //this.request = request;
        //request.send();
        $.ajax({
			url: url,
			type: "get",
			data : json,
			dataType: "json",
			success: function(text){
				try {
                    if (text != '') {
                        var json = text;
                        if (json.message != '') {
                            window.location.href = YnMediaImporter.moduleUrl + '/connect/facebook';
                        } else {
                            var wrapper = $('.layout_ynmediaimporter_media_browse');
                            if ( typeof wrapper != 'undefined') {
                            	wrapper.html(json.html);
                            }
                        }
                    }
                } catch(e) {
                    alert('There are an error occur, please refresh(F5) this page!');
                }
			}
		});
    },
    
    checkAlbum : function(){
    	var elm = $("#ynmediaimporter_album_suggest");
    	if (typeof elm == 'undefined')
    		return false;
    	var album_name = elm.val();
    	if (album_name == ''){
    		$("#ynmediaimporter_album_require").css("display","block");
    		return false;
    	}
    	return true;
    }
};