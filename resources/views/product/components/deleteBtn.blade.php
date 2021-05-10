<a style="color: red" href="#" onclick="destroy('destroy{{$product->id}}')"><i class="fa fa-trash" title="Delete"></i></a>
<form id="destroy{{$product->id}}" action="{{ route('product.destroy',array('product'=>$product->id)) }}" method="product" style="display: none;">
	@method('delete')
	@csrf
</form>