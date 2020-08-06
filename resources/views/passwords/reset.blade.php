<html>
<head>
<title>{{config('app.name')}}</title>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!------ Include the above in your HEAD tag ---------->
</head>
<body>
<!-- no additional media querie or css is required -->
<div class="container">
    <div class="row justify-content-center align-items-center" style="height:100vh">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{config('app.url').'web/password/reset'}}" method="POST">
                        @csrf
                        <div class="form-group">
                            @PHP($req=app('request')->all())

                            
                            <input id="token" type="hidden" class="form-control" name="redirect_after_reset" value="{{$req['redirect_after_reset']}}"> 
                            <input id="token" type="hidden" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}" name="token" value="{{$req['token']}}"> 
                            @if ($errors->has('token'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('token') }}</strong>
                                </span>
                            @endif
                            <br>
                            <input id="email" type="string" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{$req['email']}}" required>
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                            <br>
                            <input id="password" type="password" placeholder="New password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                            <br>
                            <input id="password-confirm" type="password" placeholder="Confirm new password"  class="form-control" name="password_confirmation" required>
                            @if ($errors->has('password_confirmation'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                            <br>
                            <div class="form-check">
                                <label class="form-check-label"><input class="form-check-input" type="checkbox" name="clear_sessions" checked> Desconnect all sessions</label>
                            </div>
                        </div>
                        <button type="submit" id="sendlogin" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>