@extends('layout.web')
@section('pagetitle','Dashboard')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Manage User</h3>
    <!-- Assign Permission to Role -->
    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
        data-bs-target="#assignPermissionRoleModal">
        Assign Permission to Role
    </button>
    <!-- Assign Permission to Role -->

    <!-- Assign Permission to Role -->
    <div class="modal fade" id="assignPermissionRoleModal" tabindex="-1"
        aria-labelledby="assignPermissionRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createRoleForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="assignPermissionRoleModalLabel">Assign Permission to Role</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role" class="form-label">Permission</label>
                            <select class="form-control" name="permission_id" id="permission_id" required>
                                <option value="">Select Permission</option>
                                @foreach($permissions as $permission)
                                <option value="{{$permission->id}}">{{$permission->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" name="role_id" id="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary assignPermissionRoleBtn">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Permission to Role -->
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" width="100px">#</th>
            <th scope="col">Permission</th>
            <th scope="col">Role</th>
            <th scope="col" width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i=0;
        @endphp
        @foreach($permissionsWithRoles as $key=>$permissionsWithRole)
        <tr>
            <td>{{++$i}}</td>
            <td>{{$permissionsWithRole->name}}</td>
            <td>
                @foreach($permissionsWithRole->roles as $key => $role)
                {{$role->name}}{{$key<count($permissionsWithRole->roles) - 1?',':''}}
                    @endforeach
            </td>
            <td>
                <button class="btn btn-primary updateAssignPermissionRoleBtn"
                    data-permission="{{$permissionsWithRole->id}}" data-roles="{{$permissionsWithRole->roles}}"
                    data-bs-toggle="modal" data-bs-target="#updateAssignPermissionRoleModal">Edit</button>
                <button class="btn btn-danger">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Assign Permission to Role -->
<div class="modal fade" id="updateAssignPermissionRoleModal" tabindex="-1"
    aria-labelledby="updateAssignPermissionRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="update-form">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="updateAssignPermissionRoleModalLabel">Update Assign Permission to
                        Role
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role" class="form-label">Permission</label>
                        <select class="form-control" name="permission_id" id="update_permission_id"
                            style="pointer-events:none;" required>
                            <option value="">Select Permission</option>
                            @foreach($permissions as $permission)
                            <option value="{{$permission->id}}">{{$permission->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" name="role_id" id="update_role_id" multiple required>
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{$role->id}}" data-name="{{$role->name}}">{{$role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary updateAssignFormPermissionRoleBtn">Update
                        Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assign Permission to Role -->
@endsection
@push('script')
<script>
    $(document).ready(function(){
        //Assign Permission Role Modal
        $('#assignPermissionRoleModal').submit(function(e){
            e.preventDefault();
            $('.assignPermissionRoleBtn').prop('disabled',true);

            // var formData = $(this).serialize();
            var roleValue = $('#role_id').val();
            var permissionValue = $('#permission_id').val();

            console.log(roleValue,permissionValue);

            $.ajax({
                url:"{{route('createPermissionRole')}}",
                type:"POST",
                data:{
                    'permission_id':permissionValue,
                    'role_id':roleValue
                },
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    $('.assignPermissionRoleBtn').prop('disabled',false);
                    if(response.success){
                        alert(response.msg);
                        location.reload();
                    }else{
                        alert(response.msg);
                    }
                }
            });
        });
        //Assign Permission Role Modal

        //Update Assign Permission Role Modal
        $('.updateAssignPermissionRoleBtn').click(function(e){
            e.preventDefault();
            var permission = $(this).data('permission');
            var roles = $(this).data('roles');
            //Permission Dropdown
            $('#update_permission_id').prop('selected',false);
            $('#update_permission_id').val(permission).prop('selected',true);
            
            //Role Dropdown
            $('#update_role_id option').prop('selected',false);
            if(roles.length > 0){
                roles.forEach(function(role){
                    $('#update_role_id option[value="'+role.id+'"]').prop('selected',true);
                });            
            }
           

            $('#update-form').submit(function(e){
                e.preventDefault();
                var permission = $('#update_permission_id').val();
                var roles = $('#update_role_id').val();

                if(roles == ''){
                    alert('Please Select atleast one role!!!');
                    return false;
                }
                $('.updateAssignFormPermissionRoleBtn').prop('disabled',true);

                $.ajax({
                    url:"{{route('updatePermissionRole')}}",
                    type:"POST",
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        'permission':permission,
                        'roles':roles
                    },
                    success:function(response){
                        $('.updateAssignFormPermissionRoleBtn').prop('disabled',false);
                        if(response.success){
                            alert(response.msg);
                            location.reload();
                        }else{
                            alert(response.msg);
                        }
                    }
                })

            })
        })
        //Update Assign Permission Role Modal
    });
</script>
@endpush