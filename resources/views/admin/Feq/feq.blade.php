@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Frequently Asked Questions
    </div>
    <div><br>
          <button class="btn btn-success"data-toggle="modal" data-target="#add_modal" style="margin-left: 20px;">Add Faq</i></i></button>
    </div>

    <div class="card-body">
    <table class="table">
  <thead>
    <tr>
      <th scope="col">S.No</th>
      <th scope="col">Question</th>
      <th scope="col">Answer</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $a)
    <tr>
      <th scope="row">{{$loop->index+1}}</th>
      <td>{{$a->question}}</td>
      <td>{{$a->ans}}</td>
      <td>
        <button class="btn btn-success"data-toggle="modal" data-target="#edit_modal-{{$a->id}}" style="border-radius: 100px;"><i class="fas fa-edit" aria-hidden="true"></i></button>
        {{-- <button class="btn btn-danger"data-toggle="modal" data-target="#delete_modal-{{$a->id}}">delete</button> --}}
        <a href="{{route('feq_delete',[$a->id])}}" class="btn btn-danger" style="border-radius: 100px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
    </div>
</div>

<!--add_Modal -->
<div class="modal fade" id="add_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
     <div class="modal-content">
       <div class="modal-header">
         <h5 class="modal-title" id="exampleModalLabel">Add Faq</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
         </button>
       </div>
       <div class="modal-body">

         <form action="{{route('feq_store')}}" method="post" id="" enctype="multipart/form-data" class="form-horizontal">
           @csrf
                        <div class="row form-group">
                           <div class="col col-md-3"><label class=" form-control-label">Question</label>
                           </div>
                           <div class="col-12 col-md-9">
                               <input type="text" class="form-control"  name="question">
                           </div>
                       </div>
                     <div class="row form-group">
                       <div class="col col-md-3"><label class=" form-control-label">Answer</label>
                       </div>
                       <div class="col-12 col-md-9">
                           <textarea name="ans" id="" cols="30" rows="10" class="form-control"></textarea>
                       </div>
                   </div>
                   <div class="text-center">
                    <input type="submit" class="btn btn-info" value="submit">
                   </div>
    
                    </form>
       </div>
     </div>
    </div>
 </div><!----add modal end----->
 <!--edit_Modal -->
 @foreach ($data as $data)
<div class="modal fade" id="edit_modal-{{$data->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">edit</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{route('feq_update',[$data->id])}}" method="post" id="" enctype="multipart/form-data" class="form-horizontal">
              @csrf
              @method('PUT')
              <div class="row form-group">
                <div class="col col-md-3"><label class=" form-control-label">Question</label>
                </div>
                <div class="col-12 col-md-9">
                    <input type="text" class="form-control"  name="question" value="{{$data->question}}">
                </div>
            </div>
          <div class="row form-group">
            <div class="col col-md-3"><label class=" form-control-label">Answer</label>
            </div>
            <div class="col-12 col-md-9">
                <textarea name="ans" id="" cols="30" rows="10" class="form-control">{{$data->ans}}</textarea>
            </div>
        </div>
        <div class="text-center">
         <input type="submit" class="btn btn-info" value="submit">
        </div>
                 </form>
            </div>
        </div>
    </div>
</div><!----edit modal end----->
@endforeach
@endsection