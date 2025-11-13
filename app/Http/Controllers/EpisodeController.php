<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Podcast;
use App\Models\Épisode;
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
       $podcast=$podcast_id->id;
       $episode['podcast_id']=$podcast;
       $data=Épisode::create($episode);
       return response()->json(['message'=>'Épisode bien ajouté',
                                'episode'=>$data]);
    }


    public function show(Épisode $episode)
    {
        return response()->json($episode);
    }


    public function update(UpdateEpisodeRequest $request, Épisode $id)
    {
        $data = $request->validated();
        $id->update($data);
        return response()->json(['message'=>'Episode modifié avec succès',
                                 'Eposide'=> $id]);
    }


    public function destroy(Épisode $id)
    {
        $id->delete();
        return response()->json(['message'=>'episode est supprimer']);
    }
}
