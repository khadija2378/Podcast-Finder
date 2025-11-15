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
        $user=Auth::user();
        $this->authorize('create',Podcast::class);
        $podcast=$request->validated();
         $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath(),
            ['resource_type' => 'image'])->getSecurePath();
         $podcast['image']=$uploadedFileUrl;
        $podcast['user_id']=$user->id;
        $datapodcast=Podcast::create($podcast);
        return response()->json($datapodcast);

    }

    public function show(Podcast $podcast)
    {
        if(!$podcast){
            return response()->json(['message'=>'podcast est introuvable']);
        }
        return response()->json($podcast);
    }

    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
        $this->authorize('update',$podcast);
        if(!$podcast){
            return response()->json(['message'=>'podcast est introuvable']);
        }
     $data = $request->validated();

     if ($request->hasFile('image')) {
        if ($podcast->image) {
            $publicId = pathinfo(parse_url($podcast->image, PHP_URL_PATH), PATHINFO_FILENAME);
             Cloudinary::destroy($publicId);
        }
         $uploadedFileUrl = Cloudinary::upload($request->file('image')->getRealPath(),
            ['resource_type' => 'image'])->getSecurePath();
         $data['image'] = $uploadedFileUrl;
     }

    $podcast->update($data);

        return response()->json([
        'message' => 'Podcast est modifié avec succès',
        'podcast' => $podcast
    ]);
    }

    public function destroy(Podcast $podcast)
{
     $this->authorize('delete',$podcast);
     
    if(!$podcast){
            return response()->json(['message'=>'podcast est introuvable']);
        }
    if ($podcast->image) {


        $parsedUrl = parse_url($podcast->image, PHP_URL_PATH);
        $publicId = pathinfo($parsedUrl, PATHINFO_FILENAME);


        Cloudinary::destroy($publicId, ['resource_type' => 'image']);
    }


    $podcast->delete();

    return response()->json([
        "message" => "Podcast est supprimé avec succès"
    ]);
}
}
