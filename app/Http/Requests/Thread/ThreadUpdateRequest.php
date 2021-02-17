<?php

namespace App\Http\Requests\Thread;

use App\Models\Thread;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class ThreadUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $thread = Thread::where('slug', $this->route('thread')->slug)->first();
        return Gate::authorize('edit-thread',$thread);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $thread = Thread::where('slug', $this->route('thread')->slug)->first();
        return [
            // 'title' =>  [Rule::unique('threads')->ignore($thread)],
            // 'title' =>  ['required'],
        ];
    }
}
