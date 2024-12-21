@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->


<section class="content mt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add New Bundle Product</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg" style="color: red;"></div>
                        <form id="createThisForm">
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="name">Title <span style="color: red;">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter title">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="start_date">Start Date <span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="end_date">End Date <span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="3" placeholder="Enter short description"></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="banner_image">Banner Image <span style="color: red;">*</span></label>
                                    <input type="file" class="form-control-file" id="banner_image" accept="image/*">
                                    <img id="preview-image" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="small_image">Small Image <span style="color: red;">*</span></label>
                                    <input type="file" class="form-control-file" id="small_image" accept="image/*">
                                    <img id="preview-image1" src="#" alt="" style="max-width: 300px; width: 100%; height: auto; margin-top: 20px;">
                                </div>

                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content mt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Campaigns</h3>
                    </div>
                    <div class="card-body">
                        <table id="campaignTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Banner Image</th>
                                    <th>Small Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $data)
                                <tr>
                                    <td>{{ $data->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->start_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->end_date)->format('d-m-Y') }}</td>
                                    <td>
                                        @if($data->banner_image)
                                            <img src="{{ asset('images/campaign_banner/' . $data->banner_image) }}" alt="Banner Image" style="width: 100px; height: auto;">
                                        @else
                                            No Banner Image
                                        @endif
                                    </td>
                                    <td>
                                        @if($data->small_image)
                                            <img src="{{ asset('images/campaign_small/' . $data->small_image) }}" alt="Small Image" style="width: 100px; height: auto;">
                                        @else
                                            No Small Image
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('campaign.details', $data->id) }}" title="View Details">
                                            <i class="fa fa-eye" style="color: green; font-size:16px;"></i>
                                        </a>
                                        <a class="EditBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-edit" style="color: #2196f3; font-size:16px;"></i>
                                        </a>
                                        <a id="deleteBtn" rid="{{ $data->id }}">
                                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #dynamicImages {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .image-input-wrapper {
        flex: 0 0 auto;
        display: inline-block; 
        vertical-align: top;
        text-align: center;
        width: calc(25% - 10px);
        margin-bottom: 10px;
        position: relative;
    }

    .image-input-wrapper img {
        max-width: 100%;
        height: auto;
    }

    .image-input-icon {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 5px;
        cursor: pointer;
    }

    .image-input-icon i {
        color: red;
    }

</style>

@endsection

@section('script')

<script>
    $(document).ready(function(){
        $("#banner_image").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<script>
    $(document).ready(function(){
        $("#small_image").change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $("#preview-image1").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#short_description').summernote({
            height: 100,
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#addThisFormContainer").hide();

        $("#newBtn").click(function(){
            clearForm();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
        });

        $("#FormCloseBtn").click(function(){
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearForm();
            $('.ermsg').empty();
        });

        $("#campaignTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#campaignTable_wrapper .col-md-6:eq(0)');

        function clearForm(){
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create').text('Create');
            $("#cardTitle").text('Add new data');
            $('#preview-image').attr('src', '#');
            $('#preview-image1').attr('src', '#');
            $('#banner_image').val('');
            $('#small_image').val('');
            $("#long_description").summernote('code', '');
            $("#short_description").summernote('code', '');
        }

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#addBtn').click(function() {
            //Create
            if($(this).val() == 'Create') {
                var formData = new FormData($('#createThisForm')[0]);

                var bannerImage = document.getElementById('banner_image');
                    if(bannerImage.files && bannerImage.files[0]) {
                        formData.append("banner_image", bannerImage.files[0]);
                    }

                var smallImage = document.getElementById('small_image');
                    if(smallImage.files && smallImage.files[0]) {
                        formData.append("small_image", smallImage.files[0]);
                    }

                    // for (var pair of formData.entries()) {
                    //     console.log(pair[0] + ': ' + pair[1]);
                    // }
                
                $.ajax({
                    url: "{{ route('campaign.store') }}",
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        swal({
                            text: "Created successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        $('.ermsg').empty();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, error) {
                                $('.ermsg').append('<p>' + error + '</p>');
                            });
                        }
                    }
                });
            }
            //Update
            if($(this).val() == 'Update') {
                var formData = new FormData($('#createThisForm')[0]);

                var bannerImage = document.getElementById('banner_image');
                    if(bannerImage.files && bannerImage.files[0]) {
                        formData.append("banner_image", bannerImage.files[0]);
                    }

                var smallImage = document.getElementById('small_image');
                    if(smallImage.files && smallImage.files[0]) {
                        formData.append("small_image", smallImage.files[0]);
                    }

                formData.append("codeid", $("#codeid").val());

                // for (let [key, value] of formData.entries()) {
                //     console.log(key, value);
                // }
                
                $.ajax({
                    url: "{{URL::to('/admin/campaign-update')}}",
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function (response) {
                        swal({
                            text: "Updated successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        $('.ermsg').empty();
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, error) {
                                $('.ermsg').append('<p>' + error + '</p>');
                            });
                        }
                    }
                });
            }

        });

        //Edit
        $(".EditBtn").on("click", function(){
            $("#cardTitle").text('Update this data');
            codeid = $(this).attr('rid');
            info_url = '{{URL::to('/admin/campaign')}}' + '/'+codeid+'/edit';
            $.get(info_url,{},function(d){
                populateForm(d);
                pagetop();
            });
        });

        //Delete
        $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = '{{URL::to('/admin/campaign')}}' + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                        swal({
                            text: "Deleted",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error:function(d){
                        // console.log(d);
                    }
            });
        });

        function populateForm(data){
            // console.log(data);
            $("#title").val(data.title);
            $("#start_date").val(data.start_date);
            $("#end_date").val(data.end_date);
            $("#short_description").val(data.short_description);
            $('#short_description').summernote('code', data.short_description);

            var featureImagePreview = document.getElementById('preview-image');
            if (data.banner_image) { 
                featureImagePreview.src = '/images/campaign_banner/' + data.banner_image; 
            } else {
                featureImagePreview.src = "#";
            }

            var featureImagePreview1 = document.getElementById('preview-image1');
            if (data.small_image) { 
                featureImagePreview1.src = '/images/campaign_small/' + data.small_image; 
            } else {
                featureImagePreview1.src = "#";
            }
            
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);

        }
    });
</script>

@endsection