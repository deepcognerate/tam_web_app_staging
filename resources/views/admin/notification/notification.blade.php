@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
    notification
    </div>

    @if ($message = Session::get('message'))
    <div class="alert alert-success alert-block">
	  <button type="button" class="close" data-dismiss="alert">×</button>	
        <strong>{{ $message }}</strong>
    </div>
   @endif
    <div class="card-body">
    <form action="{{route('add-notification')}}" method="Post" enctype="multipart/form-data">
      @csrf
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Title</label>
    <input type="text" class="form-control" id="" name="title" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Description</label>
    <input type="text" class="form-control" name="description">
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">File</label>
    <input type="file" class="form-control" name="image" >
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
    </div>
</div>

@endsection