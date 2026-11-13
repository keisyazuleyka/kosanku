<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PropertyImageService
{
    public function store(Property $property, array $files): void
    {
        $order = (int) $property->images()->max('sort_order');
        $hasCover = $property->images()->where('is_cover', true)->exists();

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('properties/'.$property->id, 'public');
            $order++;

            PropertyImage::query()->create([
                'property_id' => $property->id,
                'path' => $path,
                'is_external' => false,
                'sort_order' => $order,
                'is_cover' => ! $hasCover && $index === 0,
            ]);

            $hasCover = true;
        }
    }

    public function delete(PropertyImage $image): void
    {
        if (! $image->is_external) {
            Storage::disk('public')->delete($image->path);
        }

        $property = $image->property;
        $wasCover = $image->is_cover;
        $image->delete();

        if ($wasCover) {
            $property->images()->orderBy('sort_order')->first()?->update(['is_cover' => true]);
        }
    }

    public function reorder(Property $property, array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            PropertyImage::query()
                ->where('property_id', $property->id)
                ->where('id', $id)
                ->update(['sort_order' => $index + 1, 'is_cover' => $index === 0]);
        }
    }
}
