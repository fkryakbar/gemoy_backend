<?php

namespace App\Http\Controllers;

use App\Models\MediaModel;
use App\Models\PostModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    private function payload($data = [], $code = 200, $message = 'Success')
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    public function index(Request $request)
    {
        $posts = PostModel::where('user_id', $request->user()->username)->with('get_media')->latest()->get();
        if (count($posts) > 0) {
            return response()->json($this->payload([
                'posts' => $posts
            ], 200, 'Post Fetched'));
        }
        throw ValidationException::withMessages([
            'post' => 'Post Not Found'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:60'],
            'caption' => ['required'],
            'media' => 'required',
            'media.*' => 'mimes:jpeg,jpg,png,mov,mp4',
        ]);
        $request->merge(['user_id' => $request->user()->username]);
        $post = PostModel::create($request->except(['media']));
        foreach ($request->file('media') as $key => $file) {
            $path = $file->store('');
            MediaModel::create([
                'post_id' => $post->id,
                'media_type' => $file->getMimeType(),
                'media_path' => $path
            ]);
        }
        $post = PostModel::where('user_id', $request->user()->username)->where('id', $post->id)->with('get_media')->first();
        return response()->json($this->payload([
            'post' => $post
        ]));
    }

    public function delete(Request $request)
    {
        $request->validate([
            'post_id' => 'required'
        ]);

        $post = PostModel::where('id', $request->post_id)->where('user_id', $request->user()->username)->first();
        if ($post) {
            $media = $post->get_media;
            foreach ($media as $key => $file) {
                Storage::delete($file->media_path);
            }
            MediaModel::where('post_id', $request->post_id)->delete();
            $post->delete();
            return response()->json($this->payload([], 200, 'Post Deleted'));
        }

        throw ValidationException::withMessages([
            'post' => 'Post Not Found'
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => ['required', 'max:60'],
            'caption' => ['required'],
            'post_id' => ['required']
        ]);

        $post = PostModel::where('id', $request->post_id)->where('user_id', $request->user()->username)->first();
        if ($post) {
            $post->update($request->except(['post_id']));

            return response()->json($this->payload([
                'post' => $post
            ], 200, 'Post Updated'));
        }


        throw ValidationException::withMessages([
            'post' => 'Post Not Found'
        ]);
    }
}
