function checkXPSD()
{
	var xpsd = $.cookie('ynsocialpublisher_xpsd'), found = false;
	if (xpsd)
	{
		var ar = xpsd.split(';');
		if (ar.length > 2)
		{
			found  = true;
			var key = ar[0], type = ar[1], id = ar[2];
			var data = {
				pluginKey : key,
				entityType : type,
				entityId : id
			};
			$.removeCookie('ynsocialpublisher_xpsd');
			var opt = {
				width : 620,
				height : 560,
				iconClass : 'ow_ic_user',
				title : '',
				onLoad : function()
				{
					//window.setTimeout(checkXPSD, 10000);
				}
			};
			OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', data, opt);
		}
	}

	if(!found){
		//window.setTimeout(checkXPSD, 3000);
	}
}

$().ready(function()
{
	//window.setTimeout(checkXPSD, 3000);
});
if(typeof NEWSFEED_Feed == 'function')
{
	NEWSFEED_Feed.prototype.loadNewItem =
		function(params, preloader, callback)
			{
				if ( typeof preloader == 'undefined' )
				{
					preloader = true;
				}

				var self = this;
				if (preloader)
				{
					var $ph = self.getPlaceholder();
					this.$listNode.prepend($ph);
				}
				this.loadItemMarkup(params, function($a) {
					this.$listNode.prepend($a.hide());

	                                if ( callback )
	                                {
	                                    callback.apply(self);
	                                }

					self.adjust();
					if ( preloader )
					{
						var h = $a.height();
						$a.height($ph.height());
						$ph.replaceWith($a.css('opacity', '0.1').show());
						$a.animate({opacity: 1, height: h}, 'fast');
					}
					else
					{
						$a.animate({opacity: 'show', height: 'show'}, 'fast');
					}
				});
				//console.log(params);
				var data = {
					pluginKey : 'newsfeed',
					entityType : 'user-status',
					entityId : params.entityId
				};

				var opt = {
						width : 620,
						height : 560,
						iconClass : 'ow_ic_user',
						title : '',
						onLoad : function()
						{
							//window.setTimeout(checkXPSD, 10000);
						}
					};

				//var userId = params.feedData.data.feedId;
				var cookieData = $.cookie('ynsocialpublisher_feed_data_' + params.entityId);
				if (cookieData)
				{
					var cookieArray = cookieData.split(';');
					//$.removeCookie('ynsocialpublisher_feed_data');
					//if ((data.entityType == cookieArray[1]) && (data.entityId == cookieArray[2]) && (params.feedData.data.feedId == cookieArray[3]))
					if (data.entityId == cookieArray[0])
					{
						OW.ajaxFloatBox('YNSOCIALPUBLISHER_CMP_Popup', data, opt);
					}
				}
				//checkXPSD();
			};
}
