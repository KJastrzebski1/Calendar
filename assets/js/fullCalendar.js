/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(window).load(function () {

    var data = []; //data used in ajax

    var calendar_id = 0; // calendar_id id, 0 -> not logged in
    var calendar; // variable for calendar
    var admin = true;
    var title_input = $("<input type='text' id='input-title' class='ui-dialog-title' name='title'>");

    $("#dialog").dialog({autoOpen: false});
    var offset = new Date().getTimezoneOffset();
    console.log(offset);
    var title_span = $("#ui-id-1");
    $("#ui-id-1").before(title_input);
    $(title_input).hide();
    $.ajax({
        'method': 'POST',
        'url': ajax_object.ajax_url,
        'data': {
            "action": "get_user"
        }
    })
            .done(function (response) {
                //if (response != 1)
                console.log(response);
                calendar_ids = response['id'];
                calendar_names = response['names'];
                super_admin = response['admin'];
                //if (super_admin) {

                var select = $("<select id='admin-select'></select>");
                $('#calendar').before(select);
                $('#admin-select').css({'display': 'block'});
                for (i = 0; i < calendar_ids.length; i++) {
                    $('#admin-select').prepend("<option value='" + calendar_ids[i] + "'>" + calendar_names[i] + "</option>");
                }
                calendar_id = calendar_ids[calendar_ids.length - 1];

                //}else{
                //   calendar_id = response['id'];
                //}
                // console.log(response);
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
                            theme: false,
                            select: function (start, end)
                            {

                                if (admin) {


                                    calEvent = {
                                        'calendar_id': calendar_id,
                                        'title': 'new',
                                        'start': start,
                                        'end': end
                                    };
                                    $("#my_meta_box_ds").val(calEvent.start.format("YYYY-MM-DD"));
                                    $("#my_meta_box_ts").val(calEvent.start.format("HH:mm"));
                                    $("#my_meta_box_de").val(calEvent.end.format("YYYY-MM-DD"));
                                    $("#my_meta_box_te").val(calEvent.end.format("HH:mm"));
                                    $('#dialog').dialog({
                                        title: calEvent.title,
                                        width: 350,
                                        autoOpen: true,
                                        buttons: [
                                            {
                                                text: "OK",
                                                click: function () {
                                                    calEvent.title = title_input.val();

                                                    calEvent.start = new Date($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());

                                                    calEvent.end = new Date($("#my_meta_box_de").val() + 'T' + $("#my_meta_box_te").val());
                                                    updateEvents(calEvent).done(function (response) {
                                                        calEvent['post_id'] = response;
                                                        console.log(calEvent['post_id']);
                                                        calendar.fullCalendar('renderEvent',
                                                                calEvent,
                                                                true // make the event "stick"
                                                                );

                                                    });
                                                    $(this).dialog("close");
                                                }
                                            }
                                        ]
                                    });
                                    title_input.val(title_span.text());
                                }
                                //calendar.fullCalendar('unselect');

                            },
                            editable: true,
                            eventRender: function (event, element) {
                                element.append(event.description);
                            },
                            eventDrop: function (event) {
                                if (admin) {
                                    console.log("Drop:" + event.start.format());
                                    event.calendar_id = calendar_id;
                                    updateEvents(event);
                                }
                            },
                            eventResize: function (event) {
                                if (admin) {
                                    console.log("Resize end: " + event.end.format());
                                    event.calendar_id = calendar_id;
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
                                        autoOpen: true,
                                        buttons: [
                                            {
                                                text: "OK",
                                                click: function () {
                                                    console.log($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                    //console.log($("#my_meta_box_de").val()+'T'+$("#my_meta_box_te").val());

                                                    calEvent.start = moment($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                    calEvent.end = moment($("#my_meta_box_de").val() + 'T' + $("#my_meta_box_te").val());
                                                    calEvent.title = title_input.val();
                                                    calEvent.calendar_id = calendar_id;
                                                    calendar.fullCalendar('updateEvent', calEvent);
                                                    updateEvents(calEvent);
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
                                    title_input.val(title_span.text());

                                }
                            }
                        });
                getEvents();
                $("#ui-id-1").click(function () {
                    title_span.hide();
                    title_input.show();
                    title_input.focus();
                    title_input.val(title_span.text());
                });

                title_input.on('focusout', function () {

                    title_input.hide();
                    title_span.show();
                    $("#dialog").dialog('option', 'title', title_input.val());
                });
                $("#admin-select").change(function () {
                    calendar.fullCalendar('removeEvents');
                    calendar_id = $("#admin-select option:selected").val();
                    console.log(calendar_id);
                    getEvents();
                });
            });

    function getEvents() {
        $.post(ajax_object.ajax_url, {"data": calendar_id, "action": "get_events"}, function (response) {
            console.log(response);
            for (i = 0; i < response.length; i++) {
                calendar.fullCalendar('renderEvent',
                        {
                            'post_id': response[i].ID,
                            'title': response[i].title,
                            'start': response[i].start.date+response[i].timezone,
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
        var post_id = null;
        if (events['end'])
            endDate = events['end']+(offset*1000*60);
        if (events['description']) {
            desc = events['description'];
        }
        if (events['post_id']) {
            post_id = events['post_id'];
        }
        var start = events['start']+(offset*1000*60);
        //console.log('update:'+ moment(start).format());
        
        var data2 =
                {
                    'post_id': post_id,
                    'title': events['title'],
                    'allDay': events['allDay'],
                    'id': events['_id'],
                    'start': moment(start).format(),
                    'end': moment(endDate).format(),
                    'description': desc
                };

        return ($.ajax({
            'method': "POST",
            'url': ajax_object.ajax_url,
            'data': {
                "data": data2,
                "calendar_id": events['calendar_id'],
                "action": "update_event"
            },
            'success': function (response) {
                console.log(response);
                post_id = response;

            }}));

    }
});

