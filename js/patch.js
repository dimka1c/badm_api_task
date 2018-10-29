$(document).ready(function() {
    $('#patch-id').on('click', function(){
        $.ajax({
            url: 'api/user/4',
            type: 'patch',
            dataType: 'json',
            data: '{"email":"dima@udt.dp.ua", "name":"Дима", "phone":"newPhone", "access":"1"}',
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
    });
/*
    $('#add-user-id').on('click', function(){
        $.post(
            '/api/user',{"email":"igor@udt.dp.ua", "name":"Igor", "phone":"00-00-00-00", "access":"2"});
    });
*/

    $('#add-user-id').on('click', function(){
        var data = {'email':'igor@mail.ru', "name":'Igor', 'phone':'00-00-00-00', 'access':'2'};
        $.ajax({
            url: 'api/user',
            type: 'POST',
            dataType: 'JSON',
            data: {'email':'igor@mail.ru', 'name':'Igor', 'phone':'00-00-00-00', 'access':'2'},
            processData: false,
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

