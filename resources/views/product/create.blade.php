@extends('phobrv::layout.app')

@section('header')
<a href="{{route('product.index')}}"  class="btn btn-default float-left">
	<i class="fa fa-backward"></i> @lang('Back')
</a>

@endsection

@section('content')
<div class="box box-primary">
	<div class="box-body">
		<div class="row">
			<div class="col-md-8">
				<form class="form-horizontal" id="formSubmit" method="post" action="{{route('product.store')}}"  enctype="multipart/form-data">
					@csrf
					<input type="hidden" id="typeSubmit" name="typeSubmit" value="">
					<input type="hidden" name="lang" value="{{ $data['lang'] ?? 'vi' }}">
					@include('phobrv::input.inputText',['label'=>'Product Name','key'=>'title'])
					<button id="btnSubmit" style="display: none" type="submit" ></button>
				</form>
			</div>
			<div class="col-md-4">
				<a href="#" onclick="update()"  class="btn btn-primary float-left">
					<i class="fa fa-wrench"></i> @lang('Create')
				</a>
			</div>
		</div>
	</div>
</div>

@endsection

@section('styles')

@endsection

@section('scripts')

@endsection
