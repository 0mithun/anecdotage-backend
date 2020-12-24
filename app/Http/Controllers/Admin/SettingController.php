<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Models\Traits\UploadAble;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Http\UploadedFile;

class SettingController extends Controller
{
    use UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = Setting::first();
        return \response(new SettingResource($setting));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $setting = Setting::first();
        $setting->update($request->all());

        return response(new SettingResource($setting));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {
        $this->validate($request, [
            'site_logo'   =>  ['required','mimes:png,jpg,svg']
        ]);


        $setting = Setting::first();
        if ($request->has('site_logo') && ($request->file('site_logo') instanceof UploadedFile)) {
            if ($setting->site_logo != null) {
                $this->deleteOne($setting->site_logo);
            }
            $setting->site_logo =  $this->uploadOne($request->file('site_logo'), 'logo','public',uniqid());
            $setting->save();
        }

        return response(new SettingResource($setting));
    }


        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function updateFavicon(Request $request)
    {
        $this->validate($request, [
            'site_favicon'   =>  ['required','mimes:png,ico,svg']
        ]);

        $setting = Setting::first();
        if ($request->has('site_favicon') && ($request->file('site_favicon') instanceof UploadedFile)) {
            if ($setting->site_favicon != null) {
                $this->deleteOne($setting->site_favicon);
            }
            $setting->site_favicon =  $this->uploadOne($request->file('site_favicon'), 'logo','public',uniqid());
            $setting->save();
        }

        return response(new SettingResource($setting));
    }
}
