jQuery(document).ready(function ($) {
    $("#add-calendar-label").hide();
    function AltCalendarsTable(user_id) {
        jQuery.post(ajaxurl, {"data": user_id, "action": 'get_user'}, function (response) {
            //console.log(response);
        }).done(function (response) {
            var content = '<tr><th>ID</th><th>Name</th></tr>';
            for (i = 0; i < response['id'].length; i++) {
                content += '<tr><td>' + response['id'][i] + '</td><td>' + response['names'][i] + '</td><td><i id="remove-cal" class="fa fa-times" aria-hidden="true"></i></td></tr>';
            }
            jQuery("#alt-calendar-table").html((content));
            
        });
    }

    jQuery("#alt-user-select").change(function () {
        //console.log('cale zycie');
        var user_id = jQuery("#alt-user-select option:selected").val();
        AltCalendarsTable(user_id);
        $('#add-calendar-label label').text('Add calendar to '+jQuery("#alt-user-select option:selected").text());
        $("#add-calendar-label").show();
    });
    jQuery("#add-calendar").click(function () {
        var data = {
            'calendar_id': $("#add-calendar-select option:selected").val(),
            'user_id': $("#alt-user-select option:selected").val()
        }
        jQuery.post(ajaxurl, {"data": data, "action": 'add_calendar'}, function (response) {
            console.log(response);
        }).done(function(){
            AltCalendarsTable(data['user_id']);
        });
    });
});