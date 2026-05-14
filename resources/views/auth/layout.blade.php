<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
    <title>{{config('site.title')}}</title>
    <link rel="icon" href="{{ asset('images/logo.ico') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src='{{ asset('js/jquery/jquery.min.js') }}'></script>
</head>

<body translate="no">
    <div class="cont">
        <div class="demo">
            <div class="login">
                <table style=" width: 100%; height: 200px">
                    <tr>
                        <td align="center">
{{--                            <img src="{{ asset('images/logo.png') }}">--}}
                            <br><br><br>
                            <h1 style="color: #FFFFFF; font-size: 18px; font-weight: normal; height: 22px">{{config('site.title')}}</h1>
                            <h2 style="color: #FFFFFF; font-size: 12px; font-weight: normal; height: 16px">{{config('site.description')}}</h2>

                            <div style="color: orange; font-size: 10px; margin-top: 30px">

                                @yield('message')

                            </div>
                        </td>
                    </tr>
                </table>

                <div class="login__form">

                    @yield('content')

                    @if(isMobile())
                        <a href="{{ asset('app.apk') }}">
                            <img src="{{ asset('images/googleplay.png') }}" width="120">
                        </a>
                    @endif

                    <p style="font-size: 9px; color: #FFF; margin-top: 10px">
                        Development by Qualita Indonesia
                        <br>
                        ©{{config('site.year')}} (version: {{config('site.version')}})<br>
                    </p>

                </div>

            </div>
        </div>
    </div>
</body>
</html>
