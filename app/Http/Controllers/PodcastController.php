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
 * @OA\Get(
 *     path="/api/podcasts",
 *     summary="Get all podcasts",
 *     tags={"Podcasts"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     )
 * )
 */
    public function index()
    {
        $podcasts = Podcast::all();
        return response()->json($podcasts);
    }

     /**
     * @OA\Post(
     *     path="/api/podcasts",
     *     summary="Create a new podcast",
     *     tags={"Podcasts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","description","image"},
     *                 @OA\Property(property="title", type="string", example="My Podcast"),
     *                 @OA\Property(property="description", type="string", example="Podcast description"),
     *                 @OA\Property(property="image", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Podcast created successfully"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/podcasts/{id}",
     *     summary="Get a specific podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function show(Podcast $podcast)
    {
        if (!$podcast) {
            return response()->json(['message' => 'Podcast introuvable']);
        }

        return response()->json($podcast);
    }

    /**
     * @OA\Put(
     *     path="/api/podcasts/{id}",
     *     summary="Update a podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Title"),
     *                 @OA\Property(property="description", type="string", example="Updated description"),
     *                 @OA\Property(property="image", type="file")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/podcasts/{id}",
     *     summary="Delete a podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Podcast ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
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

    public function Search($titre){

        $podcasts=Podcast::where('titre', 'like', "%{$titre}%")->get();

        if($podcasts->isEmpty()){
            return response()->json(['message' => 'Aucun podcast trouvé']);
        }
        return response()->json($podcasts);
    }
}
