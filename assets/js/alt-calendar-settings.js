jQuery(document).ready(function ($) {

    function AltCalendarsTable(user_id) {
        jQuery.post(ajaxurl, {"data": user_id, "action": 'get_user'}, function (response) {
            console.log(response);
        }).done(function (response) {
            var content = '<tr><th>ID</th><th>Name</th></tr>';
            for (i = 0; i < response['id'].length; i++) {
                content += '<tr><td>' + response['id'][i] + '</td><td>' + response['names'][i] + '</td></tr>';
            }
            jQuery("#alt-calendar-table").html((content));
            //$("")
        });
    }

    jQuery("#alt-user-select").change(function () {
        console.log('cale zycie');
        var user_id = jQuery("#alt-user-select option:selected").val();
        AltCalendarsTable(user_id);
    });
});