<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFeatureRequest;
use App\Http\Requests\StorFeatureRequest;
use App\Http\Requests\UpdatFeatureRequest;
use App\Models\Category;
use App\Models\Feature;
use Gate;
use Session;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        

        if ($request->ajax()) {
            $query = Feature::query()->select(sprintf('%s.*', (new Feature)->table));
            $table = Datatables::of($query);
            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'features_show';
                $editGate      = 'features_edit';
                $deleteGate    = 'features_delete';
                $crudRoutePart = 'features';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : "";
            });
            $table->editColumn('feature_name', function ($row) {
                return $row->feature_name ? $row->feature_name : "";
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.features.index');
    }

    public function create()
    {

        return view('admin.features.create');
    }

    public function store(StorFeatureRequest $request)
    {
        $categorys = Feature::create($request->all());
        Session::flash('message', 'feature Added Successfully...!'); 
        return redirect()->route('admin.features.index');
    }

    public function edit(Feature $feature)
    {
        
        return view('admin.features.edit', compact('feature'));
    }

    public function update(UpdatFeatureRequest $request, Feature $feature)
    {
        $feature->update($request->all());
        Session::flash('message', 'feature Updated Successfully...!'); 
        return redirect()->route('admin.features.index');
    }

    public function show(Feature $category)
    {
        return view('admin.features.show', compact('Feature'));
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();
        return back();
    }

	public function massDestroy(MassDestroyFeatureRequest $request)
    {
        Feature::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
    
}
