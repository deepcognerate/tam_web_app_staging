@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
       <i class="fas fa-edit"></i> Feature
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.features.update", [$feature->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="feature_name">Feature Name</label>
                <input class="form-control {{ $errors->has('feature_name') ? 'is-invalid' : '' }}" type="text" name="feature_name" id="feature_name" value="{{ old('feature_name', $feature->feature_name) }}" required>
                @if($errors->has('feature_name'))
                    <span class="text-danger">{{ $errors->first('feature_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.category.fields.category_name_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection