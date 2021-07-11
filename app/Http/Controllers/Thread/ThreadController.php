<?php

namespace App\Http\Controllers\Thread;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Thread;
use App\Models\Channel;
use Illuminate\Http\Request;
use Spatie\Geocoder\Geocoder;
use App\Jobs\WikiImageProcess;
use App\Jobs\TagImageProcessing;
use App\Models\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Jobs\DownloadThreadImageJob;
use App\Jobs\OptimizeThreadImageJob;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ThreadResource;
use App\Repositories\Contracts\IThread;
use Illuminate\Support\Facades\Storage;
use App\Notifications\DownloadYourImage;
use App\Notifications\ThreadPostTwitter;
use App\Notifications\ThreadPostFacebook;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Http\Resources\SimpleThreadResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Thread\ThreadCreateRequest;
use App\Http\Requests\Thread\ThreadUpdateRequest;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Support\Facades\Request as FacadesRequest;

class ThreadController extends Controller
{
    use UploadAble;
    protected $threads;

    public function __construct(IThread $threads)
    {
        $this->threads = $threads;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = $this->threads->withCriteria([
            new EagerLoad(['emojis', 'channel']),
        ])
        ->select([
            "id",
            "user_id",
            "channel_id",
            "title",
            "slug",
            "body",
            "image_path",
            "image_path_pixel_color",
            "anonymous",
            "location",
            "formatted_address",
            "favorite_count",
            "visits",
            "like_count",
            "dislike_count",
            "created_at",
            "updated_at",
        ])
        ->orderBy('updated_at', 'desc')
        ->paginate((int) request('per_page', 10));
        // return  ThreadResource::collection($threads);
        return  SimpleThreadResource::collection($threads);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadCreateRequest $request)
    {
        $data = $request->only(['body', 'source', 'main_subject', 'age_restriction', 'anonymous', 'famous',
        'slide_body','slide_image_pos','slide_color_bg','slide_color_0','slide_color_1','slide_color_2']);
        // $data['slug'] = str_slug(strip_tags($request->title));

        $data['title'] =  $this->generateTitle($request);
        $data['cno'] =  $this->generateCNO($request);
        $data['formatted_address'] =  $request->location;
        $data['location'] =  $this->generateLocation($request);
        $data['channel_id'] =  $this->generateChannel($request);


        $thread = $this->threads->create($data + ['user_id' => auth()->id()]);

        $this->attachTags($request, $thread);


        //Currently unused
        // $this->uploadSlideImages($request, $thread);

        return response(new ThreadResource($thread), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        $thread->views()->create([]);
        $thread->update(['visits' => $thread->visits  + 1]);

        $thread = $this->threads->withCriteria([
            new EagerLoad(['tags', 'creator', 'emojis', 'channel'])
        ])->find($thread->id);
        return new ThreadResource($thread);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadUpdateRequest $request, Thread $thread)
    {

        $data = $request->only(['body', 'source', 'main_subject', 'age_restriction', 'anonymous',
        'slide_body','slide_image_pos','slide_color_bg','slide_color_0','slide_color_1','slide_color_2']);

        if ($request->has('title') && auth()->user()->is_admin) {
            $title = $request->title;
            $title = preg_replace("#(')#",'',$title);

            $slug = str_slug(strip_tags( $title));
            if($slug != $thread->slug){
                $data['slug'] = $title;
            }
        }





        // $data['title'] = $this->generateTitle($request);
        $data['cno'] =  $this->generateCNO($request);
        $data['title'] =  $this->generateTitle($request);
        $data['formatted_address'] =  $request->location;
        $data['location'] =  $this->generateLocation($request);
        $data['channel_id'] =  $this->generateChannel($request);

        $thread = $this->threads->update($thread->id, $data);
        $this->attachTags($request, $thread);



        //Currently unused
        // $this->uploadSlideImages($request, $thread);


        $this->handleSlideImagePosition($request, $thread);



        // $this->user->notify(new ThreadWasUpdated($this->thread, $reply));

        if (($request->has('scrape_image') && filter_var($request->scrape_image, FILTER_VALIDATE_BOOLEAN) == true) && ($request->has('main_subject') && $request->main_subject != null)) {
            dispatch(new WikiImageProcess($request->main_subject, $thread));
        }

        return response(new ThreadResource($thread), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        Gate::authorize('edit-thread', $thread);
        $this->threads->delete($thread->id);
        return response(null, Response::HTTP_NO_CONTENT);
    }



    /**
     * Generate CNO from request
     * @return String
     */

     public function generateTitle(Request $request){
        return  filter_var($request->title_case, FILTER_VALIDATE_BOOLEAN) == true ?  title_case($request->title): $request->title;
     }
    /**
     * Generate CNO from request
     * @return String
     */

     public function generateCNO(Request $request){
        $data = 'O';
        if ($request->has('cno') && $request->cno != null) {
            $cno = json_decode(json_encode(request('cno')));
            if (filter_var($cno->famous, FILTER_VALIDATE_BOOLEAN) == false) {
                $data= 'O';
            } else if (filter_var($cno->famous, FILTER_VALIDATE_BOOLEAN) == true && filter_var($cno->celebrity, FILTER_VALIDATE_BOOLEAN) == true) {
                $data= 'C';
            } else {
                $data= 'N';
            }
        }

        return $data;
     }





    /**
     * Generate Channel ID from request
     */

     public function generateChannel(Request $request){
        if ($request->has('channel') && $request->channel != null) {
            $channel = json_decode(json_encode(request('channel')));

            $type = gettype($channel);
            if ($type == 'string') {
                $findChannel = Channel::where('name', $channel)->first();
                if ($findChannel) {
                    return $findChannel->id;
                }
            } else {
                return $channel->id;
            }
        }

        return 2;
     }


    /**
     * Generate Location from request
     */

     public function generateLocation(Request $request){
        if ($request->location != null) {
            $location = $this->getGeocodeing($request->location);
            if ($location['accuracy'] != 'result_not_found') {
               return new Point($location['lat'], $location['lng']);

            }
            return null;
        }

        return null;
     }



    /**
     * Get lat, lng with thread location
     */

    public function getGeocodeing($address)
    {
        $client = new \GuzzleHttp\Client();
        $geocoder = new Geocoder($client);
        $geocoder->setApiKey(config('geocoder.key'));
        $geocoder->setCountry(config('geocoder.country', 'US'));
        return $geocoder->getCoordinatesForAddress($address);
    }


    /**
     * Attacg & Save Thread Tags
     */

    public function attachTags($request, $thread)
    {
        $tags = [];
        if ($request->has('tags') && $request->tags != null) {
            // $tags = explode(',', $request->tags);
            $tags = $request->tags;
        }


        if ($request->has('channel') && $request->channel != null) {
            $channel = json_decode(json_encode(request('channel')));
            $type = gettype($channel);
            if ($type == 'string') {
                $findChannel = Channel::where('name', $channel)->first();
                if ($findChannel) {
                    if (!in_array(str_slug($findChannel->name), $tags)) {
                        $tags[] = str_slug($findChannel->name);
                    }
                }
            } else {
                if (!in_array(str_slug($channel->name), $tags)) {
                    $tags[] = str_slug($channel->name);
                }
            }
        }

        if ($request->has('main_subject') && $request->main_subject != null) {
            if (!in_array(str_slug($request->main_subject), $tags)) {
                // $tags[] = str_slug($request->main_subject);
                $tags[] = ($request->main_subject);
            }
        }

        $tag_ids = [];
        foreach ($tags as $tag) {
            if(strtolower($tag) != strtolower('Other')){
                $searchTag = Tag::where('slug', str_slug($tag))->first();

                if ($searchTag) {
                    $tag_ids[] = $searchTag->id;
                } else {
                    if ($tag != 'null') {
                        $newTag = Tag::create(['name' => $tag, 'slug' => str_slug($tag)]);
                        $tag_ids[] = $newTag->id;
                        dispatch(new TagImageProcessing($newTag));
                    }
                }
            }
        }

        $thread->tags()->sync($tag_ids);
        $thread->updateIndex();
    }


    /**
     * Uplod Thread Images
     */

    public function uploadThreadImages(Request $request, Thread $thread)
    {
        if ($request->has('image') && ($request->file('image') instanceof UploadedFile)) {
            if ($thread->image_path != null) {
                $this->deleteOne($thread->image_path);
            }
            $image_path = $this->uploadOne($request->file('image'), 'threads', 'public', $thread->id . uniqid());

            $thread->image_path =  $image_path;
            $thread->image_path_pixel_color = $this->getImageColorAttribute($image_path);
            $thread->is_published = true;
            $thread->temp_image_url = null;
            $thread->temp_image_description = null;
            $thread->save();

            dispatch(new OptimizeThreadImageJob($image_path, $thread));
            return $thread;
        }

        return response('Thumbnail upload successfully');
    }


    /**
     * Uplod Slide Images
     */

    public function uploadSlideImages(Request $request, Thread $thread)
    {
        if ($request->has('slide_image_path') && ($request->file('slide_image_path') instanceof UploadedFile)) {
            if ($thread->slide_image_path != null) {
                $this->deleteOne($thread->slide_image_path);
            }
            $image_path = $this->uploadOne($request->file('slide_image_path'), 'uploads/slides', 'public', $thread->id . uniqid());

            $thread->slide_image_path =  $image_path;
            $thread->save();
        }

    }
    /**
     * handle slide_image_position
     */

    public function handleSlideImagePosition(Request $request, Thread $thread)
    {
        if ($request->has('slide_image_pos') && $request->slide_image_pos != null) {
            if (preg_match("/http/i", $thread->image_path)) {
                // return $this->image_path;
                $thread->update([
                    'temp_image_url'    => $thread->image_path
                ]);

                dispatch(new DownloadThreadImageJob($thread->image_path, $thread));
            }
        }
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

    public function imageDescription(Request $request, Thread $thread)
    {
        $data = [
            'image_description' =>  $request->temp_image_description,
            'amazon_product_url'    =>  $request->amazon_product_url,
        ];
        if($request->temp_image_url == $thread->image_path && $thread->image_path != null){
            $thread->update($data);
            return response('Description Update successfully');
        }

        // if($request->temp_image_url == $thread->temp_image_url){
        //     $thread->update($data);
        //     return response('Description Update successfully');
        // }

        if($request->temp_image_url=='' || $request->temp_image_url== null){
           $thread->update($data + ['temp_image_url'=>$request->temp_image_url]);
            return response('Description Update successfully');
        }

        $data = [
            'temp_image_url'    =>  $request->temp_image_url,
            'temp_image_description'    =>  $request->temp_image_description,
            'image_description'    =>  $request->temp_image_description,
            'amazon_product_url'    =>  $request->amazon_product_url,
            'is_published'    =>  true
        ];
        // $thread->update($request->only(['temp_image_url','temp_image_description'])  + ['image_description'=> $request->temp_image_description ,'is_published' => true]);
        $thread->update($data);

        // WikiImageProcess::dispatch(request('wiki_info_page_url'), $thread, false);
        dispatch(new DownloadThreadImageJob(request('temp_image_url'), $thread));
        auth()->user()->notify(new DownloadYourImage($thread));

        return response('Description Update successfully');
    }



    /**
     * Duplicate image
     */

    public function duplicateImage(Request $request, Thread $thread){

         $old_thread = Thread::where('slug', $request->old_thread)->first();
         if($old_thread){
             $data = [
                'image_path_pixel_color'    =>  $old_thread->image_path_pixel_color,
                'image_description'         =>  $old_thread->image_description,
                'amazon_product_url'        =>  $old_thread->amazon_product_url,
             ];

            if (preg_match("/http/i", $old_thread->image_path)) {
                 $data['image_path'] =  $old_thread->image_path;
            }else{
                $splitName = explode('.', $old_thread->image_path);
                $extension = strtolower(array_pop($splitName));

                $fileName =  join('',$splitName).$thread->id.$extension;

                Storage::disk('public')->copy($old_thread->image_path, $fileName );

                $data['image_path'] =   $fileName;
            }

            $thread = $this->threads->update($thread->id, $data);
            //image_path
            //image_path_pixel_color
            //image_description
            //amazon_product_url
        }
        return response(new ThreadResource($thread), Response::HTTP_ACCEPTED);
    }

    public function skipThumbnailEdit(Request $request, Thread $thread)
    {
        $thread->update(['is_published' => true]);

        return response('Thread Update successfully');
    }



    /**
     * Share Thread to social media
     */

    public function share(Request $request, Thread $thread)
    {
        //Send user Notification
        if ($request->has('share_on_facebook') && $request->share_on_facebook == true) {
            $thread->notify(new ThreadPostFacebook);
        }

        //Send user Notification
        if ($request->has('share_on_twitter') && $request->share_on_twitter == true) {
            $thread->notify(new ThreadPostTwitter);
        }

        return response(['success' => true]);
    }
}
