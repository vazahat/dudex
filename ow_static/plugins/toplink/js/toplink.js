jQuery(function($){
	
	$('#addsublink').click(function(){
		var t = $(this),
		nccore = $('#newchild-core').clone(),
		rnd = Math.round( Math.random() * 1000, 0 );
		
		nccore
			.removeAttr('id')
			.addClass('toplinkchild')
			.find('input')
			.removeAttr('disabled')
			.each(function(i,e){
				var mynm = /[^0-9].*(?=\_)/g.exec( $(e).attr('id') );
				console.log( mynm );
				$(e).attr({
					'id' : mynm[0]+'_'+rnd,
					'name' : mynm[0]+'[]'
				});
			});
		
		
		$('#newchild-core').before( nccore.show() );
		
		$('#no-child').hide();
		$('#newchild-core').prev().find('.removesublink').click(function(){
			var vv = $(this),
			ans = confirm('Are you sure?');
			vv.parents('tr').remove();
			/*
			if( $('.toplinkchild').length > 1 && ans ){
				vv.parents('tr').remove();
			}
			if( $('.toplinkchild').length == 1 ){
				$('#no-child').show();
			}
			*/
			return false;
		});
		
		return false;
	});
	
	$('.removesublink').click(function(){
		var vv = $(this),
		ans = confirm('Are you sure?');
		vv.parents('tr').remove();
		/*
		if( $('.toplinkchild').length > 1 && ans ){
			
		}
		if( $('.toplinkchild').length == 1 ){
			$('#no-child').show();
		}
		*/
		return false;
	});
	
});