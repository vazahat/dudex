var eventxItem = function(params)
{
    this.params = params;

    var self = this;

    $("#eventx-set-approval-staus a").bind("click", function() {
        self.ajaxSetApprovalStatus(this);
    });

    $("#eventx-delete a").bind("click", function() {
        if (confirm(self.params.txtDelConfirm))
        {
            self.ajaxDeleteItem();
        }
        else
        {
            return false;
        }
    });

    this.ajaxSetApprovalStatus = function(dom_element)
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
                if (data.result == true)
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

    this.ajaxDeleteItem = function( )
    {
        $.ajax({
            url: self.params.ajaxResponder,
            type: 'POST',
            data: {
                ajaxFunc: 'ajaxDeleteItem',
                id: self.params.id
            },
            dataType: 'json',
            success: function(data)
            {
                if (data.result == true)
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
            error: function(request, status, error) {
                alert(request.responseText);
            }
        });

    }
}

var eventAddForm = function($params)
{
    var self = this;

    var $startTime = $('select[name=\'start_time\']');
    var $startDateValue = $('input[name=\'start_date\']');

    var $endTime = $('select[name=\'end_time\']');
    var $endDateValue = $('input[name=\'end_date\']');
    var $endDateSelectbox = $('select', $('#' + $params['tdId']));

    var end_date_id = $params['end_date_id'];


    $('#' + $params['checkbox_id']).click(
            function() {
                if ($(this).attr('checked'))
                {
                    var $date = $startDateValue.val();

                    var regexp = /^(\d+)\/(\d+)\/(\d+)$/;
                    var matches = regexp.exec($date);

                    if (matches)
                    {
                        var day = matches[3];
                        var month = matches[2];
                        var year = matches[1];

                        var date = new Date();
                        date.setHours(0, 0, 0, 0);

                        if ($startTime.val() == 'all_day')
                        {
                            date.setFullYear(parseInt(year), parseInt(month) - 1, parseInt(day) + 1);
                            $endTime.val('all_day');
                        }
                        else if ($startTime.val())
                        {
                            var time = $startTime.val();

                            var timeRegexp = /^(\d+)\:(\d+)$/;

                            var matches1 = timeRegexp.exec(time);

                            if (matches1)
                            {
                                date.setFullYear(parseInt(year), parseInt(month) - 1, parseInt(day));
                                date.setHours(parseInt(matches1[1]) + 1, parseInt(matches1[2]), 0, 0);

                                $endTime.val(date.getHours() + ":" + date.getMinutes());
                            }
                        }

                        $('select[name=\'year_end_date\']').val(date.getFullYear());
                        $('select[name=\'month_end_date\']').val(date.getMonth() + 1);
                        $('select[name=\'day_end_date\']').val(date.getDate());

                        window.date_field[end_date_id].updateValue();
                    }



                    $endDateSelectbox.removeAttr('disabled').show();
                    $startTime.removeAttr('disabled');
                    $('#end_date_div').show();
                }
                else
                {
                    $endDateSelectbox.attr('disabled', 'disabled').hide();
                    $('#end_date_div').hide();
                }
            }
    );
}


