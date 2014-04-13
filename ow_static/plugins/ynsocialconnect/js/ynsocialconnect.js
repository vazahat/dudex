var YNSocialConnect = {
	viewmore:function()
	{
		window.ciMailFloatBox = new OW_FloatBox({$title: '', width: 415, $contents:$('#opensocialconnect_holder_header_view_more') });
	}
	, opensopopup:function(pageURL, service)
	{
		// if(service == 'facebook'
			// || service == 'twitter'
			// || service == 'linkedin'
			// )
		// {
			// window.location.href = pageURL;	
		// } else 
		// {
			var w = 990;
			var h = 560;
			var title ="socialconnectwindow";
			var left = (screen.width/2)-(w/2);
			var top = (screen.height/2)-(h/2);
			var newwindow = window.open (pageURL, title, 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
			if (window.focus) {newwindow.focus();}
			return newwindow;
		// }
	}
	, disconnect:function(pageURL)
	{
		var result = window.confirm(OW.getLanguageText('ynsocialconnect', 'txt_confirm_disconnect_acc_linking'));
		if (result)
		{
			window.location.href = pageURL;
		}
	}
	, removeAvatar:function(url)
	{
		$('#ynsc_profile_picture').remove();
		var set = {};
		$.post( url, set );
	}
}
