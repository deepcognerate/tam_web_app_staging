<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Feature;
use App\Models\Category;
use App\Models\ChatFeature;
use App\Models\CounsellorCategories;
use Auth;
class ChangePasswordController extends Controller
{
    public function edit()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sessionCounselorid = Auth::user()->id;
        $categorys = Category::get();
        $feature = Feature::get();
        $selectdCategory = CounsellorCategories::where('counsellor_id',$sessionCounselorid)->whereNull('deleted_at')->get();

        $selectdFeature = ChatFeature::where('counsellor_id',$sessionCounselorid)->whereNull('deleted_at')->select('feature_id')->get();


        $multiFeature = array();
         foreach($selectdFeature as $value){
            $multiFeature[] = $value->feature_id;
        }

        $multiCategory = array();
        foreach($selectdCategory as $value){
            $multiCategory[] = $value->category_id;
        }

        return view('auth.passwords.edit', compact('feature','multiFeature','categorys','multiCategory'));

       
    }

    public function update(UpdatePasswordRequest $request)
    {
        auth()->user()->update($request->validated());

        return redirect()->route('profile.password.edit')->with('message', __('global.change_password_success'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        $user->update($request->validated());

        return redirect()->route('profile.password.edit')->with('message', __('global.update_profile_success'));
    }

    public function destroy()
    {
        $user = auth()->user();

        $user->update([
            'email' => time() . '_' . $user->email,
        ]);

        $user->delete();

        return redirect()->route('login')->with('message', __('global.delete_account_success'));
    }
}
