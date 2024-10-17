@extends('layout.web')
@section('pagetitle','Dashboard')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Manage User</h3>
    <!-- Create Role Modal -->
    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createRoleModal">
        Create Role
    </button>
    <!-- Create Role Modal -->

    <!-- Create Role Modal -->
    <div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createRoleForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="createRoleModalLabel">Create Role</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" name="role" placeholder="Enter Role">
                            @error('role')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary createRoleBtn">Create</button>
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
            <th scope="col">Role</th>
            <th scope="col" width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i=0;
        @endphp
        @foreach($roles as $role)
        <tr>
            <th scope="row">{{++$i}}</th>
            <td>{{$role->name}}</td>
            <td>
                @if(strtolower($role->name)!='user')
                <button class="btn btn-primary updateRoleEditBtn" data-id="{{$role->id}}" data-name="{{$role->name}}"
                    data-bs-toggle="modal" data-bs-target="#updateRoleModal">Edit</button>
                <button data-id="{{$role->id}}" data-name="{{$role->name}}" class="btn btn-danger deleteRoleBtn"
                    data-bs-toggle="modal" data-bs-target="#deleteRoleModal">Delete</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Update Role Modal -->
<div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateRoleForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="createRoleModalLabel">Update Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" name="role" placeholder="Enter Role"
                            id="updateRoleName">
                        <input type="hidden" name="role_id" id="updateRoleId">
                        @error('role')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary updateRoleBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Update Role Modal -->

<!-- Delete Role Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteRoleForm">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deleteRoleModalLabel">Delete Role

                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="role_id" id="deleteRoleId">
                    <p>Are you sure, You want to delete the <span class="delete-role"></span> Role?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger delete-role-btn">Delete</button>
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
        $('#createRoleModal').submit(function(e){
            e.preventDefault();
            $('.createRoleBtn').prop('disabled',true);

            // var formData = $(this).serialize();
            var roleValue = $('#role').val();

            console.log(roleValue);

            $.ajax({
                url:"{{route('createRole')}}",
                type:"POST",
                data:{
                    'role':roleValue
                },
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    $('.createRoleBtn').prop('disabled',false);
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
        $('.deleteRoleBtn').click(function(){
            var roleId = $(this).data('id');
            var roleName = $(this).data('name');

            $('.delete-role').text(roleName);
            $('.deleteRoleId').val(roleId);

            $('#deleteRoleForm').submit(function(e){
                e.preventDefault();
                $('.delete-role-btn').prop('disabled',true);

                // var formData = $(this).serialize();

                $.ajax({
                    url:"{{route('deleteRole')}}",
                    type:"POST",
                    data:{
                        'role':roleId
                    },
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response){
                        $('.createRoleBtn').prop('disabled',false);
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
        $('.updateRoleEditBtn').click(function(){
            var roleId = $(this).data('id');
            var roleName = $(this).data('name');

            $('#updateRoleName').val(roleName);
            $('#updateRoleId').val(roleId);
            
            $('#updateRoleForm').submit(function(e){
                e.preventDefault();
                $('.updateRoleBtn').prop('disabled',true);

                var roleValue = $('#updateRoleName').val();

                $.ajax({
                    url:"{{route('updateRole')}}",
                    type:"POST",
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        role_id:roleId,
                        role:roleValue
                    },
                    success:function(response){
                        $('.updateRoleBtn').prop('disabled',false);
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