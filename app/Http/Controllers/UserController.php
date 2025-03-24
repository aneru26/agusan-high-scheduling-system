<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    
    public function MyAccount()
    {
        $data['getRecord'] = User::getSingle(Auth::user()->id);
        $data['header_title'] = "My Account";
        if(Auth::user()->user_type == 1)
        {
            return view('admin.my_account',$data);
        }
        else if(Auth::user()->user_type == 2)
        {
          
            return view('teacher.my_account',$data);
        }
        else if(Auth::user()->user_type == 3)
        {
            
            return view('dealer.my_account',$data);
        }
    }

    public function UpdateMyAccount(Request $request)
    {
        $id = Auth::user()->id;
        
        request()->validate([
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $user = User::getSingle($id);
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->phone_number = trim($request->phone_number);
        $user->subject = trim($request->subject);
        if(!empty($request->file('profile_pic')))
        {
            if(!empty($student->getProfile))
            {
                unlink('upload/profile/'.$user->profile_pic);
            }
            $ext = $request->file('profile_pic')->getClientOriginalExtension();
            $file = $request->file('profile_pic');
            $randomStr = date('Ymdhis').Str::random(30);
            $filename = strtolower($randomStr).'.'.$ext;
            $file->move(public_path('upload/profile/'), $filename);

            $user->profile_pic = $filename;
        }

        $user->status = is_numeric($request->status) ? (int) $request->status : 0;
        $user->email = trim($request->email);
        
        $user->save();

        return redirect()->back()->with('success',"Account successfully Updated");
    
    }
}
