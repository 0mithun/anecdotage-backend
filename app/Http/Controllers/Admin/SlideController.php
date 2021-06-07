<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Thread;
use Illuminate\Http\Request;
use App\Models\SlideCategory;
use App\Jobs\TakeSlideScreenshot;
use App\Models\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Jobs\OptimizeThreadImageJob;
use App\Http\Resources\SlideResource;
use App\Repositories\Contracts\IThread;
use App\Http\Resources\SlideCategoryResource;
use Symfony\Component\HttpFoundation\Response;

class SlideController extends Controller
{
    use UploadAble;

    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }


    public function getSingleSlide(Thread $thread){
        return  new SlideResource($thread);
    }

    public function update(Request $request, Thread $thread){

        $data = $request->only(['slide_body','slide_image_pos','slide_color_bg','slide_color_0','slide_color_1','slide_color_2',]);

        if($request->has('ready') && filter_var($request->ready, FILTER_VALIDATE_BOOLEAN) == true){
            $data['slide_ready'] = 1;
        }
        $thread = $this->threads->update($thread->id, $data);



        if ($request->has('image_path') && ($request->file('image_path') instanceof UploadedFile)) {
            if ($thread->image_path != null) {
                $this->deleteOne($thread->image_path);
            }
            $image_path = $this->uploadOne($request->file('image_path'), 'threads', 'public', $thread->id . uniqid());

            // return response(['status'=>'image found']);
            $thread->image_path =  $image_path;
            $thread->image_path_pixel_color = $this->getImageColorAttribute($image_path);
            $thread->temp_image_url = null;
            $thread->temp_image_description = null;
            $thread->save();

            dispatch(new OptimizeThreadImageJob($image_path, $thread));
        }

        return  response(['success'=> true,], Response::HTTP_ACCEPTED);
    }

    public function takeScreenshot(Request $request, Thread $thread){
        dispatch(new TakeSlideScreenshot($thread));
        return  new SlideResource($thread);
    }





    /**
     * Get Image color attribute from image
     *
     * @param string $image_path
     * @return void
     */
    public function getImageColorAttribute($image_path)
    {
        if ($image_path != '') {
            $splitName = explode('.', $image_path);
            $extension = strtolower(array_pop($splitName));

            $image_path = storage_path('app/public/'.$image_path);

            if ($extension == 'jpg') {
                $im = imagecreatefromjpeg($image_path);
            }
            if ($extension == 'jpeg') {
                $im = imagecreatefromjpeg($image_path);
            } else if ($extension == 'png') {
                $im = imagecreatefrompng($image_path);
            } else if ($extension == 'gif') {
                $im = imagecreatefromgif($image_path);
            }

            if (isset($im)) {
                $rgb = imagecolorat($im, 0, 0);
                $colors = imagecolorsforindex($im, $rgb);
                array_pop($colors);
                array_push($colors, 1);
                $rgbaString = join(', ', $colors);

                return $rgbaString;
            }
        }
        return '';
    }

    public function addCategory(Request $request){
        $this->validate($request, [
            'search_term'       =>  ['required'],
            'display_text'       =>  ['required'],
        ]);


        $category = SlideCategory::create([
            'search_term'       =>  str_slug($request->search_term),
            'display_text'      =>  $request->display_text,
        ]);

        return response(['success' => true, 'category' => $category], Response::HTTP_CREATED);
    }
    public function updateCategory(Request $request, $id){
        $this->validate($request, [
            'search_term'       =>  ['required'],
            'display_text'       =>  ['required'],
        ]);


        $category = SlideCategory::find($id);

        if(!$category){
            return response(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $category->update([
            'search_term'       =>  str_slug($request->search_term),
            'display_text'      =>  $request->display_text,
        ]);



        return response(['success' => true, 'category' => $category], Response::HTTP_ACCEPTED);
    }
}
