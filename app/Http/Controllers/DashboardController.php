<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Story;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DashboardController extends Controller
{
  public function index()
  {
      //DB::enableQueryLog();
      $query = Story::where('status', 1);
      $type = request()->input('type');
      if(in_array($type, ['short','long'])) {
        $query->where('type', $type);
      }
      $stories = $query->with('user','tags')
          ->orderBy('id', 'DESC')
          ->paginate(10);
      return view('dashboard.index', [
          'stories' => $stories
      ]);
  }

  public function show(Story $activeStory) {
    return view('dashboard.show', [
        'story' => $activeStory
    ]);
  }

  public function email() {
    Mail::raw('This is the test email', function($message) {
      $message->to('admin@localhost.com')
      ->subject('A new story was added');
    });
    dd('here');
  }
}
