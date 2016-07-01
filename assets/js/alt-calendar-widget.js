jQuery.noConflict();
jQuery(function ($) {
    $(window).load(function () {
        //console.log('widget');
        var userLang = ajax_object.lang;
        userLang = userLang.substr(0, 2);
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
                        lang: userLang,
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
                                start = NewDate(response[i].start.date);
                                start = moment(start.getTime() - (offset * 1000 * 60));
                                end = NewDate(response[i].end.date);
                                end = moment(end.getTime() - (offset * 1000 * 60));
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
                        }

                    });
                });
        $('#widget_calendar').click(function () {
            window.location = "alt-calendar/";
        });
        function NewDate(str) {
            var a = str.split(" ");
            var d = a[0].split("-");
            var t = a[1].split(":");
            return new Date(d[0], (d[1] - 1), d[2], t[0], t[1], t[2]);
        }
    });
});
