$(window).load(function () {
    //console.log('widget');
    var widget_calendar;
    $.ajax({
        'method': 'POST',
        'url': ajax_object.ajax_url,
        'data': {
            "action"
                    : "get_user"
        }
    })
            .done(function (response) {
                widget_calendar = $('#widget_calendar').fullCalendar({
                    googleCalendarApiKey: ajax_object.api_key,
                    header: {
                        left: '',
                        center: 'title',
                        right: ''
                    },
                    theme: response['styling'],
                    //timezone: 'local',
                    editable: false

                });
                var calendar_id = response['id'][0];

                $.post(ajax_object.ajax_url, {"data": calendar_id, "action": "get_events"}, function (response) {
                    //console.log(response);
                    if (response instanceof Object) {
                        var offset = new Date().getTimezoneOffset();
                        var start = 0;
                        var end = 0;
                        for (i = 0; i < response.length; i++) {
                            start = new Date(response[i].start.date);
                            start = moment(start.getTime() - (offset * 1000 * 60));
                            end = new Date(response[i].end.date);
                            end = moment(end.getTime() - (offset * 1000 * 60));
                            //console.log(start.format());
                            widget_calendar.fullCalendar('renderEvent',
                                    {
                                        'post_id': response[i].ID,
                                        'title': response[i].title,
                                        'start': start.format(),
                                        'description': response[i].description,
                                        'end': end.format()
                                    },
                                    true
                                    );

                        }
                    } else {
                        widget_calendar.fullCalendar('addEventSource', {googleCalendarId: response});
                        google_calendar = true;
                        //console.log('elo');
                    }

                });
            });
    $(widget_calendar).click(function () {
        window.location = "alt-calendar/";
    });
});
        