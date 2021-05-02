@extends('phobrv::layout.app')

@section('header')
<a href="{{route('productitem.index')}}"  class="btn btn-default float-left">
	<i class="fa fa-backward"></i> @lang('Back')
</a>

@endsection

@section('content')
<div class="nav-tabs-custom">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_1" data-toggle="tab">Main</a></li>
		<li><a href="#tab_2" data-toggle="tab">Detail</a></li>
		<li><a href="#tab_3" data-toggle="tab">Gallery</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_1">
			@include('phobrv::product.main')
		</div>
		<div class="tab-pane" id="tab_2">
			@include('phobrv::product.detail')
		</div>
		<div class="tab-pane" id="tab_3">
			@include('phobrv::product.gallery')
		</div>
	</div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')
<script type="text/javascript">
	window.onload = function() {
		CKEDITOR.replace('content', options);
	};

	function deleteImage(meta_id){
		var anwser =  confirm("Bạn muốn image này?");
		if(anwser){
			$.ajax({
				headers : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				url: '{{ route('productitem.deleteMetaAPI') }}',
				type: 'POST',
				data: {meta_id: meta_id},
				success: function (res) {
					console.log(res);
					if(res)
					{
						$('.thumb'+meta_id).css('display','none');
					}
				}
			});
		}
	}

	$('.MetaForm').submit(function(e){
		e.preventDefault();

		var data = {};
		var getData = $(this).serializeArray();
		for(var i=0;i<getData.length;i++){
			if(getData[i]['name']!='_token')
				data[getData[i]['name']] = getData[i]['value'];
		}
		var editors = $(this).find('textarea');
		for(var j=0;j<editors.length;j++)
		{
			var name = editors[j].name;
			if(CKEDITOR.instances[name])
				data[name] = CKEDITOR.instances[name].getData();
		}
		$.ajax({
			headers : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			url: '{{URL::route("menu.updateMetaAPI")}}',
			type: 'POST',
			data: {data: data},
			success: function(output){
				// console.log(output);
				alertOutput(output['msg'],output['message'])
			}
		});
	})

	$('.GalleryForm').submit(function(e){
		e.preventDefault();

		var data = {};
		var getData = $(this).serializeArray();
		for(var i=0;i<getData.length;i++){
			if(getData[i]['name']!='_token')
				data[getData[i]['name']] = getData[i]['value'];
		}
		var editors = $(this).find('textarea');
		for(var j=0;j<editors.length;j++)
		{
			var name = editors[j].name;
			if(CKEDITOR.instances[name])
				data[name] = CKEDITOR.instances[name].getData();
		}
		$.ajax({
			headers : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			url: '{{URL::route("productitem.uploadGallery")}}',
			type: 'POST',
			data: {data: data},
			success: function(output){
				// console.log(output);
				alertOutput(output['msg'],output['message'])
			}
		});
	})

</script>
@endsection