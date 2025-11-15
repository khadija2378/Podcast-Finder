<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Podcast;
use App\Models\Épisode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\HttpCache\Store;

class EpisodeController extends Controller
{

    public function index(Podcast $podcast)
    {
        $episodes=$podcast->episodes;

        return response()->json($episodes);
    }

    public function store(StoreEpisodeRequest $request,Podcast $podcast_id)
    {
       $episode=$request->validated();
       $this->authorize('create',Épisode::class);
        $uploadedFileUrl = Cloudinary::upload(
        $request->file('audio')->getRealPath(),['resource_type' => 'video'])->getSecurePath();

        $episode['audio'] = $uploadedFileUrl;


       $podcast=$podcast_id->id;
       $episode['podcast_id']=$podcast;
       $data=Épisode::create($episode);
       return response()->json(['message'=>'Épisode bien ajouté',
                                'episode'=>$data]);
    }


    public function show(Épisode $episode)
    {
        if(!$episode){
            return response()->json(['message'=>'episode est introuvable']);
        }
        return response()->json($episode);
    }


    public function update(UpdateEpisodeRequest $request, Épisode $id)
    {
        if(!$id){
            return response()->json(['message'=>'episode est introuvable']);
        }
        $data = $request->validated();
        if ($request->hasFile('audio')) {
        if ($id->audio) {
            $publicId = pathinfo(parse_url($id->audio, PHP_URL_PATH), PATHINFO_FILENAME);
             Cloudinary::destroy($publicId, ['resource_type' => 'video']);
        }
         $uploadedFileUrl = Cloudinary::upload($request->file('audio')->getRealPath(),
            ['resource_type' => 'video']
        )->getSecurePath();
         $data['audio'] = $uploadedFileUrl;
     }
        $id->update($data);
        return response()->json(['message'=>'Episode modifié avec succès',
                                 'Eposide'=> $id]);
    }


    public function destroy(Épisode $id)
    {
        if(!$id){
            return response()->json(['message'=>'episode est introuvable']);
        }
        if ($id->audio) {

        $parsedUrl = parse_url($id->audio, PHP_URL_PATH);
        $publicId = pathinfo($parsedUrl, PATHINFO_FILENAME);

        Cloudinary::destroy($publicId, ['resource_type' => 'video']);
    }
        $id->delete();
        return response()->json(['message'=>'episode est supprimer']);
    }
}
