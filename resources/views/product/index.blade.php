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
	table =  $('#tableProduct').DataTable({
		lengthMenu: [[15,35,50, -1], [15,35,50, "All"]],
		processing: true,
		serverSide: true,
		ajax: "{{ route('product.getData') }}",
		columns:
		[
		{ data: 'id', name: 'id' ,className:'text-center'},
		{ data: 'title', name: 'title' },
		{ data: 'status', name: 'status', orderable: false, searchable: false,className:'text-center'},
		{ data: 'edit', name: 'edit',orderable: false, searchable: false,className:'text-center'},
		{ data: 'delete', name: 'delete',orderable: false, searchable: false,className:'text-center'},
		]
	})
	function destroy(form){
		var anwser =  confirm("Bạn muốn xóa sản phẩm này?");
		if(anwser){
			event.preventDefault();
			document.getElementById(form).submit();
		}
	}
</script>
@endsection



{{-- <tbody>
	@if($data['products'])
	@foreach($data['products'] as $r)
	<tr>
		<td align="center">{{$loop->index+1}}</td>
		<td><a target="_blank" href="{{ route('level1',['slug'=>$r->slug]) }}">{{$r->title}} </a> </td>
		<td class="list-category">
			@isset($r->terms)
			@foreach($r->terms as $key => $group)
			@isset($data['arrayGroup'][$group->id])
			<span class="comma"> , </span> {{$group->name}}
			@endif
			@endforeach
			@endisset

		</td>
		<td align="center">
			<a href="{{route('product.edit',array('product'=>$r->id))}}"><i class="fa fa-edit" title="Sửa"></i></a>
			&nbsp;&nbsp;&nbsp;
			<a style="color: red" href="#" onclick="destroy('destroy{{$r->id}}')"><i class="fa fa-times" title="Sửa"></i></a>
			<form id="destroy{{$r->id}}" action="{{ route('product.destroy',array('product'=>$r->id)) }}" method="post" style="display: none;">
				@method('delete')
				@csrf
			</form>
		</td>
	</tr>
	@endforeach
	@endif
</tbody> --}}