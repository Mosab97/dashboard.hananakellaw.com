<?php

namespace App\Services\Restaurant;

use App\Exceptions\CustomBusinessException;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryService
{
    /**
     * Handle the creation or update of a category
     *
     * @param  array  $validatedData  The validated data from the request
     * @param  int|null  $id  Existing category ID for updates
     * @param  UploadedFile|null  $imageFile  Image file if provided
     * @param  UploadedFile|null  $iconFile  Icon file if provided
     * @return array Result containing the category, message, and status
     *
     * @throws CustomBusinessException
     */
    public function handleAddEdit(Request $request, array $validatedData, ?int $id = null): array
    {

    }



    /**
     * Update category order
     *
     * @param  array  $categoryOrders  Array of category IDs with their new orders
     */
    public function updateOrder(array $categoryOrders): array
    {
        try {
            DB::beginTransaction();

            foreach ($categoryOrders as $categoryData) {
                if (isset($categoryData['id']) && isset($categoryData['order'])) {
                    Category::where('id', $categoryData['id'])
                        ->update(['order' => $categoryData['order']]);
                }
            }

            DB::commit();

            Log::info('Category orders updated successfully', [
                'orders' => $categoryOrders,
            ]);

            return [
                'status' => true,
                'message' => t('Category orders updated successfully.'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating category orders', [
                'error' => $e->getMessage(),
                'orders' => $categoryOrders,
            ]);

            throw new CustomBusinessException(
                t('Failed to update category orders. Please try again.'),
                422
            );
        }
    }
}
