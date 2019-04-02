@extends('layout.bst')

@section('content')
<form class="form-signin" action="/pc/logins" method="post">
    {{csrf_field()}}
        账号<input type="text" name="uname"><br/>
        密码<input type="password" name="password"><br/>
        <input type="submit" value="登陆">
</form>
@endsection