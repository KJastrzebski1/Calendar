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
                var calendar_ids = response['id'];
                var calendar_names = response['names'];
                var super_admin = response['admin'];
                var logged_in = response['logged_in'];
                if (logged_in) {

                    var select = $("<select id='admin-select'></select>");
                    $('#calendar').before(select);
                    $('#admin-select').css({'display': 'inline-block'});
                    for (i = 0; i < calendar_ids.length; i++) {
                        $('#admin-select').prepend("<option value='" + calendar_ids[i] + "'>" + calendar_names[i] + "</option>");
                    }
                    $('#admin-select').after($('<i id="remove-calendar" class="fa fa-times" aria-hidden="true"></i>'))
                    $('#admin-select').after($('<i id="add-calendar" class="fa fa-plus"></i>'));

                    calendar_id = calendar_ids[calendar_ids.length - 1];

                } else {
                    calendar_id = response['id'];
                }
                // console.log(response);

                calendar = $('#calendar').fullCalendar(
                        {
                            googleCalendarApiKey: '151948062187-b7ohe959qgun7eefl3cubhrnrlf7g6ae.apps.googleusercontent.com',
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

                                if (logged_in) {
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
                                                    calEvent.description = $("#my_meta_box_desc").val();
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
                            editable: logged_in,
                            eventRender: function (event, element) {
                                element.append(event.description);
                            },
                            eventDrop: function (event) {
                                if (logged_in) {
                                    console.log("Drop:" + event.start.format());
                                    event.calendar_id = calendar_id;
                                    updateEvents(event);
                                }
                            },
                            eventResize: function (event) {
                                if (logged_in) {
                                    console.log("Resize end: " + event.end.format());
                                    event.calendar_id = calendar_id;
                                    updateEvents(event);
                                }
                            },
                            eventAfterAllRender: function () {

                            },
                            eventClick: function (calEvent) {
                                if (logged_in) {

                                    $("#my_meta_box_ds").val(calEvent.start.format("YYYY-MM-DD"));
                                    $("#my_meta_box_ts").val(calEvent.start.format("HH:mm"));
                                    $("#my_meta_box_de").val(calEvent.end.format("YYYY-MM-DD"));
                                    $("#my_meta_box_te").val(calEvent.end.format("HH:mm"));
                                    $("#my_meta_box_desc").val(calEvent.description);
                                    $('#dialog').dialog({
                                        title: calEvent.title,
                                        width: 350,
                                        autoOpen: true,
                                        buttons: [
                                            {
                                                text: "OK",
                                                click: function () {
                                                    //console.log($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                    //console.log($("#my_meta_box_de").val()+'T'+$("#my_meta_box_te").val());

                                                    calEvent.start = moment($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                    calEvent.end = moment($("#my_meta_box_de").val() + 'T' + $("#my_meta_box_te").val());
                                                    calEvent.title = title_input.val();
                                                    calEvent.calendar_id = calendar_id;
                                                    calEvent.description = $("#my_meta_box_desc").val();
                                                    console.log($("#my_meta_box_desc").val());
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
                // events from user actions
                if (logged_in) {


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
                    $("#add-calendar").click(function () {

                        $("#add-calendar").after("<div id='add-dialog'></div>");
                        var content = "<label>Calendar name:</label>";
                        content += "<input type='text' id='add-calendar-name' />";
                        content += "<input type='checkbox' id='add-calendar-checkbox' />Google ID";
                        content += "<input type='text' id='add-calendar-google-id' disabled />";
                        $("#add-dialog").html(content);

                        $("#add-dialog").dialog({
                            'title': 'Add new calendar',
                            'width': 350,
                            'buttons': [
                                {
                                    'text': 'OK',
                                    'click': function () {
                                        data = {'title': $("#add-calendar-name").val(),
                                            'google_id': $("#add-calendar-google-id").val()
                                        };
                                        console.log(data);
                                        newCalendar(data);
                                        $(this).dialog("close");
                                    }
                                }
                            ]
                        });

                    });
                    $("#remove-calendar").click(function () {
                        $("#remove-calendar").after("<div id='remove-dialog'></div>");
                        var title = $("#admin-select option:selected").text();
                        var content = "Are you sure you want to delete calendar: " + title + "?";
                        $("#remove-dialog").html(content);
                        $("#remove-dialog").dialog({
                            'title': 'Remove ' + title,
                            'buttons': [
                                {
                                    'text': "Yes",
                                    'click': function () {
                                        $.post(ajax_object.ajax_url, {"data": calendar_id, "action": 'remove_calendar'}, function (response) {
                                            console.log(response);
                                        });
                                        $(this).dialog('close');
                                    }
                                },
                                {
                                    'text': 'No',
                                    'click': function () {
                                        $(this).dialog('close');
                                    }
                                }
                            ]
                        });
                    });
                    $(document).on('click', '#add-calendar-checkbox', function () {
                        if ($("#add-calendar-checkbox").prop('checked')) {
                            $("#add-calendar-google-id").prop('disabled', false);
                        } else {
                            $("#add-calendar-google-id").prop('disabled', true);
                        }
                    });
                }
            });
    function newCalendar(ncal) {
        console.log(ncal);
        $.post(ajax_object.ajax_url, {"data": ncal, "action": 'new_calendar'}, function (response) {
            console.log(response);
        });
    }
    function getEvents() {
        $.post(ajax_object.ajax_url, {"data": calendar_id, "action": "get_events"}, function (response) {
            console.log(response);
            var start = 0;
            var end = 0;
            for (i = 0; i < response.length; i++) {
                start = new Date(response[i].start.date);
                start = moment(start.getTime() - (offset * 1000 * 60));
                end = new Date(response[i].end.date);
                end = moment(end.getTime() - (offset * 1000 * 60));
                //console.log(start.format());
                calendar.fullCalendar('renderEvent',
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
        });
    }
    function updateEvents(events) {

        var endDate = null;
        var desc = null;
        var post_id = null;
        if (events['end'])
            endDate = events['end'] + (offset * 1000 * 60);
        if (events['description']) {
            desc = events['description'];
        }
        if (events['post_id']) {
            post_id = events['post_id'];
        }
        var start = events['start'] + (offset * 1000 * 60);
        console.log(events);

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

