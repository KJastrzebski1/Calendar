/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(window).load(function () {
    /*
     date store today date.
     d store today date.
     m store current month.
     y store current year.
     */
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    /*
     Initialize fullCalendar and store into variable.
     Why in variable?
     Because doing so we can use it inside other function.
     In order to modify its option later.
     */

    var calendar = $('#calendar').fullCalendar(
            {
                /*
                 header option will define our calendar header.
                 left define what will be at left position in calendar
                 center define what will be at center position in calendar
                 right define what will be at right position in calendar
                 */
                header:
                        {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek,agendaDay'
                        },
                /*
                 defaultView option used to define which view to show by default,
                 for example we have used agendaWeek.
                 */
                defaultView: 'month',
                /*
                 selectable:true will enable user to select datetime slot
                 selectHelper will add helpers for selectable.
                 */
                selectable: true,
                selectHelper: true,
                /*
                 when user select timeslot this option code will execute.
                 It has three arguments. Start,end and allDay.
                 Start means starting time of event.
                 End means ending time of event.
                 allDay means if events is for entire day or not.
                 */
                select: function (start, end, allDay)
                {
                    /*
                     after selection user will be promted for enter title for event.
                     */
                    var title = prompt('Event Title:');
                    /*
                     if title is enterd calendar will add title and event into fullCalendar.
                     */
                    if (title)
                    {
                        calendar.fullCalendar('renderEvent',
                                {
                                    title: title,
                                    start: start,
                                    end: end,
                                    allDay: allDay
                                },
                                true // make the event "stick"
                                );
                    }
                    calendar.fullCalendar('unselect');
                },
                dayClick: function () {
                    calendar.fullCalendar('changeView', 'agendaDay');
                },
                /*
                 editable: true allow user to edit events.
                 */
                editable: true,
                /*
                 events is the main option for calendar.
                 for demo we have added predefined events in json object.
                 */
                events: [
                    {
                        title: 'All Day Event',
                        start: new Date(y, m, 1)
                    },
                    {
                        title: 'Long Event',
                        start: new Date(y, m, d - 5),
                        end: new Date(y, m, d - 2)
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: new Date(y, m, d - 3, 16, 0),
                        allDay: false
                    },
                    {
                        id: 999,
                        title: 'Repeating Event',
                        start: new Date(y, m, d + 4, 16, 0),
                        allDay: false
                    },
                    {
                        title: 'Meeting',
                        start: new Date(y, m, d, 10, 30),
                        allDay: false
                    },
                    {
                        title: 'Lunch',
                        start: new Date(y, m, d, 12, 0),
                        end: new Date(y, m, d, 14, 0),
                        allDay: false
                    },
                    {
                        title: 'Birthday Party',
                        start: new Date(y, m, d + 1, 19, 0),
                        end: new Date(y, m, d + 1, 22, 30),
                        allDay: false
                    },
                    {
                        title: 'Click for Google',
                        description: 'This is some google event',
                        start: new Date(y, m, 28),
                        end: new Date(y, m, 29),
                        url: 'http://google.com/'
                    }
                ],
                eventRender: function (event, element) {
                    element.append(event.description);
                },
                eventAfterAllRender: function () {

                }
            });
    events = calendar.fullCalendar('clientEvents');
    console.log(calendar.fullCalendar('clientEvents'));
    data = [];
    for (var i = 0, len = events.length; i < len; i++) {
        var endDate = null;
        var desc = null;
            if (events[i]['end'])
                endDate = events[i]['end']['_d'];
            if( events[i]['description']){
                desc = events[i]['description'];
            }
        data[i] =
                {
                    'title': events[i]['title'],
                    'allDay': events[i]['allDay'],
                    'id': events[i]['_id'],
                    'start': events[i]['start']['_d'],
                    'end': endDate,
                    'description' : desc
                };
                
    }

    console.log(data);
    
    $.post(ajax_object.ajax_url, {"data":data, "action": "my_action"}, function(response) {
		console.log('Got this from the server: ' + response);
	});
});

