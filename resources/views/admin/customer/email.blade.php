@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row justify-content-md-center">
        <div class="col-md-8">
          <div class="mb-3">
            <a href="{{ route('allcustomer') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back
            </a>
          </div>

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Email</h3>
            </div>
            
            <form id="createThisForm">
              @csrf
              <div class="card-body">

                <div class="text-center mb-4 company-name-container">
                    <h2>{{ $customer->name }}</h2>
                    <h4>{{ $customer->email }}</h4>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Body</label>
                        <textarea name="body" id="body" cols="30" rows="5" class="form-control"></textarea>
                      </div>
                    </div>
                </div>

              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-secondary" id="sendEmailButton">Send</button>
                <div id="loader" style="display: none;">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Loading...
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
</section>

@endsection
@section('script')

<script>
  $(function() {
      $('#body').summernote({
          height: 300,
      });

      $('#createThisForm').on('submit', function(event) {
          event.preventDefault();

          var subject = $('#subject').val();
          var body = $('#body').val();
          var sendButton = $('#sendEmailButton');
          var loader = $('#loader');

          if (!subject || !body) {
              swal("Error", "Please fill all required fields", "error");
              return;
          }

          sendButton.prop('disabled', true);
          loader.show();

          $.ajax({
              url: "{{ route('customer.email.send', $customer->id) }}",
              method: 'POST',
              data: {
                  _token: "{{ csrf_token() }}",
                  subject: subject,
                  body: body
              },
              success: function(response) {
                  if (response.status === 'success') {
                      swal("Success", response.message, "success");
                  } else {
                      swal("Error", response.message, "error");
                  }

                  $('#createThisForm')[0].reset();
                  $('#body').summernote('code', '');
                  $('#subject').val('');
              },
              error: function(xhr) {
                  console.error(xhr.responseText);
                  swal("Error", "There was an error sending the email.", "error");
              },
              complete: function() {
                  sendButton.prop('disabled', false);
                  loader.hide();
              }
          });
      });
  });
</script>

@endsection