<?php

namespace App\Services\Restaurant;

use App\Exceptions\CustomBusinessException;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Handle the creation or update of a product
     *
     * @param  array  $validatedData  The validated data from the request
     * @param  int|null  $id  Existing model ID for updates
     * @param  \Illuminate\Http\UploadedFile|null  $imageFile  Image file upload
     * @return array Result containing the model, message, and status
     *
     * @throws CustomBusinessException
     */
    public function handleAddEdit(array $validatedData, ?int $id = null, $imageFile = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = ! empty($id);

            if ($isUpdate) {
                $model = Product::findOrFail($id);

                // Handle image upload
                if ($imageFile) {
                    $imagePath = uploadImage($imageFile, 'products');
                    // $imagePath = $imageFile->store('products', 'public');
                    $validatedData['image'] = $imagePath;
                }
                if (request()->has('delete_image')) {
                    $validatedData['image'] = null;
                }
                $model->update($validatedData);
                $message = t('Product updated successfully.');
            } else {
                // Handle image upload for new product
                if ($imageFile) {
                    $imagePath = uploadImage($imageFile, 'products');
                    // $imagePath = $imageFile->store('products', 'public');
                    $validatedData['image'] = $imagePath;
                }

                $model = Product::create($validatedData);
                $message = t('Product created successfully.');
            }

            // Perform any additional business logic here
            $this->performAdditionalActions($model, $validatedData, $isUpdate);

            DB::commit();

            Log::info('Product '.($isUpdate ? 'updated' : 'created').' successfully', [
                'id' => $model->id,
                'data' => $validatedData,
            ]);

            return [
                'model' => $model->load(['category', 'restaurant', 'sizes']),
                'message' => $message,
                'is_update' => $isUpdate,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Product service', [
                'error' => $e->getMessage(),
                'data' => $validatedData,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Perform additional business logic
     */
    private function performAdditionalActions($model, $validatedData, $isUpdate)
    {
        // Add any additional business logic here
        // For example: sending notifications, updating related models, etc.
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules($validatedData, $id = null)
    {
        // Add custom business validation here
        // Throw CustomBusinessException if validation fails
    }
}
