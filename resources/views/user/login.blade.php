@extends('layout.bst')

@section('content')
<form class="form-signin" action="/user/login" method="post">
    {{csrf_field()}}
    <h2 class="form-signin-heading">请登录</h2>
    <label for="inputEmail">用户名:</label>
    <input type="text" name="u_name" id="inputEmail" class="form-control" placeholder="name" required autofocus>
    <label for="inputPassword" >密码:</label>
    <input type="password" name="u_password" id="inputPassword" class="form-control" placeholder="***" required>
    <div class="checkbox">
        <label>
            <input type="checkbox" value="remember-me"> Remember me
        </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>
@endsection