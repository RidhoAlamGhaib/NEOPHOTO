<?php

namespace App\Services;

use App\Models\CustomerMembership;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use RuntimeException;
use Throwable;

class MemberProfilePictureService
{
    private const MAX_BYTES = 2 * 1024 * 1024;

    private const MAX_DIMENSION = 1200;

    private const MIN_DIMENSION = 64;

    /**
     * Re-encode and store a profile image as private WebP, capped at 2 MB.
     */
    public function replace(CustomerMembership $membership, UploadedFile $upload): void
    {
        $image = ImageManager::withDriver(GdDriver::class)->read($upload->getRealPath());
        $image->scaleDown(self::MAX_DIMENSION, self::MAX_DIMENSION);

        $encodedImage = $this->encodeWithinLimit($image);
        $path = sprintf(
            'members/profile-pictures/%s/%s.webp',
            $membership->getKey(),
            Str::uuid(),
        );
        $disk = Storage::disk('local');

        if (! $disk->put($path, $encodedImage)) {
            throw new RuntimeException('Unable to store the member profile picture.');
        }

        $previousPath = $membership->profile_picture_path;

        try {
            $membership->profile_picture_path = $path;
            $membership->saveOrFail();
        } catch (Throwable $exception) {
            if (! $disk->delete($path)) {
                Log::warning('Failed to clean up an unlinked member profile picture.', [
                    'membership_id' => $membership->getKey(),
                    'path' => $path,
                ]);
            }

            throw $exception;
        }

        if (
            is_string($previousPath)
            && $previousPath !== $path
            && $disk->exists($previousPath)
            && ! $disk->delete($previousPath)
        ) {
            Log::warning('Failed to remove a replaced member profile picture.', [
                'membership_id' => $membership->getKey(),
                'path' => $previousPath,
            ]);
        }
    }

    private function encodeWithinLimit(\Intervention\Image\Interfaces\ImageInterface $image): string
    {
        do {
            foreach ([85, 70, 55, 40, 25, 10] as $quality) {
                $encoded = $image->toWebp($quality)->toString();

                if (strlen($encoded) <= self::MAX_BYTES) {
                    return $encoded;
                }
            }

            $longestSide = max($image->width(), $image->height());

            if ($longestSide <= self::MIN_DIMENSION) {
                break;
            }

            $nextDimension = max(
                self::MIN_DIMENSION,
                (int) floor($longestSide * 0.75),
            );
            $image->scaleDown($nextDimension, $nextDimension);
        } while (true);

        throw new RuntimeException('Unable to compress the profile picture below 2 MB.');
    }
}
