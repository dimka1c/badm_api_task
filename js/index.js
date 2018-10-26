$(document).ready(function() {
    $('form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/api/login',
            type: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            headers: {
                'Authorization' : 'myToken',
            },
            success: function(resp) {
                console.log(resp);
            },
            error: function(err) {
                console.log(err);
            },
        });
    })
});