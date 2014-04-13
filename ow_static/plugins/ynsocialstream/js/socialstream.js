

$(document).ready(function() {	
	if($('.ow_attachment_icons #nfa-feed1 span.buttons').length)
	{
		//var owAttachment = new OwAttachment();
		//owAttachment.showLoader();
		
		$('.ow_attachment_icons #nfa-feed1 span.buttons').append("<a id=\"get_feed_fb\" rel=\"facebook\" href=\"javascript:;\" style=\"\"><img src=\"http://dev2.younetco.com/oxwall/luannd/ow_static/plugins/yncontactimporter/img/facebook.png\" width=\"30px\" height=\"30px\" style=\"max-width: 100%;\"></a>");
		
		$("#get_feed_fb").click(function(){			
			$('.ow_submit_auto_click').hide();
		    $('#attachment_preview_nfa-feed1').show();
		    $('#attachment_preview_nfa-feed1').empty().addClass('attachment_preloader').animate({height:45});
		});
	}
	
	
});

