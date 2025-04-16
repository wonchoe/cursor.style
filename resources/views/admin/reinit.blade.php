@extends('admin.layouts.app')

@section('title', 'Code Editor')

@section('src_top')
<link rel='stylesheet' href="{{ secure_asset('css/admin/cursors.css') }}">
<link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css">

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


@endsection

@section('cursor_section')
<div id="test_cursor" class="test_cursor">
    <img id="cursor_img" src="javascript:"></img>
</div>
@endsection

@section('content')

<div id="loaderNew" class="loader"><img class="loaderImg" src="{{ secure_asset('images/loader.svg') }}"></img></div>
<!-- Modal -->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" id="new-cat-form" name="new-cat-form" enctype="multipart/form-data" files="true">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Название категории(отображаемая)</label>
                        <input type="text" class="form-control" id="base_name" name="base_name" aria-describedby="emailHelp" placeholder="Мода, брэнды">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Альтернативное название(СЕО)</label>
                        <input type="text" class="form-control" id="alt_name" name="alt_name" placeholder="modabrands">
                    </div>
                    <div class="form-group">
                        <label for="descr">Краткое описание</label>
                        <textarea style="height:150px;" class="form-control" id="short_descr" name="short_descr" placeholder="Описание здесь"></textarea>
                    </div>                    
                    <div class="form-group">
                        <label for="descr">Описание</label>
                        <textarea style="height:150px;" class="form-control" id="descr" name="descr" placeholder="Описание здесь"></textarea>
                    </div>      

                    <div class="form-group">
                        <label for="inputGroupFile01">Логотип категории</label>
                        <div class="input-group">                            
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile01" name="inputGroupFile01"
                                       aria-describedby="inputGroupFileAddon01">
                                <label class="custom-file-label" id="fileLabel" for="inputGroupFile01">Выберите файл</label>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeModal">Закрыть</button>
                    <button type="submit" class="btn btn-primary" id="saveCategories">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>
@foreach($db as $item)

<div class="container-fluid">

    <form id="upload_form" name="upload_form" method="POST" action="/admin/reinit/update">

        <input type="hidden" id="id" name="id" value="{{$item->id}}">
        <input type="hidden" id="oX" name="oX" value="0">
        <input type="hidden" id="oY" name="oY" value="0">
        <input type="hidden" id="oXp" name="oXp" value="0">
        <input type="hidden" id="oYp" name="oYp" value="0">        

        <div class="row" id='pic_row'>
            <div class="col-12 text_align_center paddingr0">
                <input id="c_name" name="c_name" class="form-control" type="text" placeholder="Название курсора" value="{{$item->name}}">
            </div>            

            <div class='col-6'>

                {{ csrf_field() }}
                <div id="uploadContainer">            
                    <div class="row uploadArea">
                        <div class="picContainer">
                            <div class="cursor-upload">
                                <div class="cursor-edit">
                                    <label for="cursorUpload"></label>
                                </div>
                                <div class="cursor-preview" id="cursor_data">

                                    <div id="imagePreviewCursor" style="background: url('/resources/cursors/{{$item->c_file}}'); background-size: contain; ">
                                    </div>
                                </div>
                            </div>

                            <div class="pointer-upload">
                                <div class="pointer-edit">
                                    <label for="pointerUpload"></label>
                                </div>
                                <div class="pointer-preview" id="pointer_data">
                                    <div id="imagePreviewPointer" style="background: url('/resources/pointers/{{$item->p_file}}'); background-size: contain; ">
                                    </div>
                                </div>
                            </div>                
                        </div>
                    </div>
                </div>

            </div>

            <div class='col-6'>
                <div class="row uploadArea" id="test_box">
                    <div class="col-12">
                        <div class="cur-options">
                            <label for="offsetX">Offset X</label><br/>         
                            <input id="offsetX" name="offsetX" type="text"/><br/>

                            <label for="offsetY">Offset Y</label><br/>
                            <input id="offsetY" name="offsetY" type="text"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 text_align_center">
                <div class="test_link" id="test_link_div">
                    <div><a id="test_link" href="#" onClick="alert('click'); return;" class="test_a">This is a test link</a></div>
                    <div style="margin-top:10px">Just a text to select</div>
                </div>
            </div>

            <div class="col-12 text_align_center">
                <button type="submit" class="btn btn-primary btn_save_cursor">Сохранить</button>
            </div>



        </div>
    </form>
</div>
@endforeach
@endsection

@section('src_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/bootstrap-slider.min.js"></script> 
<script src="{{ secure_asset('js/admin/cursors.js') }}"></script> 
<script src="{{ secure_asset('js/admin/upload.js') }}"></script> 

@endsection