@extends('layout.web')
@section('pagetitle','Dashboard')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Manage User</h3>
    <!-- Create Permission Modal -->
    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
        data-bs-target="#createPermissionModal">
        Create Permission
    </button>
    <!-- Create Permission Modal -->

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createPermissionForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="createPermissionModalLabel">Create Permission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="permission" class="form-label">Permission</label>
                            <input type="text" class="form-control" id="permission" name="permission"
                                placeholder="Enter Permission">
                            @error('permission')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary createPermissionBtn">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Create Permission Modal -->
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th scope="col" width="100px">#</th>
            <th scope="col">Permission</th>
            <th scope="col" width="150px">Action</th>
        </tr>
    </thead>
    <tbody>
        @php
        $i=0;
        @endphp
        @foreach($permissions as $permission)
        <tr>
            <th scope="row">{{++$i}}</th>
            <td>{{$permission->name}}</td>
            <td>
                <button class="btn btn-primary updatePermissionEditBtn" data-id="{{$permission->id}}"
                    data-name="{{$permission->name}}" data-bs-toggle="modal"
                    data-bs-target="#updatePermissionModal">Edit</button>
                <button data-id="{{$permission->id}}" data-name="{{$permission->name}}"
                    class="btn btn-danger deletePermissionBtn" data-bs-toggle="modal"
                    data-bs-target="#deletePermissionModal">Delete</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Update Permission Modal -->
<div class="modal fade" id="updatePermissionModal" tabindex="-1" aria-labelledby="updatePermissionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updatePermissionForm">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="createPermissionModalLabel">Update Permission</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="Permission" class="form-label">Permission</label>
                        <input type="text" class="form-control" name="permission" placeholder="Enter Permission"
                            id="updatePermissionName">
                        <input type="hidden" name="permission_id" id="updatePermissionId">
                        @error('permission')
                        <span class="text-danger">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary updatePermissionBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Update Permission Modal -->

<!-- Delete Permission Modal -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deletePermissionForm">
                @csrf
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="deletePermissionModalLabel">Delete Permission

                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="permission_id" id="deletePermissionId">
                    <p>Are you sure, You want to delete the <span class="delete-permission"></span> Permission?
                        If you will delete this permission, then this permission is deleted from All users.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger delete-permission-btn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Permission Modal -->
@endsection
@push('script')
<script>
    $(document).ready(function(){
        //Create Permission Modal
        $('#createPermissionModal').submit(function(e){
            e.preventDefault();
            $('.createPermissionBtn').prop('disabled',true);

            // var formData = $(this).serialize();
            var permissionValue = $('#permission').val();

            console.log(permissionValue);

            $.ajax({
                url:"{{route('createPermission')}}",
                type:"POST",
                data:{
                    'permission':permissionValue
                },
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    $('.createPermissionBtn').prop('disabled',false);
                    if(response.success){
                        alert(response.msg);
                        location.reload();
                    }else{
                        alert(response.msg);
                    }
                }
            });
        });
        //Create Permission Modal

        //Delete Permission Modal
        $('.deletePermissionBtn').click(function(){
            var permissionId = $(this).data('id');
            var permissionName = $(this).data('name');

            $('.delete-permission').html('<b>'+permissionName+'</b>');
            $('.deletePermissionId').val(permissionId);

            $('#deletePermissionForm').submit(function(e){
                e.preventDefault();
                $('.delete-permission-btn').prop('disabled',true);

                // var formData = $(this).serialize();

                $.ajax({
                    url:"{{route('deletePermission')}}",
                    type:"POST",
                    data:{
                        'permission':permissionId
                    },
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response){
                        $('.createPermissionBtn').prop('disabled',false);
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

       
        //Delete Permission Modal

        //Update Permission Modal
        $('.updatePermissionEditBtn').click(function(){
            var permissionId = $(this).data('id');
            var permissionName = $(this).data('name');

            $('#updatePermissionName').val(permissionName);
            $('#updatePermissionId').val(permissionId);
            
            $('#updatePermissionForm').submit(function(e){
                e.preventDefault();
                $('.updatePermissionBtn').prop('disabled',true);

                var permissionValue = $('#updatePermissionName').val();

                $.ajax({
                    url:"{{route('updatePermission')}}",
                    type:"POST",
                    headers:{
                        'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                    },
                    data:{
                        permission_id:permissionId,
                        permission:permissionValue
                    },
                    success:function(response){
                        $('.updatePermissionBtn').prop('disabled',false);
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

        //Update Permission Modal
        

    });
</script>
@endpush