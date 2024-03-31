<!DOCTYPE html>
<html class="notranslate" translate="no" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.x"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @vite('resources/js/app.js')
    <script>
        window.base_url = '{{url('/')}}'

    </script>
    @if (Auth::check())
        <script>
            window.auth_user=true;
            window.user_id = parseInt('{{auth()->user()->id}}');
        </script>
    @else
        <script>window.auth_user=false;</script>
    @endif
</head>
<body id="app">
    <main-component/>
</body>
</html>
