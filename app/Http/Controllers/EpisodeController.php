<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Podcast;
use App\Models\Épisode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class EpisodeController extends Controller
{
    /**
     * Afficher tous les épisodes d’un podcast.
     */
    public function index(Podcast $podcast)
    {
        $episodes = $podcast->episodes;
        return response()->json($episodes);
    }

    /**
     * Créer un nouvel épisode.
     */
    public function store(StoreEpisodeRequest $request, Podcast $podcast)
    {
        // Policy
        $this->authorize('create', Épisode::class);

        // Validate data
        $episodeData = $request->validated();

        // Upload audio to Cloudinary
        $uploadedAudioUrl = Cloudinary::upload(
            $request->file('audio')->getRealPath(),
            ['resource_type' => 'video']
        )->getSecurePath();

        $episodeData['audio'] = $uploadedAudioUrl;
        $episodeData['podcast_id'] = $podcast->id;

        $episode = Épisode::create($episodeData);

        return response()->json([
            'message' => 'Épisode bien ajouté',
            'episode' => $episode
        ]);
    }

    /**
     * Afficher un épisode spécifique.
     */
    public function show(Épisode $episode)
    {
        if (!$episode) {
            return response()->json(['message' => 'Épisode introuvable']);
        }

        return response()->json($episode);
    }

    /**
     * Modifier un épisode.
     */
    public function update(UpdateEpisodeRequest $request, Épisode $episode)
    {
        // Policy
        $this->authorize('update', $episode);

        if (!$episode) {
            return response()->json(['message' => 'Épisode introuvable']);
        }

        $data = $request->validated();

        // Update audio if new file uploaded
        if ($request->hasFile('audio')) {
            if ($episode->audio) {
                $publicId = pathinfo(parse_url($episode->audio, PHP_URL_PATH), PATHINFO_FILENAME);
                Cloudinary::destroy($publicId, ['resource_type' => 'video']);
            }

            $uploadedAudioUrl = Cloudinary::upload(
                $request->file('audio')->getRealPath(),
                ['resource_type' => 'video']
            )->getSecurePath();

            $data['audio'] = $uploadedAudioUrl;
        }

        $episode->update($data);

        return response()->json([
            'message' => 'Épisode modifié avec succès',
            'episode' => $episode
        ]);
    }

    /**
     * Supprimer un épisode.
     */
    public function destroy(Épisode $episode)
    {
        // Policy
        $this->authorize('delete', $episode);

        if (!$episode) {
            return response()->json(['message' => 'Épisode introuvable']);
        }

        // Delete audio file
        if ($episode->audio) {
            $publicId = pathinfo(parse_url($episode->audio, PHP_URL_PATH), PATHINFO_FILENAME);
            Cloudinary::destroy($publicId, ['resource_type' => 'video']);
        }

        $episode->delete();

        return response()->json([
            'message' => 'Épisode supprimé avec succès'
        ]);
    }
}
