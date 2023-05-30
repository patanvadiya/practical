<!DOCTYPE html>
<html lang="en">

<head>
    <title>User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container mt-5">
        <div class="alert alert-danger">#Note = 
        Please run that command <span class="alert alert-success" > php artisan db:deed </span> in your cmd because of that run the factory then create Education and Company dummy record</div>
        <button type="button" class="btn btn-primary btn_add float-end" data-bs-toggle="modal" data-bs-target="#myModal">
            Add User
        </button><br><br>

        <table class="table table-striped " id="users" >
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Education</th>
                    <th>Company</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>


    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="modal-title msg"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="user_form" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3 mt-3">
                            <label for="" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter name" name="name">
                            <span class="error_name error"></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                            <span class="error_email error"></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="" class="form-label">Phone:</label>
                            <input type="text" class="form-control" id="phone" placeholder="Enter phone" name="phone">
                            <span class="error_phone error"></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="" class="form-label">Education:</label>
                            <select name="edu_id" id="edu_id" class="form-control">
                                <option value="">Select Education</option>
                                @foreach($education as $educations)
                                <option value="{{$educations->id}}">{{$educations->name}}</option>
                                @endforeach
                            </select>
                            <span class="error_edu_id error"></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="" class="form-label">Company:</label>
                            <select name="cmp_id" id="cmp_id" class="form-control">
                                <option value="">Select Company</option>
                                @foreach($company as $companys)
                                <option value="{{$companys->id}}">{{$companys->name}}</option>
                                @endforeach
                            </select>
                            <span class="error_cmp_id error"></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="" class="form-label">Image:</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <div id="priview" ></div>
                            <span class="error_image error"></span>
                        </div>
                        <input type="hidden" id="id" name="id">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


</body>

</html>
<script>
    $(document).ready(function(e) {


        $(".btn_add").click(function() {
            $("#id").val("");
            $('#user_form')[0].reset();
            $("#priview").hide();
            $(".msg").html("Add User");
            $(".error").hide();
        });

        $("#user_form").on('submit', (function(e) {
            e.preventDefault();
            var id_check = $("#id").val();
            if (id_check) {
                var url = "{{route('update_user')}}"
            } else {
                url = "{{route('store_user')}}"
            }

            $.ajax({
                url: url,
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    
                    if(data.status == 0) {
                        $(".error").show();
                        $(".error").empty();
                        $.each(data.error,function(k,v){
                            $(".error_"+k).html(v);
                            $(".error").css("color","red")
                        })
                    }
                    if (data.status == 1) {
                        Swal.fire('Data update successfully')
                        $("#myModal").modal("hide");
                    } 
                    if(data.status == 2) {
                        Swal.fire('Data insert successfully')
                        $("#myModal").modal("hide");

                    }
                    $('#users').DataTable().ajax.reload();
                },

            });
        }));

        $(document).on("click", ".edit", function() {
             $('#user_form')[0].reset();
             $("#priview").show();
             $(".msg").html("Edit User");
            var id = $(this).attr("data-id");
            $.ajax({
                url: "{{route('edit_user')}}",
                type: "get",
                data: {
                    id: id
                },
                success: function(data) {
                    console.log(data);
                    $("#name").val(data.name)
                    $("#id").val(data.id)
                    $("#email").val(data.email)
                    $("#phone").val(data.phone)
                    $("#edu_id").val(data.edu_id)
                    $("#cmp_id").val(data.cmp_id)
                    $("#priview").html("<img  style='width:50%' src='{{url('storage/images')}}/" + data.image + "'>")
                    $("#myModal").modal("show");
                },
            })
        });
        $(document).on("click", ".delete", function() {

            var id = $(this).attr("data-id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                url: "{{route('delete_user')}}",
                type: "get",
                data: {
                    id: id
                },
                success: function(data) {
                    $('#users').DataTable().ajax.reload();
                },
            })
                    Swal.fire(
                        'Deleted!',
                        'Your data has been deleted.',
                        'success'
                    )
                }
            })

        });
        $(function() {

            var table = $('#users').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('user_data') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'edu_id',
                        name: 'edu_id'
                    },
                    {
                        data: 'cmp_id',
                        name: 'cmp_id'
                    },
                    {
                        data: 'image',
                        name: 'image'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });


    });
</script>