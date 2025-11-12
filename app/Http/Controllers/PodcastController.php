<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class PodcastController extends Controller
{

    public function index()
    {
        $podcast=Podcast::all();
        return response()->json($podcast);
    }

    public function store(StorePodcastRequest $request)
    {
        $user=Auth::user()->id;
        $podcast=$request->validated();
         $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
         $podcast['image']=$uploadedFileUrl;
        $podcast['user_id']=$user;
        $datapodcast=Podcast::create($podcast);
        return response()->json($datapodcast);

    }

    public function show(Podcast $podcast)
    {
        return response()->json($podcast);
    }

    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
     $data = $request->validated();

     if ($request->hasFile('image')) {
        if ($podcast->image) {
            $publicId = pathinfo(parse_url($podcast->image, PHP_URL_PATH), PATHINFO_FILENAME);
             Cloudinary::destroy($publicId);
        }
         $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath())->getSecurePath();
         $data['image'] = $uploadedFileUrl;
     }

    $podcast->update($data);

        return response()->json([
        'message' => 'Podcast modifié avec succès',
        'podcast' => $podcast
    ]);
    }

    public function destroy(Podcast $podcast)
    {
       if ($podcast->image) {

        $parsedUrl = parse_url($podcast->image, PHP_URL_PATH);
       $publicId = pathinfo($parsedUrl, PATHINFO_FILENAME);

        Cloudinary::destroy($publicId);
     }
        $podcast->delete();
        return response()->json(["message"=>"podcast est supprimer"]);
    }
}
