<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use App\Rules\CheckSamePassword;
use App\Models\Traits\UploadAble;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    use UploadAble;
    protected $users;
    public function __construct(IUser $users)
    {
        $this->users = $users;
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => ['required'],
            'date_of_birth' =>  ['date'],
            'about' => ['required', 'string', 'min:20'],
            'formatted_address' => ['required'],
            'location.latitude' => ['required', 'numeric', 'min:-90', 'max:90'],
            'location.longitude' => ['required', 'numeric', 'min:-180', 'max:180']
        ]);

        $location = new Point($request->location['latitude'], $request->location['longitude']);


        $user = $this->users->update(auth()->id(), [
            'name' => $request->name,
            'date_of_birth' => $request->date_of_birth,
            'about' => $request->about,
            'formatted_address' => $request->formatted_address,
            'location' => $location,
        ]);

        return new UserResource($user);

    }

    public function updatePassword(Request $request)
    {

        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => ['required', 'confirmed', 'min:6', new CheckSamePassword],
        ]);

        $this->users->update(auth()->id(), [
            'password' => bcrypt($request->password)
        ]);

        return response()->json(['message' => 'Password updated'], 200);

    }

    public function updateAvatar(Request $request){

        $this->validate($request, [
            'avatar_path'       => ['required','mimes:png,jpg,jpeg','max:1024']
        ]);

        $user = auth()->user();

        if ($request->has('avatar_path') && ($request->file('avatar_path') instanceof UploadedFile)) {
            if ($user->avatar_path != null) {
                $this->deleteOne($user->avatar_path);
            }
            $user =  $this->users->update($user->id, [
                'avatar_path'   => $this->uploadOne($request->file('avatar_path'), 'avatars','public',$user->username.uniqid()),
            ]);

            return  \response(new UserResource($user), Response::HTTP_ACCEPTED);
        }
    }

    public function updateAbout(Request $request){
        $this->validate($request, [
            'about'     => ['required']
        ]);

        $user =  $this->users->update(\auth()->id(), [
            'about' =>  $request->about,
        ]);

        return  \response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

}
