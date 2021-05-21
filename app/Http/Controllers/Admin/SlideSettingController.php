<?php

namespace App\Http\Controllers\Admin;

use App\Models\SlideSetting;
use Illuminate\Http\Request;
use App\Models\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Http\Resources\SlideSettingResource;

class SlideSettingController extends Controller
{
    use UploadAble;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting = SlideSetting::first();
        return \response(new SlideSettingResource($setting));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SlideSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $setting = SlideSetting::first();
        $setting->update($request->except(['site_logo','site_favicon']));

        return response(new SlideSettingResource($setting));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SlideSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {
        $this->validate($request, [
            'site_logo'   =>  ['required','mimes:png,jpg,svg']
        ]);


        $setting = SlideSetting::first();
        if ($request->has('site_logo') && ($request->file('site_logo') instanceof UploadedFile)) {
            if ($setting->site_logo != null) {
                $this->deleteOne($setting->site_logo);
            }
            $setting->site_logo =  $this->uploadOne($request->file('site_logo'), 'slides_logo','public',uniqid());
            $setting->save();
        }

        return response(new SlideSettingResource($setting));
    }


        /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SlideSetting  $setting
     * @return \Illuminate\Http\Response
     */
    public function updateFavicon(Request $request)
    {
        $this->validate($request, [
            'site_favicon'   =>  ['required','mimes:png,ico,svg']
        ]);

        $setting = SlideSetting::first();
        if ($request->has('site_favicon') && ($request->file('site_favicon') instanceof UploadedFile)) {
            if ($setting->site_favicon != null) {
                $this->deleteOne($setting->site_favicon);
            }
            $setting->site_favicon =  $this->uploadOne($request->file('site_favicon'), 'slides_logo','public',uniqid());
            $setting->save();
        }

        return response(new SlideSettingResource($setting));
    }
}
