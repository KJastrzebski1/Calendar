/*
 * 
 */
jQuery.noConflict();
jQuery(function ($) {
    $(window).ready(function () {
        var userLang = ajax_object.lang;
        userLang = userLang.substr(0, 2);
        var data = []; //data used in ajax
        var calendar_id = 0; // calendar_id id, 0 -> not logged in
        var calendar; // variable for calendar
        var google_calendar = false; // is calendar from google
        var title_input = $("<input type='text' id='input-title' class='ui-dialog-title' name='title'>");
        $.post(ajax_object.ajax_url, {"action": 'dialog_content'}, function (response) {
            $("#dialog").html(response);
        });
        $("#dialog").dialog({autoOpen: false});
        var offset = new Date().getTimezoneOffset();
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
                    var calendar_ids = response['id'];
                    var calendar_names = response['names'];
                    var styling = response['styling'];

                    var logged_in = response['logged_in'];
                    if (logged_in) {

                        var select = $("<select id='admin-select'></select>");
                        $('#calendar').before(select);
                        $('#admin-select').css({'display': 'inline-block'});
                        for (i = 0; i < calendar_ids.length; i++) {
                            if (i === 0) {
                                $('#admin-select').prepend("<option value='" + calendar_ids[i] + "' selected >" + calendar_names[i] + "</option>");
                            } else {
                                $('#admin-select').prepend("<option value='" + calendar_ids[i] + "'>" + calendar_names[i] + "</option>");
                            }

                        }
                        $('#admin-select').after($('<i id="remove-calendar" class="fa fa-times" aria-hidden="true"></i>'))
                        $('#admin-select').after($('<i id="add-calendar" class="fa fa-plus"></i>'));

                        calendar_id = calendar_ids[0];

                    } else {
                        calendar_id = response['id'][0];
                    }

                    calendar = $('#calendar').fullCalendar(
                            {
                                googleCalendarApiKey: ajax_object.api_key,
                                timezone: 'local',
                                lang: userLang,
                                header:
                                        {
                                            left: 'prev,next',
                                            center: 'title',
                                            right: 'month,agendaWeek,agendaDay'
                                        },
                                defaultView: 'month',
                                selectable: true,
                                selectHelper: true,
                                theme: styling,
                                select: function (start, end)
                                {

                                    if (logged_in && !google_calendar) {
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
                                                        calEvent.description = $("#my_meta_box_desc").val();

                                                        calEvent.start = moment($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                        calEvent.end = moment($("#my_meta_box_de").val() + 'T' + $("#my_meta_box_te").val());
                                                        updateEvents(calEvent).done(function (response) {
                                                            calEvent['post_id'] = response;
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
                                },
                                editable: logged_in,
                                eventRender: function (event, element) {
                                    element.append(event.description);
                                },
                                eventDrop: function (event) {
                                    console.log(event);
                                    if (logged_in) {
                                        event.calendar_id = calendar_id;
                                        updateEvents(event);
                                    }
                                },
                                eventResize: function (event) {
                                    if (logged_in) {
                                        event.calendar_id = calendar_id;
                                        updateEvents(event);
                                    }
                                },
                                eventAfterAllRender: function () {

                                },
                                eventClick: function (calEvent) {
                                    if (logged_in && !google_calendar) {

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
                                                        calEvent.start = moment($("#my_meta_box_ds").val() + 'T' + $("#my_meta_box_ts").val());
                                                        calEvent.end = moment($("#my_meta_box_de").val() + 'T' + $("#my_meta_box_te").val());
                                                        calEvent.title = title_input.val();
                                                        calEvent.calendar_id = calendar_id;
                                                        calEvent.description = $("#my_meta_box_desc").val();
                                                        //console.log(calEvent);
                                                        calendar.fullCalendar('updateEvent', calEvent);
                                                        updateEvents(calEvent);
                                                        $(this).dialog("close");
                                                    }
                                                },
                                                {
                                                    text: "DEL",
                                                    click: function () {
                                                        calendar.fullCalendar('removeEvents', calEvent._id);
                                                        $.post(ajax_object.ajax_url, {"data": calEvent.post_id, "action": "delete_event"}, function (response) {
                                                            //console.log(response);
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
                            getEvents();
                        });
                        $("#add-calendar").click(function () {

                            if ($('#add-dialog').length) {
                                $('#add-dialog').dialog('open');
                                $("#add-calendar-name").val('');

                            } else {
                                $("#add-calendar").after("<div id='add-dialog'></div>");
                                var content = "<label>Calendar name:</label>";
                                content += "<input type='text' id='add-calendar-name' />";
                                content += "<input type='checkbox' id='add-calendar-checkbox' />Google ID";
                                content += "<input type='text' id='add-calendar-google-id' disabled />";
                                $("#add-dialog").html(content);
                            }


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
                                            var data = {
                                                'calendar_id': calendar_id
                                            }
                                            $.post(ajax_object.ajax_url, {"data": data, "action": 'remove_calendar'}, function (response) {
                                                //console.log(response);
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

            $.post(ajax_object.ajax_url, {"data": ncal, "action": 'new_calendar'}, function (response) {
                $('#admin-select').prepend("<option selected value='" + response['term_id'] + "'>" + ncal['title'] + "</option>");
                calendar_id = response['term_id'];
                calendar.fullCalendar('removeEvents');
                getEvents();
            });
        }
        function getEvents() {
            $.post(ajax_object.ajax_url, {"data": calendar_id, "action": "get_events"}, function (response) {
                //console.log(response);
                if (response instanceof Object) {
                    var start = 0;
                    var end = 0;
                    google_calendar = false;
                    for (i = 0; i < response.length; i++) {
                        start = NewDate(response[i].start.date);
                        start = moment(start.getTime() - (offset * 1000 * 60));
                        end = NewDate(response[i].end.date);
                        end = moment(end.getTime() - (offset * 1000 * 60));
                        calendar.fullCalendar('renderEvent',
                                {
                                    "post_id": response[i].ID,
                                    "title": response[i].title,
                                    "start": start.format(),
                                    "description": response[i].description,
                                    "end": end.format()
                                },
                                true
                                );
                    }
                } else {
                    calendar.fullCalendar('addEventSource', {googleCalendarId: response});
                    google_calendar = true;
                }

            });
        }
        function NewDate(str) {
            var a = str.split(" ");
            var d = a[0].split("-");
            var t = a[1].split(":");
            return new Date(d[0], (d[1] - 1), d[2], t[0], t[1], t[2]);
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
                    post_id = response;
                }}));
        }
    });

});
