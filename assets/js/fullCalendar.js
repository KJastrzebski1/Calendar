/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(window).load(function () {
   
    data = []; //data used in ajax
    
    calendar_id = 0; // calendar_id id, 0 -> not logged in
    calendar; // variable for calendar
    admin = true;
    $.ajax({
        'method': 'POST',
        'url': ajax_object.ajax_url,
        'data': {
            "action": "get_user"
        }
    })
            .done(function (response) {
                //if (response != 1)
                calendar_ids = response['id'];
                calendar_names = response['names'];
                super_admin = response['admin'];
                //if (super_admin) {

                    var select = $("<select id='admin-select'></select>");
                    $('#calendar').before(select);
                    $('#admin-select').css({'display': 'block'});
                    for (i = 0; i < calendar_ids.length; i++) {
                        $('#admin-select').prepend("<option value='" + calendar_ids[i] + "'>"+ calendar_names[i]+"</option>");
                    }
                    calendar_id = calendar_ids[calendar_ids.length-1];
                    
                //}else{
                 //   calendar_id = response['id'];
                //}
                console.log(response);
                admin = true;
                calendar = $('#calendar').fullCalendar(
                        {
                            header:
                                    {
                                        left: 'prev,next',
                                        center: 'title',
                                        right: 'month,agendaWeek,agendaDay'
                                    },
                            defaultView: 'month',
                            selectable: true,
                            selectHelper: true,
                            select: function (start, end)
                            {
                                if (admin) {
                                    var title = prompt('Event Title:');

                                    if (title)
                                    {
                                        new_event = {
                                            title: title,
                                            start: start,
                                            end: end
                                        };


                                        updateEvents(new_event).done(function (response) {
                                            new_event['post_id'] = response;
                                            console.log(new_event['post_id']);
                                            calendar.fullCalendar('renderEvent',
                                                    new_event,
                                                    true // make the event "stick"
                                                    );

                                        });

                                    }
                                }
                                calendar.fullCalendar('unselect');

                            },
                            editable: true,
                            eventRender: function (event, element) {
                                element.append(event.description);
                            },
                            eventDrop: function (event) {
                                if (admin) {
                                    console.log("Drop:" + event.start.format());
                                    updateEvents(event);
                                }
                            },
                            eventResize: function (event) {
                                if (admin) {
                                    console.log("Resize end: " + event.end.format());
                                    updateEvents(event);
                                }
                            },
                            eventAfterAllRender: function () {

                            },
                            eventClick: function (calEvent) {
                                if (admin) {

                                    $("#my_meta_box_ds").val(calEvent.start.format("YYYY-MM-DD"));
                                    $("#my_meta_box_ts").val(calEvent.start.format("HH:mm"));
                                    $("#my_meta_box_de").val(calEvent.end.format("YYYY-MM-DD"));
                                    $("#my_meta_box_te").val(calEvent.end.format("HH:mm"));
                                    $('#dialog').dialog({
                                        title: calEvent.title,
                                        width: 350,
                                        buttons: [
                                            {
                                                text: "OK",
                                                click: function () {
                                                    $(this).dialog("close");
                                                }
                                            },
                                            {
                                                text: "DEL",
                                                click: function () {
                                                    calendar.fullCalendar('removeEvents', calEvent._id);
                                                    //console.log(calEvent);
                                                    $.post(ajax_object.ajax_url, {"data": calEvent.post_id, "action": "delete_event"}, function (response) {
                                                        console.log(response);

                                                    });
                                                    $(this).dialog("close");
                                                }
                                            }
                                        ]
                                    });
                                }
                            }
                        });
                        getEvents();
                $("#admin-select").change(function () {
                    calendar.fullCalendar('removeEvents');
                    calendar_id = $("#admin-select option:selected").val();
                    console.log(calendar_id );
                    getEvents();
                });
            });

    function getEvents() {
        $.post(ajax_object.ajax_url, {"data": calendar_id , "action": "get_events"}, function (response) {
            console.log(response);
            for (i = 0; i < response.length; i++) {
                calendar.fullCalendar('renderEvent',
                        {
                            'post_id': response[i].ID,
                            'title': response[i].title,
                            'start': response[i].start.date,
                            'description': response[i].description,
                            'end': response[i].end.date
                        },
                        true
                        );

            }
        });
    }
    function updateEvents(events) {

        var endDate = null;
        var desc = null;
        post_id = null;
        if (events['end'])
            endDate = events['end'].format();
        if (events['description']) {
            desc = events['description'];
        }
        if (events['post_id']) {
            post_id = events['post_id'];
        }

        data2 =
                {
                    'post_id': post_id,
                    'title': events['title'],
                    'allDay': events['allDay'],
                    'id': events['_id'],
                    'start': events['start'].format(),
                    'end': endDate,
                    'description': desc
                };

        return ($.ajax({
            'method': "POST",
            'url': ajax_object.ajax_url,
            'data': {
                "data": data2,
                "action": "update_event"
            },
            'success': function (response) {
                console.log(response);
                post_id = response;

            }}));

    }
});

