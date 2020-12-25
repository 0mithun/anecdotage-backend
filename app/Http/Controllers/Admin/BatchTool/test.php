$request->validate([
    'ban_users_tag' =>  'required',
    'ban_users_tag_type' =>  'required',
],[
    'ban_users_tag.required'  => 'The tag field is required.',
    'ban_users_tag_type.required'  => 'The type field is required.',
]);

if($request->ban_users_tag_type == 1){
    $request->validate([
        'ban_users_tag_days' =>  ['required'],
    ],[
        'ban_users_tag_days.required'  => 'The day field is required.',
    ]);
}

$userIds = Thread::
            where('tag', 'LIKE', "%{$request->ban_users_tag}%")
            ->distinct()
            ->pluck('user_id')
            ->all()
            ;
$this->banUsers($userIds, $request->ban_users_tag_type, $request->ban_users_tag_days);

return response(['success'=> 'true', 'message'=> 'Thread tag added successfully'], Response::HTTP_CREATED);
