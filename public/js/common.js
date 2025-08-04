/* $("#country").on("change", function(){
    let country_id = $(this).val();
    var baseUrl = $(this).data('url');
    console.log(country_id, baseUrl);
    if (country_id) {
        $.ajax({
            url: '/get-states/' + country_id,
            type: 'GET',
            success: function(states) {
                $('#state').empty().append('<option value="">Select State</option>');
                $.each(states, function(key, value) {
                    $('#state').append('<option value="' + key + '">' + value + '</option>');
                });
            }
        });
    } else {
        $('#state').empty().append('<option value="">Select State</option>');
    }
}) */

function ajaxCall(options) {
    $.ajax({
        url: options.url,
        type: options.method || 'GET',
        data: options.data || {},
        dataType: options.dataType || 'json',
        beforeSend: function () {
            if (typeof options.beforeSend === 'function') {
                options.beforeSend();
            }
        },
        success: function (response) {
            if (typeof options.success === 'function') {
                options.success(response);
            }
        },
        error: function (xhr, status, error) {
            if (typeof options.error === 'function') {
                options.error(xhr, status, error);
            } else {
                console.error("AJAX Error:", error);
            }
        },
        complete: function () {
            if (typeof options.complete === 'function') {
                options.complete();
            }
        }
    });
}