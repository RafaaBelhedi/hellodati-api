<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- CSRF Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title></title>
        <meta name="description" content="">
        <meta name="author" content="Jeremy Kenedy">
        <link rel="shortcut icon" href="/favicon.ico">


    </head>
    <body>
        <div id="app">
            <main class="py-4">

                <div class="container">
                    <div class="row">
                        <div class="col-12">
                        </div>
                    </div>
                </div>

                @yield('content')

            </main>

        </div>

    </body>
</html>