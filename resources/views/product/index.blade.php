@extends('phobrv::layout.app')

@section('header')
<ul>
	<li>
		<a href="{{route('product.create')}}"  class="btn btn-primary float-left">
			<i class="fa fa-edit"></i> @lang('Create new')
		</a>
	</li>
	<li class="text-center">
		{{ Form::open(array('route'=>'product.updateUserSelectGroup','method'=>'post')) }}
		<table class="form" width="100%" border="0" cellspacing="1" cellpadding="1">
			<tbody>
				<tr>
					<td style="text-align:center; padding-right: 10px;">
						<div class="form-group">
							{{ Form::select('select',$data['arrayGroup'],(isset($data['select']) ? $data['select'] : '0'),array('id'=>'choose','class'=>'form-control')) }}
						</div>
					</td>
					<td>
						<div class="form-group">
							<button id="btnSubmitFilter" class="btn btn-primary ">@lang('Filter')</button>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		{{Form::close()}}
	</li>
</ul>
@endsection

@section('content')
<div class="box box-primary">
	<div class="box-body">
		<table id="tableProduct" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class="text-center">#</th>
					<th>{{__('Name')}}</th>
					<th>{{__('Status')}}</th>
					<th>{{__('Lang')}}</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
@endsection

@section('styles')

@endsection

@section('scripts')
<script type="text/javascript">
	var tableProduct =  $('#tableProduct').DataTable({
		lengthMenu: [[15,35,50, -1], [15,35,50, "All"]],
		processing: true,
		serverSide: true,
		ajax: "{{ route('product.getData') }}",
		columns:
		[
		{ data: 'i', name: 'i' ,className:'text-center'},
		{ data: 'title', name: 'title' },
		{ data: 'status', name: 'status', orderable: false, searchable: false,className:'text-center'},
		{ data: 'langButtons', name: 'langButtons', orderable: false, searchable: false,className:'text-center'},
		{ data: 'edit', name: 'edit',orderable: false, searchable: false,className:'text-center'},
		{ data: 'delete', name: 'delete',orderable: false, searchable: false,className:'text-center'},
		]
	})
	function destroy(id){
		var anwser =  confirm("Bạn muốn xóa sản phẩm này?");
		if(anwser){
			$.ajax({
				headers : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
				url: '{{ route('product.apiDelete') }}',
				type: 'POST',
				data: {id},
				success: function (res) {
					if(res.msg == 'success'){
						tableProduct.draw()
					}
				}
			});
		}
	}
</script>
@endsection
