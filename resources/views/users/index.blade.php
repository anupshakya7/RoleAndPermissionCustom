@extends('layout.web')
@section('pagetitle','Dashboard')
@section('content')
<div class="my-3">
    <h3 class="d-inline">User</h3>
    <!-- Create Role Modal -->
    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
        Create User
    </button>
    <!-- Create Role Modal -->
    <!-- Create Role Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addModalForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addModalLabel">Create User</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Enter Username">
                            @error('username')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="Enter Email Address">
                            @error('email')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Select Role</label>
                            <select class="form-control" name="role" id="role">
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                            @error('role')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter Password">
                            @error('password')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Enter Confirmation Password">
                            @error('password_confirmation')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary createUserBtn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Create Role Modal -->
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" width="100px">#</th>
            <th scope="col">Fullname</th>
            <th scope="col">Email</th>
            <th scope="col">Role</th>
            <th scope="col" width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i=0;
        @endphp
        @foreach($users as $user)
        <tr>
            <th scope="row">{{++$i}}</th>
            <td>{{$user->name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->role->name}}</td>
            <td>
                @if(strtolower($role->name)!='user')
                <button class="btn btn-primary updateUserBtn" data-id="{{$user->id}}" data-name="{{$user->name}}"
                    data-email="{{$user->email}}" data-role-id="{{$user->role->id}}" data-bs-toggle="modal"
                    data-bs-target="#updateUserModal">Edit</button>
                <button class="btn btn-danger deleteUserBtn" data-id="{{$user->id}}" data-name="{{$user->name}}"
                    data-bs-toggle="modal" data-bs-target="#deleteUserModal">Delete</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Update Role Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateUserForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="updateUserModalLabel">Update User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="update_id" name="update_id" id="update_id">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control update_username" id="update_username" name="username"
                            placeholder="Enter Username">
                        @error('username')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control update_email" id="update_email" name="email"
                            placeholder="Enter Email Address">
                        @error('email')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Select Role</label>
                        <select class="form-control update_role" name="role" id="update_role">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control update_password" id="update_password" name="password"
                            placeholder="Enter Password">
                        @error('password')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary updateSubmitUserBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Update Role Modal -->

<!-- Delete Role Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteUserForm">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteUserModalLabel">Delete User

                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <p>Are you sure, You want to delete the <span class="delete-user"></span> user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger delete-user-btn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Role Modal -->
@endsection
@push('script')
<script>
    $(document).ready(function(){
        //Create Role Modal
        $('#addModalForm').submit(function(e){
            e.preventDefault();
            $('.createUserBtn').prop('disabled',true);

            // var formData = $(this).serialize();
            var username = $('#username').val();
            var email = $('#email').val();
            var password = $('#password').val();
            var password_confirmation = $('#password_confirmation').val();
            var role = $('#role').val();

            $.ajax({
                url:"{{route('createUser')}}",
                type:"POST",
                data:{
                    'fullname':username,
                    'email':email,
                    'password':password,
                    'password_confirmation':password_confirmation,
                    'role':role,
                },
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    $('.createUserBtn').prop('disabled',false);
                    if(response.success){
                        alert(response.msg);
                        location.reload();
                    }else{
                        alert(response.msg);
                    }
                }
            });
        });
        //Create Role Modal

        //Delete Role Modal
        $('.deleteUserBtn').click(function(){
            var userId = $(this).data('id');
            var userName = $(this).data('name');

            $('#deleteUserId').val(userId);
            $('.delete-user').html('<b>'+userName+'</b>');

            $('#deleteUserForm').submit(function(e){
                e.preventDefault();
                $('.delete-user-btn').prop('disabled',true);

                // var formData = $(this).serialize();

                $.ajax({
                    url:"{{route('deleteUser')}}",
                    type:"POST",
                    data:{
                        'id':userId
                    },
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response){
                        $('.delete-user-btn').prop('disabled',false);
                        if(response.success){
                            alert(response.msg);
                            location.reload();
                        }else{
                            alert(response.msg);
                        }
                    }
                });
            });
        });

       
        //Delete Role Modal

        //Update Role Modal
        $('.updateUserBtn').click(function(){
            var userId = $(this).data('id');
            var userfullName = $(this).data('name');
            var userEmail = $(this).data('email');
            var userRole = $(this).data('role-id');

            $('.update_id').val(userId);
            $('.update_username').val(userfullName);
            $('.update_email').val(userEmail);
            $('.update_role').val(userRole);
            
            $('#updateUserForm').submit(function(e){
                e.preventDefault();
                $('.updateSubmitUserBtn').prop('disabled',true);

                var id = $('.update_id').val();
                var name = $('.update_username').val();
                var email = $('.update_email').val();
                var role = $('.update_role').val();
                var password = $('.update_password').val();

                $.ajax({
                    url:"{{route('updateUser')}}",
                    type:"POST",
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        id:id,
                        fullname:name,
                        email:email,
                        role:role,
                        password:password
                    },
                    success:function(response){
                        $('.updateSubmitUserBtn').prop('disabled',false);
                        if(response.success){
                            alert(response.msg);
                            location.reload();
                        }else{
                            alert(response.msg);
                        }
                    }
                })

            });
        });

        //Update Role Modal
        

    });
</script>
@endpush