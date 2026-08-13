<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\TripMemory;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TripMemoryService
{
    public function currentClientId(): int
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 401, 'Unauthenticated.');
        $clientId = $user->client?->id;
        abort_if($clientId === null, 403, 'A client profile is required.');

        return $clientId;
    }

    public function assertMember(Trip $trip): void
    {
        $clientId = $this->currentClientId();
        abort_unless(
            $trip->clients()->whereKey($clientId)->exists(),
            403,
            'You are not a member of this trip.'
        );
    }

    public function addMemory(Trip $trip, string $type, ?string $note, ?UploadedFile $file, ?string $caption): TripMemory
    {
        if ($type === 'note') {
            $content = $note;
        } else {
            $path = $file->store("trip-memories/{$trip->id}", 'cloudinary');
            /** @var Cloud $disk */
            $disk = Storage::disk('cloudinary');
            $content = $disk->url($path);
        }

        return $trip->memories()->create([
            'client_id' => $this->currentClientId(),
            'type' => $type,
            'content' => $content,
            'caption' => $caption,
        ]);
    }

    public function getCapsule(Trip $trip): array
    {
        $clientId = $this->currentClientId();
        $unlocked = now()->greaterThanOrEqualTo(
            $trip->start_date->copy()->addDays($trip->number_of_days)
        );

        if ($unlocked) {
            return ['unlocked' => true, 'memories' => $trip->memories()->with('client:id,phone')->get()];
        }

        return [
            'unlocked' => false,
            'your_memories' => $trip->memories()->where('client_id', $clientId)->get(),
            'others_count' => $trip->memories()->where('client_id', '!=', $clientId)->count(),
        ];
    }

    public function deleteMemory(Trip $trip, TripMemory $memory): void
    {
        $this->assertMember($trip);
        abort_unless($memory->trip_id === $trip->id, 404);
        abort_unless($memory->client_id === $this->currentClientId(), 403);
        $memory->delete();
    }
}
