@extends('admin.layouts.admin')

@section('content')

<section class="content mt-3" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-12">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Company Informations</h3>
          </div>
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm">
                @if (!empty($company->id))
                    <input type="hidden" id="company_id" name="company_id" value="{{ $company->id }}">
                @endif
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Company name*</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ !empty($company->company_name) ? $company->company_name : '' }}">
                  </div>
                </div>
               <div class="col-sm-4">
                <div class="form-group">
                    <label>Email (1)</label>
                    <input type="email" class="form-control" id="email1" name="email1" value="{{ !empty($company->id) ? $company->email1 : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Email (2)</label>
                    <input type="email" class="form-control" id="email2" name="email2" value="{{ !empty($company->id) ? $company->email2 : '' }}">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Phone (1)</label>
                    <input type="text" class="form-control" id="phone1" name="phone1" value="{{ !empty($company->id) ? $company->phone1 : '' }}">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Phone (2)</label>
                    <input type="text" class="form-control" id="phone2" name="phone2" value="{{ !empty($company->id) ? $company->phone2 : '' }}">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Phone (3)</label>
                    <input type="text" class="form-control" id="phone3" name="phone3" value="{{ !empty($company->id) ? $company->phone3 : '' }}">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Phone (4)</label>
                    <input type="text" class="form-control" id="phone4" name="phone4" value="{{ !empty($company->id) ? $company->phone4 : '' }}">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Address (1)</label>
                    <input type="text" class="form-control" id="address1" name="address1" value="{{ !empty($company->id) ? $company->address1 : '' }}">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Address (2)</label>
                    <input type="text" class="form-control" id="address2" name="address2" value="{{ !empty($company->id) ? $company->address2 : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Website</label>
                    <input type="text" class="form-control" id="website" name="website" value="{{ !empty($company->id) ? $company->website : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Facebook</label>
                    <input type="text" class="form-control" id="facebook" name="facebook" value="{{ !empty($company->id) ? $company->facebook : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Instagram</label>
                    <input type="text" class="form-control" id="instagram" name="instagram" value="{{ !empty($company->id) ? $company->instagram : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Twitter</label>
                    <input type="text" class="form-control" id="twitter" name="twitter" value="{{ !empty($company->id) ? $company->twitter : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>LinkedIn</label>
                    <input type="text" class="form-control" id="linkedin" name="linkedin" value="{{ !empty($company->id) ? $company->linkedin : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Youtube</label>
                    <input type="text" class="form-control" id="youtube" name="youtube" value="{{ !empty($company->id) ? $company->youtube : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Tawkto</label>
                    <input type="text" class="form-control" id="tawkto" name="tawkto" value="{{ !empty($company->id) ? $company->tawkto : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>App store link</label>
                    <input type="text" class="form-control" id="google_appstore_link" name="google_appstore_link" value="{{ !empty($company->id) ? $company->google_appstore_link : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Google playstore link</label>
                    <input type="text" class="form-control" id="google_play_link" name="google_play_link" value="{{ !empty($company->id) ? $company->google_play_link : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Opening Time</label>
                    <input type="time" class="form-control" id="opening_time" name="opening_time" value="{{ !empty($company->id) ? $company->opening_time : '' }}">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Footer Link</label>
                    <input type="text" class="form-control" id="footer_link" name="footer_link" value="{{ !empty($company->id) ? $company->footer_link : '' }}">
                </div>
            </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label>Currency</label>
                    <select class="form-control" id="currency" name="currency">
                        <option value="" disabled selected>Please choose currency</option>
                        <option value="$" @if (!empty($company->currency) && $company->currency == '$') selected @endif>$</option>
                        <option value="£" @if (!empty($company->currency) && $company->currency == '£') selected @endif>£</option>
                        <option value="€" @if (!empty($company->currency) && $company->currency == '€') selected @endif>€</option>
                        <option value="৳" @if (!empty($company->currency) && $company->currency == '৳') selected @endif>৳</option>
                    </select>
                  </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                    <label>About Us</label>
                    <textarea name="about_us" id="about_us" class="form-control summernote" cols="30" rows="3">{{ !empty($company->id) ? $company->about_us : '' }}</textarea>
                </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Footer Content</label>
                        <textarea name="footer_content" id="footer_content" class="form-control" cols="30" rows="3">{{ !empty($company->id) ? $company->footer_content : '' }}</textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Google Map source code</label>
                        <textarea name="google_map" id="google_map" class="form-control" cols="30" rows="3">{{ !empty($company->id) ? $company->google_map : '' }}</textarea>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Fav Icon</label>
                        <input type="file" class="form-control" id="fav_icon" name="fav_icon" onchange="previewImage(this, 'fav_icon_preview')">
                    </div>
                    <div class="card card-outline card-info">
                        <div class="card-body">
                        <img id="fav_icon_preview" src="@if (isset($company->fav_icon)){{ asset('images/company/' . $company->fav_icon) }}@endif" alt="" style="width: 230px">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Logo</label>
                        <input type="file" class="form-control" id="company_logo" name="company_logo" onchange="previewImage(this, 'company_logo_preview')">
                    </div>
                    <div class="card card-outline card-info">
                        <div class="card-body">
                        <img id="company_logo_preview" src="@if (isset($company->company_logo)){{ asset('images/company/' . $company->company_logo) }}@endif" alt="" style="width: 230px">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Footer Logo</label>
                        <input type="file" class="form-control" id="footer_logo" name="footer_logo" onchange="previewImage(this, 'footer_logo_preview')">
                    </div>
                    <div class="card card-outline card-info">
                        <div class="card-body">
                        <img id="footer_logo_preview" src="@if (isset($company->footer_logo)){{ asset('images/company/' . $company->footer_logo) }}@endif" alt="" style="width: 230px">
                        </div>
                    </div>
                </div>
              </div>
              <div class="card-footer">
                <button type="button" class="btn btn-secondary" id="updateBtn">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
@section('script')

<script>
  function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        preview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      preview.src = "";
    }
  }
</script>

<script>
    $(document).ready(function () {

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $("#updateBtn").click(function(event){
            event.preventDefault();
            
            var form_data = new FormData();
            form_data.append("company_name", $("#company_name").val());
            var company_logo = $('#company_logo')[0].files[0];
            var fav_icon = $('#fav_icon')[0].files[0];
            var footer_logo = $('#footer_logo')[0].files[0];

            if (company_logo) {
                form_data.append('company_logo', company_logo);
            }
            if (fav_icon) {
                form_data.append('fav_icon', fav_icon);
            }
            if (footer_logo) {
                form_data.append('footer_logo', footer_logo);
            }

            var aboutUsContent = $('#about_us').summernote('code');
            $('#about_us').val(aboutUsContent);

            form_data.append("address1", $("#address1").val());
            form_data.append("address2", $("#address2").val());
            form_data.append("footer_content", $("#footer_content").val());
            form_data.append("google_map", $("#google_map").val());
            form_data.append("opening_time", $("#opening_time").val());
            form_data.append("phone1", $("#phone1").val());
            form_data.append("phone2", $("#phone2").val());
            form_data.append("phone3", $("#phone3").val());
            form_data.append("phone4", $("#phone4").val());
            form_data.append("email1", $("#email1").val());
            form_data.append("email2", $("#email2").val());

            form_data.append("facebook", $("#facebook").val());
            form_data.append("youtube", $("#youtube").val());
            form_data.append("twitter", $("#twitter").val());
            form_data.append("instagram", $("#instagram").val());
            form_data.append("linkedin", $("#linkedin").val());

            form_data.append("website", $("#website").val());
            form_data.append("footer_link", $("#footer_link").val());
            form_data.append("google_play_link", $("#google_play_link").val());
            form_data.append("google_appstore_link", $("#google_appstore_link").val());
            form_data.append("tawkto", $("#tawkto").val());
            form_data.append("about_us", aboutUsContent);
            form_data.append("currency", $("#currency").val());


            for (let pair of form_data.entries()) {
                console.log(pair[0] + ', ' + pair[1]);
            }

             var companyId = $("#company_id").val();

             if (companyId) {
                url1 = "{{ url('admin/company-detail') }}/" + companyId;
            }

            $.ajax({
                url: companyId ? url1 : "{{ url('admin/company-detail') }}",
                method: companyId ? "PUT" : "POST",
                processData: false, 
                contentType: false,
                data: form_data,
                success: function (response) {
                    if (response.status == 303) {
                        $(".ermsg").html(response.message);
                    } else if (response.status == 300) {
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
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200, 
        });
        $('#about_us').summernote('code', {!! json_encode(!empty($company->about_us) ? $company->about_us : '') !!});
    });
</script>

@endsection