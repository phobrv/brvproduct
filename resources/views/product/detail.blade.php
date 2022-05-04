<form class="form-horizontal MetaForm"  enctype="multipart/form-data">
	<input type="hidden" name="menu_id" value="{{ $data['post']->id }}">
	@csrf
	<div class="card-body">
		<div class="row">
			<div class="col-sm-6">
				@include('phobrv::input.inputText',['label'=>'Mã sản phẩm','key'=>'code','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Số lượng','key'=>'count','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Nơi sản xuất','key'=>'madein','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Giá cũ','key'=>'price_old','type'=>'meta'])
			</div>
			<div class="col-sm-6">
				@include('phobrv::input.inputText',['label'=>'Đơn vị','key'=>'unit','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Thương hiệu','key'=>'brand','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Quy cách','key'=>'pack','type'=>'meta'])
				@include('phobrv::input.inputText',['label'=>'Giá bán','key'=>'price','type'=>'meta'])

			</div>
		</div>

		<label class="font16" style="margin-top: 10px;">{{__('Seo Meta')}}</label>
		@include('phobrv::input.inputText',['label'=>'Meta Title','key'=>'meta_title','type'=>'meta'])
		@include('phobrv::input.inputText',['label'=>'Meta Description','key'=>'meta_description','type'=>'meta'])
		@include('phobrv::input.inputText',['label'=>'Meta Keywords','key'=>'meta_keywords','type'=>'meta'])
	</div>
	<div class="card-footer">
		{{ Form::submit('Lưu cấu hình',array('class'=>'btn btn-primary pull-right')) }}
	</div>
</form>
