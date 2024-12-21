@extends('admin.layouts.admin')
@section('content')

<?php
$user_id = Session::get('categoryEmployId');
$brnach_id = Session::get('brnach_id');
?>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
<?php
echo Session::put('message', '');
?>
@if (session('info'))
    <div class="alert alert-danger">
        {{ session('info') }}
    </div>
@endif
<?php
echo Session::put('info', '');
?>

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                @component('components.widget')
                    @slot('title')
                        Branch
                    @endslot
                    @slot('description')
                        Branch information
                    @endslot
                    @slot('body')
                        @component('components.table')
                            @slot('tableID')
                                branchTBL
                            @endslot
                            @slot('head')
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            @endslot
                        @endcomponent
                    @endslot
                @endcomponent
            </div>

            <div class="col-md-4">
                <div class="box-inner">
                    <div class="box-content">
                        <h4>New Branch</h4>
                        <div class="form-group">
                            <label for="branchName2">Branch Name</label>
                            <input type="text" id="branchName2" class="form-control" required="">
                        </div>
                        <button type="submit" onclick="save_branch2();" class="btn btn-primary btn-block">
                            <i class="fa fa-save"></i> SAVE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(function () {
        $('.select2').select2();
    });

    var branchurl = "{{URL::to('/admin/branch')}}";
    var branchTBL = $('#branchTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: branchurl,
        deferRender: true,
        columns: [
            {
                data: 'name', name: 'name', render: function (data, type, row, meta) {
                    return `<input id='${row.id}' value="${data}" type="text" class="form-control" maxlength="50"/>`;
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function (data, type, row, meta) {
                    const isChecked = row.status == 1 ? 'checked' : '';
                    return `
                        <label class="switch">
                            <input class="status-btn" type="checkbox" value='${row.id}' ${isChecked} onclick='branch_status("${row.status == 1 ? "unpublished-branch" : "published-branch"}", "${row.id}")'>
                            <span class="slider round"></span>
                        </label>`;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return `<button class="btn btn-flat btn-sm btn-primary" onclick='edit_data("${row.id}")'>
                                <i class="fa fa-save"></i> Save
                            </button>`;
                }
            },
        ]
    });

    var stsurl = "{{URL::to('/admin')}}";
    function branch_status(route, id) {
        $.ajax({
            url: stsurl + "/" + route + "/" + id,
            type: 'GET',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                branchTBL.draw();
                swal({
                    text: "Updated successfully",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function edit_data(id) {
        let branchName = $("#" + id).val();
        if (!branchName) {
            alert("Please Provide Branch Name");
            return;
        }
        let data = {
            branchName: branchName,
        };
        $.ajax({
            url: stsurl + '/edit-branch/' + id,
            data: {
                data: data
            },
            type: 'POST',
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                branchTBL.draw();
                swal({
                    text: "Updated successfully",
                    icon: "success",
                    button: {
                        text: "OK",
                        className: "swal-button--confirm"
                    }
                });
            },
            error: function (err) {
                console.log(err);
                alert("Something Went Wrong, Please check again");
            }
        });
    }

    function save_branch2() {
        if ($("#branchName2").val() == "") {
            alert("Please Provide Branch Name");
        } else {
            var branch = $("#branchName2").val();
            $.ajax({
                data: {
                    branch: branch
                },
                url: branchurl,
                type: 'POST',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    branchTBL.draw();
                    $("#branchName2").val(""); // Clear the input after saving
                    swal({
                        text: "Saved successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                },
                error: function (err) {
                    console.log(err);
                    alert("Something Went Wrong, Please check again");
                }
            });
        }
    }
</script>
@endsection