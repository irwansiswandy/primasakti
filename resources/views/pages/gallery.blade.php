@extends('app')

@section('css')

<link href="/css/lightbox.css" rel="stylesheet">

@stop

@section('content')

<!-- START: DISPLAY PHOTOS -->
<div id="galleryController">
	<!-- START: OPTIONS FOR CATEGORY -->
	<ol class="breadcrumb" v-on:click="selectCategory(category.id)">
		<li style="cursor: pointer" v-for="category in categories">
			@{{ category.name }}
		</li>
	</ol>
	<!-- END: OPTIONS FOR CATEGORY -->
	<!-- START: DISPLAY ALL PHOTOS -->
	<div class="row">
		<div v-for="category in categories">
			<div class="col-xs-10 col-sm-2" v-for="photo in category.photos">
				<a :href="photo.file_path" data-lightbox="all" class="thumbnail">
					<img :src="photo.thumb_path">
				</a>
			</div>
		</div>
	</div>
	<!-- END: DISPLAY ALL PHOTOS -->
</div>
<!-- END: DISPLAY PHOTOS -->

@stop

@section('js')

<script src="/js/lightbox.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script>
	new Vue({
		el: '#galleryController',
		data: {
			categories: [],
			selectedCategory: {
				id: ''
			}
		},
		methods: {
			getAllCategoriesWithPhotos: function() {
				this.$http.get(document.URL + '/photos/all', function(data) {
					this.$set('categories', data)
				})
			},
			selectCategory: function(id) {
				this.selectedCategory.id = id
			}
		},
		ready: function() {
			this.getAllCategoriesWithPhotos()
		}
	});
</script>

@stop