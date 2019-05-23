function formChecker() {

    $('body').on('submit', '.geocode-form', function (e) {

        e.preventDefault();

        var text = $('#exampleInputIp').val();
        var url  =  'http://api.ipstack.com/' + text

        $.ajax({
            type: $(this).attr('method'),
            url: url,
            data: 'access_key=ee9550e285db4c9f3769db3c24012953',
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            success: function (response) {
                var html = '';
                if (response.city === null) {
                    html = '<p class="center">city not found</p>'
                } else {
                    html = '<p>ip: ' + response.ip + '</p>' +
                        '<p>city: ' + response.city + '</p>' +
                        '<p>country name: ' + response.country_name + '</p>';
                }

                $('#geocode-data').html(html);
            },
            error: function () {
                console.log('error');
            }
        })
    });
}