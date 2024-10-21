@extends('layout.web')
@section('pagetitle', 'Dashboard')
@section('content')
<div class="my-3">
    <h3 class="d-inline">Assign Permission to Route</h3>
    <!-- Assign Permission to Route -->
    <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal"
        data-bs-target="#assignPermissionRouteModal">
        Assign Permission to Route
    </button>
    <!-- Assign Permission to Route -->
    <!-- Assign Permission to Route -->
    <div class="modal fade" id="assignPermissionRouteModal" tabindex="-1"
        aria-labelledby="assignPermissionRouteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="add-form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="assignPermissionRouteModalLabel">Assign Permission to Route
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="role" class="form-label">Permission</label>
                            <select class="form-control" name="permission_id" id="permission_id" required>
                                <option value="">Select Permission</option>
                                @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="route" class="form-label">Route</label>
                            <select class="form-control" name="route" id="route" required>
                                <option value="">Select Route</option>
                                @foreach ($routeDetails as $routeDetail)
                                <option value="{{ $routeDetail['name'] }}">{{ $routeDetail['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary assignPermissionRouteBtn">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Permission to Route -->

    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col" width="100px">#</th>
                <th scope="col">Permission</th>
                <th scope="col">Route Name</th>
                <th scope="col" width="150px">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
            $i=0;
            @endphp
            @foreach($routerPermissions as $key=>$routerPermission)
            <tr>
                <td>{{++$i}}</td>
                <td>{{$routerPermission->permission->name}}</td>
                <td>
                    {{$routerPermission->router}}
                </td>
                <td>
                    <button class="btn btn-primary editBtn" data-id="{{$routerPermission->id}}"
                        data-permission-id="{{$routerPermission->permission_id}}"
                        data-router="{{$routerPermission->router}}" data-bs-toggle="modal"
                        data-bs-target="#updateModal">Edit</button>
                    <button class="btn btn-danger deleteBtn" data-id="{{$routerPermission->id}}" data-bs-toggle="modal"
                        data-bs-target="#deleteModal">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Update Permission to Route -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="update-form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="updateModalLabel">Update Permission to Route
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="update-id">
                        <div class="mb-3">
                            <label for="role" class="form-label">Permission</label>
                            <select class="form-control" name="permission_id" id="update_permission_id" required>
                                <option value="">Select Permission</option>
                                @foreach ($permissions as $permission)
                                <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="route" class="form-label">Route</label>
                            <select class="form-control" name="route" id="update_route" required>
                                <option value="">Select Route</option>
                                @foreach ($routeDetails as $routeDetail)
                                <option value="{{ $routeDetail['name'] }}">{{ $routeDetail['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submitUpdateBtn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Assign Permission to Route -->
    <!-- Delete Permission to Route -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="delete-form">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteModalLabel">Delete Permission to Route
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete_id">
                        <p>Are you sure you want to delete?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary submitDeleteBtn">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Permission to Route -->

    @endsection
    @push('script')
    <script>
        $(document).ready(function() {
            //Create Permission to Route Modal
            $('#add-form').submit(function(e) {
                e.preventDefault();
                $('.assignPermissionRouteBtn').prop('disabled', true);

                // var formData = $(this).serialize();
                var permissionValue = $('#permission_id').val();
                var routeValue = $('#route').val();

                $.ajax({
                    url: "{{ route('createPermissionRoute') }}",
                    type: "POST",
                    data: {
                        'permission_id': permissionValue,
                        'route': routeValue,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.assignPermissionRouteBtn').prop('disabled', false);
                        if (response.success) {
                            alert(response.msg);
                            location.reload();
                        } else {
                            alert(response.msg);
                        }
                    }
                });
            });
            //Create Permission to Route Modal

            //Update Permission to Route Modal
            $('.editBtn').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');
                var permission_id = $(this).data('permission-id');
                var router = $(this).data('router');

                $('#update-id').val(id);
                $('#update_permission_id').val(permission_id).prop('selected',true);
                $('#update_route').val(router).prop('selected',true);

                $('#update-form').submit(function(e) {
                    e.preventDefault();
                    $('.submitUpdateBtn').prop('disabled', true);

                    // var formData = $(this).serialize();
                    var permissionValue = $('#update_permission_id').val();
                    var routeValue = $('#update_route').val();

                    $.ajax({
                        url: "{{ route('updatePermissionRoute') }}",
                        type: "POST",
                        data: {
                            'id':id,
                            'permission_id': permissionValue,
                            'route': routeValue,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('.submitUpdateBtn').prop('disabled', false);
                            if (response.success) {
                                alert(response.msg);
                                location.reload();
                            } else {
                                alert(response.msg);
                            }
                        }
                    });
                });
            })
            //Update Permission to Route Modal

             //Delete Permission to Route Modal
             $('.deleteBtn').click(function(e){
                e.preventDefault();
                var id = $(this).data('id');

                $('#delete_id').val(id);

                $('#delete-form').submit(function(e) {
                    e.preventDefault();
                    $('.submitDeleteBtn').prop('disabled', true);

                    // var formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('deletePermissionRoute') }}",
                        type: "POST",
                        data: {
                            'id':id
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('.submitDeleteBtn').prop('disabled', false);
                            if (response.success) {
                                alert(response.msg);
                                location.reload();
                            } else {
                                alert(response.msg);
                            }
                        }
                    });
                });
            })
            //Delete Permission to Route Modal

        });
    </script>
    @endpush