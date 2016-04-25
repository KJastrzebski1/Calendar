jQuery(document).ready(function ($) {
    function altCalendarsTable(user_id) {
        $.post(ajaxurl, {"data": user_id, "action": 'get_user'}, function (response) {
            //console.log(response);
        }).done(function (response) {
            var content = '<tr><th>ID</th><th>'+alt_var.name +'</th></tr>';
            for (i = 0; i < response['id'].length; i++) {
                content += '<tr><td>' + response['id'][i] + '</td><td>' + response['names'][i] + '</td><td>';
                if(i>0){
                    content += '<i class="fa fa-times remove-calendar" aria-hidden="true">';
                }else{
                    content += 'default';
                }
                content += '</i></td></tr>';
            }
            $("#alt-calendar-table").html((content));
            $("#user").remove();
            $('#add-calendar-label label:first').after('<label id="user" class="bold-label"> '+ $("#alt-user-select option:selected").text()+'</label>');
            $("#add-calendar-label").show();

        });
    }

    altCalendarsTable($("#alt-user-select option:selected").val());
    
    $("#alt-user-select").change(function () {
        var user_id = $("#alt-user-select option:selected").val();
        altCalendarsTable(user_id);

    });
    $("#add-calendar").click(function () {
        var data = {
            'calendar_id': $("#add-calendar-select option:selected").val(),
            'user_id': $("#alt-user-select option:selected").val()
        }
        $.post(ajaxurl, {"data": data, "action": 'add_calendar'}, function (response) {
            //console.log(response);
        }).done(function () {
            altCalendarsTable(data['user_id']);
        });
    });
    $(document).on('click', '.remove-calendar', function () {
        var data = {
            'calendar_id': $(this).parent().parent().find('td:first').text(),
            'user_id': $("#alt-user-select option:selected").val()
        }
        $.post(ajaxurl, {"data": data, "action": 'remove_calendar'}, function (response) {
            //console.log(response);
        }).done(function (response) {
            // console.log(response);
            altCalendarsTable(response);
        });
    });
    
});