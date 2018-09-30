@extends('layouts.backend.app')

@section('title','Post')



@push('css')

@endpush



@section('content')

<div class="container-fluid">

    <!-- Vertical Layout | With Floating Label (first row) -->
    <!-- Back button -->
    <a href="{{ route('admin.post.index') }}" class="btn btn-danger waves-effect">BACK</a>

    <!-- Check Approved or Pending -->
    @if($post->is_approved == false)
      <button type="button" class="btn btn-success waves-effect pull-right" onclick="approvePost({{ $post->id }})">
        <i class="material-icons">done</i>
        <span>Approve It</span>
      </button>
      <form method="post" action="{{ route('admin.post.approve', $post->id) }}" id="approval-form" style="display: none;">
        @csrf
        @method('PUT')
      </form>
    @else
      <button type="button" class="btn btn-success pull-right" disabled>
        <i class="material-icons">done</i>
        <span>Approved</span>
      </button>
    @endif

<br/><br/>

    <div class="row clearfix">
      <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
          <div class="card">
              <div class="header">
                  <h2>
                      {{ $post->title }}
                      <small>Posted By <strong> <a href="">{{ $post->user->name }}</a></strong> on {{ $post->created_at->toFormattedDateString() }}</small>
                  </h2>
              </div>

              <div class="body">
                {!! $post->body !!}
              </div><!-- End:body -->

          </div><!-- End:card -->
      </div><!-- End:col-lg-8 -->

      <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
          <div class="card">
              <div class="header bg-cyan">
                  <h2>
                      CATEGORIES
                  </h2>
              </div>

              <div class="body">
                @foreach($post->categories as $category)
                  <span class="label bg-cyan">{{ $category->name }}</span>
                @endforeach
              </div>
          </div><!-- Card 1 -->
          <div class="card">
              <div class="header bg-cyan">
                  <h2>
                      TAGS
                  </h2>
              </div>

              <div class="body">
                @foreach($post->tags as $tag)
                  <span class="label bg-cyan">{{ $tag->name }}</span>
                @endforeach
              </div>
          </div><!-- Card 2 -->
          <div class="card">
              <div class="header bg-cyan">
                  <h2>
                      FEATURED IMAGE
                  </h2>
              </div>

              <div class="body">
                <img class="img-responsive thumbnail" src="{{ Storage::disk('public')->url('post/'.$post->image) }}" alt="">
              </div>
          </div><!-- Card 3 -->

      </div>
    </div><!-- End:row -->
    <!-- Vertical Layout | With Floating Label -->

</div>

@endsection



@push('js')
<!-- Select Plugin Js -->
<script src="{{ asset('assets/backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
<!-- TinyMCE for Body -->
<script src="{{ asset('assets/backend/plugins/tinymce/tinymce.js') }}"></script>

<script type="text/javascript">
$(function () {
  //CKEditor

  //TinyMCE
  tinymce.init({
      selector: "textarea#tinymce",
      theme: "modern",
      height: 300,
      plugins: [
          'advlist autolink lists link image charmap print preview hr anchor pagebreak',
          'searchreplace wordcount visualblocks visualchars code fullscreen',
          'insertdatetime media nonbreaking save table contextmenu directionality',
          'emoticons template paste textcolor colorpicker textpattern imagetools'
      ],
      toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
      toolbar2: 'print preview media | forecolor backcolor emoticons',
      image_advtab: true
  });
  tinymce.suffix = ".min";
  tinyMCE.baseURL = '{{ asset('assets/backend/plugins/tinymce') }}';
});
</script>

<!-- Sweet Alert for delete -->
<script src="https://unpkg.com/sweetalert2@7.24.1/dist/sweetalert2.all.js"></script>
<script type="text/javascript">
  function approvePost(id) {
    const swalWithBootstrapButtons = swal.mixin({

      confirmButtonClass: 'btn btn-success',
      cancelButtonClass: 'btn btn-danger',
      buttonsStyling: false,
    })

    swalWithBootstrapButtons({
      title: 'Are you sure?',
      text: "You want to approve this post !!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, approve it!',
      cancelButtonText: 'No, cancel!',
      reverseButtons: true
    }).then((result) => {
      if (result.value) {
        event.preventDefault();
        document.getElementById('approval-form').submit();
      } else if (
        // Read more about handling dismissals
        result.dismiss === swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons(
          'Cancelled',
          'Post remain pending :)',
          'info'
        )
      }
    })
  }
</script>

@endpush
