<?php

namespace App\Http\Controllers\Tag;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThreadResource;
use App\Models\Traits\Encoded;
use App\Models\Traits\UploadAble;
use App\Repositories\Contracts\ITag;
use App\Repositories\Contracts\IThread;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    use UploadAble, Encoded;

    protected $tags;
    protected $threads;

    public function __construct(ITag $tags, IThread $threads)
    {
        $this->tags = $tags;
        $this->threads = $threads;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        // $threads = $tag->setRelation('threads', $tag->threads()->paginate());
        // $threads = $tag->threads()->with(['channel', 'emojis'])->paginate(10);
        // return $threads;


        // $threadsId = $tag->threads()->pluck('id')->toArray();
        // $threads = $this->threads->withCriteria([
        //     new EagerLoad(['channel', 'emojis']),
        // ])->findWhereInPaginate('id', $threadsId);


        $tagResponse =  (new TagResource($tag))->additional([
            'data'  => [
                'is_follow'         =>  $tag->is_follow,
                'followers_count'    =>  $tag->followers_count
            ]
        ]);

        $threads = $tag->threads()->with(['channel', 'emojis'])->paginate(10);
        return response(['tag' => $tagResponse->response()->getData(true), 'threads' => ThreadResource::collection($threads)->response()->getData(true)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        $data = $request->only(['name', 'description']);
        if ($request->has('photo') && ($request->file('photo') instanceof UploadedFile)) {
            if ($tag->photo != null) {
                $this->deleteOne($tag->photo);
            }
            $data['photo'] = $this->uploadOne($request->file('photo'), 'tags', 'public', $tag->slug . uniqid());
        }
        $tag = $this->tags->update($tag->id, $data  + ['slug' => str_slug($request->name ?? $tag->name)]);

        return \response(new TagResource($tag), Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        $this->tags->delete($tag->id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function upload($request, $thread)
    {
        if ($request->hasFile('image_path')) {
            $thread_thumb = $thread->image_path;
            if (file_exists($thread_thumb)) {
                unlink($thread_thumb);
            }

            $extension = $request->file('image_path')->getClientOriginalExtension();
            $file_name = $thread->id . "." . $extension;
            $file_path = $request->image_path->storeAs('threads', $file_name);
            $thread->image_path = 'uploads/' . $file_path;
            $thread->image_path_pixel_color = $this->getImageColorAttribute('uploads/' . $file_path);


            $thread->save();
        }
    }


    public function search()
    {
        if (request()->has('q')) {
            $query = request()->q;
            $tags = Tag::where('name', 'LIKE', "$query%")->orderBy('name', 'ASC')->limit(5)->get()->pluck('name');
        } else {
            $tags = Tag::orderBy('name', 'ASC')->limit(5)->get()->pluck('name');
        }

        $tags = $this->convert_from_latin1_to_utf8_recursively($tags->all());
        return $tags;
    }
}
