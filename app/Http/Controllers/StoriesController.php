<?php
namespace App\Http\Controllers;

use App\Events\StoryCreated;
use App\Events\StoryEdited;
use App\Story;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\StoryRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewStoryNotification;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class StoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function __construct()
   {
     $this->authorizeResource(Story::class, 'story');
   }
    public function index()
    {
        //
        $stories = Story::where('user_id', auth()->user()->id)
            ->with('tags')
            ->orderBy('id', 'DESC')
            ->paginate(3);
        return view('stories.index', [
            'stories' => $stories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$this->authorize('create');
        $story = new Story;
        $tags = Tag::get();
        return view('stories.create', [
            'story' => $story,
            'tags' => $tags
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoryRequest $request)
    {
        $story = auth()->user()->stories()->create($request->all());
        if( $request->hasFile('image') ) {
            $this->_uploadImage($request, $story);
        }
        $story->tags()->sync($request->tags);
        Log::info('A story with title '.$story->title.' was added!');
        event( new StoryCreated($story->title));
        return redirect()->route('stories.index')->with('status', 'Story Created Successfully!');
    }

    // public function store(StoryRequest $request) {
    //
    //     if($request->hasfile('image')) {
    //     $file = $request->file('image');
    //     $extension = $file->getClientOriginalExtension(); // getting image extension
    //     $filename =time().'.'.$extension;
    //     $file->move('assets/admin/images', $filename);
    //     }
    //
    //     $story = auth()->user()->stories()->create([
    //     'title'=>$request->title,
    //     'body'=>$request->body,
    //     'type'=>$request->type,
    //     'status'=>$request->status,
    //     'image'=>$filename
    //     ]);
    //
    //     event( new StoryCreated($story->title));
    //     return redirect()->route('stories.index')->with('status', 'Story Created Successfully!');
    //   }

    /**
     * Display the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function show(Story $story)
    {
        //
        return view('stories.show', [
            'story' => $story
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
     public function edit(Story $story)
     {
         $tags = Tag::get();
         return view('stories.edit', [
             'story' => $story,
             'tags' => $tags
         ]);
     }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
     public function update(StoryRequest $request, Story $story)
    {
        // $this->authorize('update', $story);
        $story->update( $request->all() );

        if( $request->hasFile('image') ) {
            $this->_uploadImage($request, $story);
        }
        $story->tags()->sync($request->tags);
        event( new StoryEdited($story->title));

        return redirect()->route('stories.index')->with('status', 'Story Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Story  $story
     * @return \Illuminate\Http\Response
     */
    public function destroy(Story $story)
    {
        //
        $story->delete();
        return redirect()->route('stories.index')->with('status', 'Story Deleted Successfully!');
    }

    private function _uploadImage( $request, $story)
    {
        if( $request->hasFile('image') ) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            Image::make($image)->resize(225, 100)->save(public_path('storage/' . $filename));
            $story->image = $filename;
            $story->save();
        }
    }
}
