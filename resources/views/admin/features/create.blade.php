@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} Feature
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.features.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="name">Feature Name</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="feature_name" id="feature_name" value="{{ old('name', '') }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.category.fields.category_name_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
                @can('category_access')
                    <a class="btn btn-primary" href="{{ route('admin.features.index') }}">
                       <i class="fa fa-eye" aria-hidden="true"></i> Features
                    </a>
                @endcan
            </div>
        </form>
    </div>
</div>
@endsection