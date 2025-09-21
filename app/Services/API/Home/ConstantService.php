<?php

namespace App\Services\API\Home;

use App\Exceptions\CustomBusinessException;
use App\Http\Resources\API\ConstantResource;
use App\Models\Constant;
use App\Services\Constants\ConstantHandler;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ConstantService
{
    private array $selectedColumns = [
        'id',
        'name',
        'icon',
        'constant_name',
        'module',
        'field',
    ];

    public function getConstants(array $params): AnonymousResourceCollection
    {
        try {
            Log::info('Processing constants with parameters:', $params);

            // Start with base query
            $query = Constant::select($this->selectedColumns);
            // If module and field are provided, use ConstantHandler
            if (isset($params['module']) && isset($params['field']) && false) {
                $handler = new ConstantHandler($params['module'], $params['field']);
                $results = $handler->getFromDatabase($this->selectedColumns);
            } else {
                // If module is provided, filter by it
                if (isset($params['module'])) {
                    $query->where('module', $params['module']);
                }

                // If field is provided, filter by it
                if (isset($params['field'])) {
                    $query->where('field', $params['field']);
                }

                $results = $query->get();
            }

            // Apply additional filters
            if (isset($params['constant_id'])) {
                $results = $results->where('id', $params['constant_id']);
                if ($results->isEmpty()) {
                    throw new CustomBusinessException(
                        'Constant not found with the provided ID',
                        404,
                        ['constant_id' => $params['constant_id']]
                    );
                }
            }

            if (isset($params['parent_id'])) {
                $results = $results->where('parent_id', $params['parent_id']);
                if ($results->isEmpty()) {
                    throw new CustomBusinessException(
                        'No constants found with the provided parent ID',
                        404,
                        ['parent_id' => $params['parent_id']]
                    );
                }
            }

            if (isset($params['constant_name'])) {
                $results = $results->where('constant_name', $params['constant_name']);
                if ($results->isEmpty()) {
                    throw new CustomBusinessException(
                        'Constant not found with the provided name',
                        404,
                        ['constant_name' => $params['constant_name']]
                    );
                }
            }

            // Return empty collection if no results found instead of throwing error
            if ($results->isEmpty()) {
                return ConstantResource::collection(collect([]));
            }

            return ConstantResource::collection($results);
        } catch (CustomBusinessException $e) {
            Log::warning('Business validation failed:', [
                'message' => $e->getMessage(),
                'data' => $e->getData(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in getConstants:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new CustomBusinessException(
                'An error occurred while processing constants',
                500,
                ['original_error' => $e->getMessage()]
            );
        }
    }
}
