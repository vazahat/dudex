var ivideoClip = function( params )
{
    this.params = params;    

    var self = this;

    $("#clip-mark-featured a").bind("click", function() {
        self.ajaxSetFeaturedStatus(this);
    });

    $("#clip-set-approval-staus a").bind("click", function() {
        self.ajaxSetApprovalStatus(this);
    });
    
    $("#clip-delete a").bind( "click", function() {
        if ( confirm(self.params.txtDelConfirm) )
        {
            self.ajaxDeleteClip();
        }
        else
        {
            return false;
        }
    });

    this.ajaxSetApprovalStatus = function( dom_element )
    {
        var status = $(dom_element).attr('rel');
        
        $.ajax({
            url: self.params.ajaxResponder,
            type: 'POST',
            data: {
                ajaxFunc: 'ajaxSetApprovalStatus', 
                id: self.params.id, 
                status: status
            },
            dataType: 'json',
            success: function(data) 
            {	        
                if ( data.result == true )
                {
                    var newStatus = status == 'approved' ? 'pending' : 'approved';
                    var newLabel = status == 'approved' ? self.params.txtDisapprove : self.params.txtApprove;
                    $(dom_element).html(newLabel);
                    $(dom_element).attr('rel', newStatus)
		            
                    OW.info(data.msg);
                }
                else if (data.error != undefined)
                {
                    OW.warning(data.error);
                }
            }
        });
    }
            
    this.ajaxSetFeaturedStatus = function( dom_element )
    {
        var status = $(dom_element).attr('rel');
        
        $.ajax({
            url: self.params.ajaxResponder,
            type: 'POST',
            data: {
                ajaxFunc: 'ajaxSetFeaturedStatus', 
                id: self.params.id, 
                status: status
            },
            dataType: 'json',
            success: function(data) 
            {           
                if ( data.result == true )
                {
                    var newStatus = status == 'remove_from_featured' ? 'mark_featured' : 'remove_from_featured';
                    var newLabel = status == 'remove_from_featured' ? self.params.txtMarkFeatured : self.params.txtRemoveFromFeatured;
                    $(dom_element).html(newLabel);
                    $(dom_element).attr('rel', newStatus)
                    
                    OW.info(data.msg);
                }
                else if (data.error != undefined)
                {
                    OW.warning(data.error);
                }
            }
        });
    }
       
    this.ajaxDeleteClip = function( )
    {        
        $.ajax({
            url: self.params.ajaxResponder,
            type: 'POST',
            data: {
                ajaxFunc: 'ajaxDeleteClip', 
                id: self.params.id
            },
            dataType: 'json',
            success: function(data) 
            {
                if ( data.result == true )
                {
                    OW.info(data.msg);
                    if (data.url)
                        document.location.href = data.url;
                }
                else if (data.error != undefined)
                {
                    OW.warning(data.error);
                }            	
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }
        });

    }
}