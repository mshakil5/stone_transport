<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactMessageController extends Controller
{
    public function getMessaege()
    {
        $messages = Contact::orderby('id','DESC')->get();
        return view('admin.contactMessage.index' , compact('messages'));
    }
}
