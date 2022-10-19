@extends('layouts.admin')
@section('content')
@can('user_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.tamhubs.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.tamhub.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.tamhub.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.tamhub.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.tamhub.fields.tamhub_name') }}
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                  
                        <tr>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                         
                            <td>
                                @can('tamhub_show')
                                    <a class=" btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.tamhubs.show', 1) }}">
                                       <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endcan

                                @can('tamhub_edit')
                                    <a class="btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.tamhubs.edit', 1) }}">
                                       <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('tamhub_delete')
                                    <form action="{{ route('admin.tamhubs.destroy', 1) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                         <button type="submit" class="btn btn-gradient-primary btn-rounded btn-icon"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                @endcan

                            </td>

                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection