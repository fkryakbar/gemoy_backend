<?php

namespace App\Http\Controllers;

use App\Models\PostModel;
use Illuminate\Http\Request;

class PublicController extends Controller
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
        $posts = PostModel::with('get_media')->latest()->paginate(10);

        return response()->json($this->payload($posts, 200, 'Posts Fetched'));
    }
}
