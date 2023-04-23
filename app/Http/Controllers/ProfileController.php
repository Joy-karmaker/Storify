<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function edit() {
        $user = auth()->user();
        return view('profiles.edit', [
            'user' => $user
        ]);
    }

    public function update(ProfileRequest $request, User $user){
      $user->name=$request->name;
      $user->save();
      if($user->profile) {
        $user->profile()->update($request->only(['biography','address']));
      }else {
        $user->profile()->create($request->all());
      }

      return redirect()->route('profiles.edit')->with('status', 'Profile Updated Successfully!');
   }
}
