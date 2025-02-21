<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                @if(auth()->user()->hasPermissionToRoute('users'))
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="{{route('users')}}">User</a>
                </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Role & Permission
                    </a>
                    <ul class="dropdown-menu" style="width:200px">
                        @if(auth()->user()->hasPermissionToRoute('manageRole'))
                        <li><a class="nav-link" href="{{route('manageRole')}}">Manage Roles</a></li>
                        @endif
                        @if(auth()->user()->hasPermissionToRoute('managePermission'))
                        <li><a class="nav-link" href="{{route('managePermission')}}">Manage Permissions</a></li>
                        @endif
                        @if(auth()->user()->hasPermissionToRoute('assignPermissionRole'))
                        <li><a class="nav-link" href="{{route('assignPermissionRole')}}">Assign Permission-Role</a></li>
                        @endif
                        @if(auth()->user()->hasPermissionToRoute('assignPermissionRoute'))
                        <li><a class="nav-link" href="{{route('assignPermissionRoute')}}">Assign Permission-Route</a>
                        @endif
                        </li>
                    </ul>
                </li>
                @if(auth()->user()->hasPermissionToRoute('product.index'))
                <li class="nav-item">
                    <a class="nav-link" href="{{route('product.index')}}">Product</a>
                </li>
                @endif
               
            </ul>
            <form class="d-flex" role="search">
                <span class="nav-item dropdown">
                    <a class="btn btn-outlined-primary" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Hi, {{auth()->user()->name}}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="text-center text-decoration-none p-2" id="logout">Logout</a>
                        </li>
                    </ul>
                </span>
            </form>
        </div>
    </div>
</nav>