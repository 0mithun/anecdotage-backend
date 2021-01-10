<?php

namespace App\Http\Controllers\Frontend;

use App\Mail\ContactToAdmin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{


    public function contact(Request $request){
        $this->validate($request, [
            'name'          =>  ['required'],
            'subject'          =>  ['required'],
            'body'          =>  ['required','min:15'],
        ]);

        Mail::to('admin@anecdotage.com')->send(new ContactToAdmin($request->only(['name', 'subject', 'body'])));

        return response(['success'=>true,'message'=>'Contact to admin successfullly']);
    }
}
