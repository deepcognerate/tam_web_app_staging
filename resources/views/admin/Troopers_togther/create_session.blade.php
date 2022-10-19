@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        Create session
    </div>
    @if(Session::has('message'))
<p class="alert alert-success">{{ Session::get('message') }}</p>
@endif

    <div class="card-body">
        <form action="{{url('store')}}" method="POST">
            @csrf
            <div class="form-group">
                <label class="required" for="title">Session title</label>
                <input class="form-control " type="text" name="session_title"  required>
            </div>
            <div class="form-group">
                <label class="required" for="title">Session description</label>
                <textarea name="session_description" class="form-control " id="editor"></textarea>
                <!-- <input class="form-control " type="text" name="session_description"  required> -->
            </div>
            <div class="form-group">
                <label class="required" for="title">Start time</label>
                <input class="form-control " type="time" name="start_time"  required>
            </div>
            <div class="form-group">
                <label class="required" for="title">Duration</label>
                <input class="form-control " type="text" name="duration"  required>
            </div>
            <div class="form-group">
                <label class="required" for="category_name">Counselor</label>
                       
                <select class="form-control select2 {{ $errors->has('counselor_name') ? 'is-invalid' : '' }}" name="counselor_id[]" id="counselor_id" multiple required>
                @if(!empty($counselor))   
                @foreach($counselor as $id => $counselor)
                        <option value="{{ $counselor->id }}" {{ old('counselor_id') == $id ? 'selected' : '' }}>{{ $counselor->name }}</option>
                    @endforeach
                @endif
                </select>
                 @if($errors->has('category'))
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                @endif
            </div>
            <div class="form-group">
                <label class="required" for="category_name">Live chat Category</label>
                       
                <select class="form-control select2 {{ $errors->has('category_name') ? 'is-invalid' : '' }}" name="category_id[]" id="category_id" multiple required>
                @if(!empty($categorys))   
                @foreach($categorys as $id => $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                    @endforeach
                @endif
                </select>               
                 @if($errors->has('category'))
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.counselor.fields.category_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="">session type</label>
                <br>
                <label class="required" for="title">Chat 
                <input class=" " type="radio" name="session_type" value="chat" >
                </label>
                <br>
                <label class="required" for="title">Audio
                <input class=" " type="radio" name="session_type" value="Audio">
                </label>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-info">submit</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/ckeditor.js"></script>
<script>
                        ClassicEditor
                                .create( document.querySelector( '#editor' ) )
                                .then( editor => {
                                        console.log( editor );
                                } )
                                .catch( error => {
                                        console.error( error );
                                } );
                </script>
@endsection