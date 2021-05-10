<a id="status{{ $product->id }}" href="javascript:changeStatus('{{ $product->id }}')">
	@if($product->status  == 1)
	<i class="fa fa-check" style="color:green;"></i>
	@else
	<i class="fa fa-times-circle" style="color:red"></i>
	@endif
</a>