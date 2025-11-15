<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class PodcastController extends Controller
{
    /**
     * Afficher la liste des podcasts.
     */
    public function index()
    {
        $podcasts = Podcast::all();
        return response()->json($podcasts);
    }

    /**
     * Créer un nouveau podcast.
     */
    public function store(StorePodcastRequest $request)
    {
        $user = Auth::user();

        // Policy
        $this->authorize('create', Podcast::class);

        // Validate data
        $podcastData = $request->validated();

        // Upload image to Cloudinary
        $uploadedFileUrl = Cloudinary::upload(
            $request->file('image')->getRealPath(),
            ['resource_type' => 'image']
        )->getSecurePath();

        $podcastData['image'] = $uploadedFileUrl;
        $podcastData['user_id'] = $user->id;

        $podcast = Podcast::create($podcastData);

        return response()->json($podcast);
    }

    /**
     * Afficher un podcast spécifique.
     */
    public function show(Podcast $podcast)
    {
        if (!$podcast) {
            return response()->json(['message' => 'Podcast introuvable']);
        }

        return response()->json($podcast);
    }

    /**
     * Modifier un podcast.
     */
    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
        // Policy
        $this->authorize('update', $podcast);

        if (!$podcast) {
            return response()->json(['message' => 'Podcast introuvable']);
        }

        $data = $request->validated();

        // Update image
        if ($request->hasFile('image')) {
            if ($podcast->image) {
                $publicId = pathinfo(parse_url($podcast->image, PHP_URL_PATH), PATHINFO_FILENAME);
                Cloudinary::destroy($publicId);
            }

            $uploadedImage = Cloudinary::upload(
                $request->file('image')->getRealPath(),
                ['resource_type' => 'image']
            )->getSecurePath();

            $data['image'] = $uploadedImage;
        }

        // Save update
        $podcast->update($data);

        return response()->json([
            'message' => 'Podcast modifié avec succès',
            'podcast' => $podcast,
        ]);
    }

    /**
     * Supprimer un podcast.
     */
    public function destroy(Podcast $podcast)
    {
        // Policy
        $this->authorize('delete', $podcast);

        if (!$podcast) {
            return response()->json(['message' => 'Podcast introuvable']);
        }

        // Delete image from Cloudinary
        if ($podcast->image) {
            $publicId = pathinfo(parse_url($podcast->image, PHP_URL_PATH), PATHINFO_FILENAME);

            Cloudinary::destroy($publicId, ['resource_type' => 'image']);
        }

        // Delete podcast
        $podcast->delete();

        return response()->json([
            'message' => 'Podcast supprimé avec succès'
        ]);
    }
}
