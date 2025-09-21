<?php

namespace App\Services\Restaurant;

use App\Exceptions\CustomBusinessException;
use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantService
{
    /**
     * Handle the creation or update of a restaurant
     *
     * @param  array  $validatedData  The validated data from the request
     * @param  int|null  $id  Existing restaurant ID for updates
     * @param  UploadedFile|null  $logoFile  Logo file if provided
     * @return array Result containing the restaurant, message, and status
     *
     * @throws CustomBusinessException
     */
    public function handleAddEdit(array $validatedData, ?int $id = null, ?UploadedFile $logoFile = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = ! empty($id);

            // Handle logo upload
            if ($logoFile) {
                $logoPath = $this->handleLogoUpload($logoFile, $id);
                $validatedData['logo'] = $logoPath;
            }

            // Generate slug if not provided
            if (empty($validatedData['slug']) && isset($validatedData['name'])) {
                $name = $this->getNameFromTranslatableField($validatedData['name']);
                $validatedData['slug'] = Restaurant::generateUniqueSlug($name);
            }

            if ($isUpdate) {
                $restaurant = Restaurant::findOrFail($id);

                // Delete old logo if a new one is uploaded
                if ($logoFile && $restaurant->logo && Storage::disk('public')->exists($restaurant->logo)) {
                    Storage::disk('public')->delete($restaurant->logo);
                }

                $restaurant->update($validatedData);
                $message = t('Restaurant updated successfully.');
            } else {
                $restaurant = Restaurant::create($validatedData);
                $message = t('Restaurant created successfully.');
            }

            DB::commit();

            Log::info('Restaurant '.($isUpdate ? 'updated' : 'created').' successfully', [
                'id' => $restaurant->id,
                'name' => $restaurant->getTranslatedName(),
                'slug' => $restaurant->slug,
            ]);

            return [
                'model' => $restaurant,
                'message' => $message,
                'is_update' => $isUpdate,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if there was an error
            if ($logoFile && isset($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }

            Log::error('Error in Restaurant service', [
                'error' => $e->getMessage(),
                'data' => $validatedData,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle logo file upload
     *
     * @return string The stored file path
     */
    private function handleLogoUpload(UploadedFile $logoFile, ?int $restaurantId = null): string
    {
        try {
            // Create a unique filename
            $filename = time().'_'.Str::random(10).'.'.$logoFile->getClientOriginalExtension();

            // Store in the restaurants directory
            $path = $logoFile->storeAs('restaurants/logos', $filename, 'public');

            Log::info('Logo uploaded successfully', [
                'restaurant_id' => $restaurantId,
                'filename' => $filename,
                'path' => $path,
            ]);

            return $path;

        } catch (\Exception $e) {
            Log::error('Error uploading restaurant logo', [
                'restaurant_id' => $restaurantId,
                'error' => $e->getMessage(),
            ]);
            throw new CustomBusinessException(
                t('Failed to upload logo. Please try again.'),
                422
            );
        }
    }

    /**
     * Extract a name from translatable field for slug generation
     *
     * @param  array|string  $nameField
     */
    private function getNameFromTranslatableField($nameField): string
    {
        if (is_array($nameField)) {
            // Try Arabic first, then English, then any available
            return $nameField['ar'] ?? $nameField['en'] ?? array_values($nameField)[0] ?? 'restaurant';
        }

        return $nameField ?? 'restaurant';
    }

    /**
     * Validate business rules for restaurants
     *
     * @throws CustomBusinessException
     */
    private function validateBusinessRules(array $validatedData, ?int $id = null): void
    {
        // Check for duplicate slug if provided
        if (! empty($validatedData['slug'])) {
            $query = Restaurant::where('slug', $validatedData['slug']);

            if ($id) {
                $query->where('id', '!=', $id);
            }

            if ($query->exists()) {
                throw new CustomBusinessException(
                    t('A restaurant with this slug already exists.'),
                    422,
                    ['slug' => $validatedData['slug']]
                );
            }
        }

        // Check for duplicate email if provided
        if (! empty($validatedData['email'])) {
            $query = Restaurant::where('email', $validatedData['email']);

            if ($id) {
                $query->where('id', '!=', $id);
            }

            if ($query->exists()) {
                throw new CustomBusinessException(
                    t('A restaurant with this email already exists.'),
                    422,
                    ['email' => $validatedData['email']]
                );
            }
        }

        // Validate at least one service is available
        $hasAnyService = ($validatedData['delivery_available'] ?? false) ||
                        ($validatedData['pickup_available'] ?? false) ||
                        ($validatedData['dine_in_available'] ?? false);

        if (! $hasAnyService) {
            throw new CustomBusinessException(
                t('Restaurant must have at least one service available (delivery, pickup, or dine-in).'),
                422
            );
        }
    }

    /**
     * Validate opening hours structure
     *
     * @throws CustomBusinessException
     */
    private function validateOpeningHours(array $openingHours): void
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($openingHours as $day => $hours) {
            if (! in_array($day, $validDays)) {
                throw new CustomBusinessException(
                    t('Invalid day specified in opening hours: :day', ['day' => $day]),
                    422
                );
            }

            if (is_array($hours)) {
                // If not closed, validate open and close times
                if (! isset($hours['closed']) || ! $hours['closed']) {
                    if (! isset($hours['open']) || ! isset($hours['close'])) {
                        throw new CustomBusinessException(
                            t('Opening and closing times are required for :day', ['day' => ucfirst($day)]),
                            422
                        );
                    }

                    // Validate time format (basic check)
                    if (! preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $hours['open']) ||
                        ! preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $hours['close'])) {
                        throw new CustomBusinessException(
                            t('Invalid time format for :day. Use HH:MM format.', ['day' => ucfirst($day)]),
                            422
                        );
                    }

                    // Check that closing time is after opening time
                    if (strtotime($hours['close']) <= strtotime($hours['open'])) {
                        throw new CustomBusinessException(
                            t('Closing time must be after opening time for :day', ['day' => ucfirst($day)]),
                            422
                        );
                    }
                }
            }
        }
    }
}
