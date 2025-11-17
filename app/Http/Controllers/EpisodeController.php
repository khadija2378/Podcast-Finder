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
     * @OA\Get(
     *     path="/api/podcasts/{podcast_id}/episodes",
     *     summary="Get all episodes of a podcast",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of episodes"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function index(Podcast $podcast)
    {
        $episodes = $podcast->episodes;
        return response()->json($episodes);
    }

    /**
     * @OA\Post(
     *     path="/api/podcasts/{podcast_id}/episodes",
     *     summary="Create a new episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","audio"},
     *                 @OA\Property(property="title", type="string", example="Episode 1"),
     *                 @OA\Property(property="description", type="string", example="Episode description"),
     *                 @OA\Property(property="audio", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Episode created successfully"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/episodes/{id}",
     *     summary="Get a specific episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function show(Épisode $episode)
    {
        if (!$episode) {
            return response()->json(['message' => 'Épisode introuvable']);
        }

        return response()->json($episode);
    }

    /**
     * @OA\Put(
     *     path="/api/episodes/{id}",
     *     summary="Update an episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Episode"),
     *                 @OA\Property(property="description", type="string", example="Updated description"),
     *                 @OA\Property(property="audio", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/episodes/{id}",
     *     summary="Delete an episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Episode ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
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

    public function Search($titre){

        $episode=Épisode::where('titre', 'like', "%{$titre}%")->get();

        if($episode->isEmpty()){
            return response()->json(['message' => 'Aucun podcast trouvé']);
        }
        return response()->json($episode);
    }
}
