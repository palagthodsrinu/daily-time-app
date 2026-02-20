@auth
<li class="nav-item">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="btn btn-link nav-link" style="cursor:pointer;">Logout</button>
    </form>
</li>
@endauth
