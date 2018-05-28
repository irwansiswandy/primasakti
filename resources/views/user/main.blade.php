<head>

<link href="/libraries/bootstrap/dist/css/bootstrap.css" rel="stylesheet"> <!-- BOOTSTRAP CSS -->
<link href="/css/webfontkit-unda/stylesheet.css" rel="stylesheet"> <!-- PRIMASAKTI LOGO FONT -->
<link href="/css/sweetalert.css" rel="stylesheet"> <!-- SWEETALERT CSS -->

</head>

<body>

<br>

<!-- START: REVIEW REQUEST -->
<div class="container">
  @if ($user->wrote_review == false)
  <div class="alert alert-info text-center">
      Anda belum memberikan review tentang pelayanan dari kami. <a data-toggle="modal" data-target="#ReviewModal" style="cursor: pointer"><b>Write Review</b></a>
  </div>
  @endif
</div>
<!-- END: REVIEW REQUEST -->

<!-- START: REVIEW_FORM MODAL -->
<div id="ReviewModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h1 class="modal-title text-center" style="font-family: unda_angleitalic">
          PRIMASAKTI <small>REVIEW
        </h1>
      </div>
      <div class="modal-body">
        <form method="POST" v-on:submit.prevent="submitReview">
          <input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
          <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" class="form-control" v-model="review.title">
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="8" class="form-control" v-model="review.description"></textarea>
          </div>
          <div class="form-group">
            {!! Form::label('score', 'Score') !!}
            {!! Form::selectRange('score', 0, 10, null, ['class' => 'form-control', 'v-model' => 'review.score']) !!}
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary form-control">Submit This Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- END: REVIEW_FORM MODAL -->

<script src="/js/jquery-2.2.0.js"></script>
<script src="/libraries/bootstrap/dist/js/bootstrap.js"></script>
<script src="/js/sweetalert.min.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>

<!-- START: VUE SCRIPT FOR REVIEW_MODAL -->
<script>
  var reviewVue = new Vue({
    el: '#ReviewModal',
    http: {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
      }
    },
    data: {
      review: {
        title: '',
        description: '',
        score: ''
      }
    },
    methods: {
      submitReview: function() {
        var submittedReview = this.review;
        if (this.$http.post(document.URL + '/submitReview', submittedReview)) {
          swal({
            type: "success",
            title: "Your review has been submitted",
            text: "Thank you for your time",
            timer: 3000,
            showConfirmButton: false
          });
          return location.reload()
        }
      }
    }
  });
</script>
<!-- END: VUE SCRIPT FOR REVIEW_MODAL -->

</body>