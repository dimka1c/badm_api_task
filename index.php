<?php
    require $_SERVER['DOCUMENT_ROOT'] .'/class/Task.php';
    $model = new \app\Task();

    echo $res; die;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-param" content="_csrf">
    <title>Badm test</title>
</head>
<body>
<div class="container">
    <div class="col-sm-4">
        <form id="login-form">
            <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" name="email" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Password</label>
                <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <button type="button" id="patch-id">Request Patch</button>
    <button type="button" id="add-user-id">Request Add-User</button>
</div>
<script src="/js/jquery-3.3.1.min.js"></script>
<script src="/js/patch.js"></script>
</body>
</html>