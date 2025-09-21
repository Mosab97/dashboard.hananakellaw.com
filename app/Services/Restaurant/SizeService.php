<?php

namespace App\Services\Restaurant;

use App\Exceptions\CustomBusinessException;
use App\Models\Size;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SizeService
{
    /**
     * Handle the creation or update of a size
     *
     * @param  array  $validatedData  The validated data from the request
     * @param  int|null  $id  Existing model ID for updates
     * @return array Result containing the model, message, and status
     *
     * @throws CustomBusinessException
     */
    public function handleAddEdit(array $validatedData, ?int $id = null): array
    {
        try {
            DB::beginTransaction();

            $isUpdate = ! empty($id);

            if ($isUpdate) {
                $model = Size::findOrFail($id);
                $model->update($validatedData);
                $message = t('Size updated successfully.');
            } else {
                $model = Size::create($validatedData);
                $message = t('Size created successfully.');
            }

            // Perform any additional business logic here
            $this->performAdditionalActions($model, $validatedData, $isUpdate);

            DB::commit();

            Log::info('Size '.($isUpdate ? 'updated' : 'created').' successfully', [
                'id' => $model->id,
                'data' => $validatedData,
            ]);

            return [
                'model' => $model->load(['product']),
                'message' => $message,
                'is_update' => $isUpdate,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in Size service', [
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
