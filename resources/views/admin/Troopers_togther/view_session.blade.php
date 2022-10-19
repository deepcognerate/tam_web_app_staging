@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        view session
    </div>
    @if(Session::has('message'))
<p class="alert alert-success">{{ Session::get('message') }}</p>
@endif
    <div class="card-body">
    <table class="table">
  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Session title</th>
      <th scope="col">Session description</th>
      <th scope="col">Start time</th>
      <th scope="col">Duration</th>
      <!-- <th scope="col">counsellor</th> -->
      <!-- <th scope="col">Live chat category</th> -->
      <th scope="col">Session type</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach($data as $datas)
    <tr>
      <th scope="row">{{$loop->index+1}}</th>
      <td>{{$datas->session_title}}</td>
      <td>{!!$datas->session_description!!}</td>
      <td>{{$datas->start_time}}</td>
      <td>{{$datas->Duration}}</td>
      <!-- <td>{{$datas->name}}</td> -->
      <!-- <td>{{$datas->category_name}}</td> -->
      <td>{{$datas->session_type}}</td>
      <td>
          <a class="iconeye btn btn-gradient-primary btn-rounded btn-icon" href="">join</a>
      <a class="iconeye btn btn-gradient-primary btn-rounded btn-icon" href="{{route('edit',[$datas->id])}}"><i class="fas fa-edit"></i></a>
      <a href="{{route('delete',[$datas->id])}}" class="btn btn-danger" style="border-radius: 100px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
    </div>
</div>



@endsection