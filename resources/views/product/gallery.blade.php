<div class="card">

    <form class="form-horizontal GalleryForm" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <input type="hidden" name="product_id" value="{{ $data['post']->id }}">
            <div class="form-group">
                <div class="col-sm-12">
                    @include('phobrv::input.inputImage', ['key' => 'images', 'basic' => true])
                </div>
            </div>
        </div>
        <div class="card-footer">
            {{ Form::submit('Lưu cấu hình', ['class' => 'btn btn-primary pull-right']) }}
        </div>
    </form>

    <div class="card-body">
        <label>Danh sách image trong gallery product</label>
        <div class="row">
            @if (isset($data['gallery']))
                @foreach ($data['gallery'] as $key => $value)
                    <div class="col-md-3 thumb{{ $key }}">
                        <div class="thumb" style="padding:15px; position: relative;">
                            <img src="{{ $value }}" style="width: 100%;height: auto;">
                            <a href="#" onclick="deleteImage('{{ $key }}')" title="Delete image"
                                style="position: absolute; top:5px;right: 5px">
                                <i class="fa fa-times-circle-o" title="Delete image"
                                    style="color: red;font-size: 150%;"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
