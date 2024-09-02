<h3>Active User {{ $user->name ?? '-' }}</h3>
<ul>
    <li>Username: {{ $user->username }}</li>
    <li>Password: {{ $password }}</li>
</ul>

Please Login To Website : <a href="{{ url('/') }}">{{ url('/') }}</a>
