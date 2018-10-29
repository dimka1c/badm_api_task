$(document).ready(function() {
    $('#add-user-id').on('click', function(){
        $.ajax({
            url: 'api/user',
            type: 'post',
            dataType: 'json',
            data: '{"email":"igor@udt.dp.ua", "name":"Igor", "phone":"00-00-00-00", "access":"2"}',
            contentType : 'application/json',
            headers: {
                "Authorization":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyIjp7ImlkIjoiNCIsIm5hbWUiOiJcdTA0MTRcdTA0MzhcdTA0M2NcdTA0MzAiLCJyb2xlX3JlYWQiOiIxIiwicm9sZV93cml0ZSI6IjEiLCJyb2xlX2RlbGV0ZSI6IjEiLCJyb2xlX3VwZGF0ZSI6IjEiLCJyb2xlX3Rhc2siOiIxIn19.6h0vrUY_1vYYUDVCcoi61p_1u87BkkqPOg9p2VJpIOA",
            },
            success: function (resp) {
                console.log(resp);
            },
            error: function(err) {
                console.log(err.responseJSON);
            }
        })
    })
})