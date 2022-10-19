@extends('layouts.admin')
@section('content')
@can('user_create')
    <!-- <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.counselorcurrentcases.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.current_cases.title_singular') }}
            </a>
        </div>
    </div> -->
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.current_cases.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table   table-striped   datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.current_cases_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.current_cases_age') }}
                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.current_cases_gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.current_cases_topic') }}
                        </th>
                        <th>
                            {{ trans('cruds.current_cases.fields.current_cases_chat_type') }}
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
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                         
                            <td>
                                @can('counselor_current_cases_show')
                                    <a class=" btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.counselorcurrentcases.show', 1) }}">
                                       <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                @endcan

                                @can('counselor_current_cases_edit')
                                    <a class="btn btn-gradient-primary btn-rounded btn-icon" href="{{ route('admin.counselorcurrentcases.edit', 1) }}">
                                       <i class="fas fa-edit"></i>
                                    </a>
                                @endcan

                                @can('counselor_current_cases_delete')
                                    <form action="{{ route('admin.counselorcurrentcases.destroy', 1) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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