<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageImage;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = $request->user()->messages()->with('images')->latest()->get();
        return response()->json($messages);
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        $message = DB::transaction(function () use ($data, $request) {
            $message = Message::create([
                'user_id' => $request->user()->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'spotify_link' => $data['spotify_link'] ?? null,
                'is_public' => $data['is_public'] ?? false,
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('message-images', 'public');
                    $message->images()->create([
                        'image_path' => $path
                    ]);
                }
            }

            return $message;
        });

        return response()->json($message->load('images'), 201);
    }

    public function show(Message $message)
    {
        $user = Auth::user();
        if (!$message->is_public && (!$user || $user->id !== $message->user_id)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }
        return response()->json($message->load('images'));
    }

    public function showPublic(Message $message)
    {
        if (!$message->is_public) {
            return response()->json(['message' => 'Esta mensagem é privada'], 403);
        }

        return response()->json($message->load('images'));
    }

    public function update(UpdateMessageRequest $request, Message $message)
    {
        $user = $request->user();
        if ($user->id !== $message->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $data = $request->validated();
        $message->update($data);

        // Optionally handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('messages', 'public');
                $message->images()->create(['image_path' => $path]);
            }
        }

        return response()->json($message->load('images'));
    }

    public function destroy(Request $request, Message $message)
    {
        $user = $request->user();
        if ($user->id !== $message->user_id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        // Delete images from storage
        foreach ($message->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $message->delete();
        return response()->json(['message' => 'Mensagem excluída com sucesso']);
    }
}
