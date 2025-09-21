# Refactored Filter System Documentation

## Overview

The refactored filter system is designed to provide a robust, maintainable, and consistent way to filter database queries across different models. It follows object-oriented principles with a base abstract class that contains common functionality and specific implementations for each model.

## Architecture

The filter system consists of:

1. **BaseFilterService** - An abstract base class that contains the common filtering logic
2. **Model-specific Filter Services** - Concrete implementations for each model (Student, Teacher, Classroom, Notify)
3. **FilterServiceFactory** - A factory class to instantiate the appropriate filter service

## Using the Filter System

### Basic Usage

```php
// In a controller
use App\Services\API\Filters\FilterServiceFactory;

public function index(Request $request)
{
    $query = Student::query();
    
    // Get the appropriate filter service
    $filterService = FilterServiceFactory::make('student');
    
    // Apply filters based on request parameters
    $filterService->applyFilters($query, $request->all());
    
    // Get the filtered results
    $students = $query->paginate();
    
    return response()->json($students);
}
```

### Alternative Usage

You can also directly instantiate a specific filter service:

```php
use App\Services\API\Filters\StudentFilterService;

public function index(Request $request)
{
    $query = Student::query();
    
    // Instantiate the filter service directly
    $filterService = new StudentFilterService();
    
    // Apply filters based on request parameters
    $filterService->applyFilters($query, $request->all());
    
    // Get the filtered results
    $students = $query->paginate();
    
    return response()->json($students);
}
```

## Filter Types

The system supports various types of filters:

1. **Regular Fields** - Simple exact matching on fields
2. **Translatable Fields** - Fields that use JSON localization
3. **Foreign Key Fields** - Fields that reference other tables
4. **Date Fields** - Fields storing dates with support for Hijri date conversion
5. **Boolean Fields** - Fields with boolean values
6. **JSON Array Fields** - Fields storing JSON arrays
7. **Relation Filters** - Filtering by related model attributes

## Filter Syntax

### Basic Field Filtering

To filter by a model field, simply include it as a query parameter:

```
/api/students?name=Ahmed
/api/teachers?id_number=12345
/api/classrooms?class_number=101
```

### Multiple Values

For filtering by multiple values of the same field, use comma-separated values:

```
/api/students?id=1,2,3
/api/teachers?school_id=1,2
```

### Date Ranges

For date filters, use the format `start_date to end_date`:

```
/api/students?date_of_birth=01/01/2000 to 31/12/2010
/api/teachers?graduation_date=01/01/2015 to 31/12/2020
```

### Relation Filtering

To filter by related model attributes, use the `relation__field` syntax:

```
/api/students?classroom__name=Class A
/api/teachers?member__email=teacher@example.com
/api/classrooms?teachers__id_number=12345
```

### Nested Relation Filtering

For filtering through nested relations, use the `relation.subrelation__field` syntax:

```
/api/students?teacher.member__name=Ahmed
/api/classrooms?teachers.member__email=teacher@example.com
```

## Extending the System

### Creating a New Filter Service

To add support for a new model:

1. Create a new class that extends `BaseFilterService`
2. Define model-specific properties (fields, relations, etc.)
3. Implement any custom filtering methods if needed
4. Add the new model to the `FilterServiceFactory`

Example:

```php
<?php

namespace App\Services\API\Filters;

class NewModelFilterService extends BaseFilterService
{
    protected $tableName = 'new_models';
    
    protected $translatableFields = ['name', 'description'];
    
    protected $foreignKeyFields = ['category_id'];
    
    // Define other properties...
    
    // Override methods if needed...
}
```

Then add it to the factory:

```php
$filterMap = [
    // Existing mappings...
    'newmodel' => NewModelFilterService::class,
    'newmodels' => NewModelFilterService::class,
];
```

## Benefits of the Refactored System

1. **Reduced Code Duplication** - Common filtering logic is centralized
2. **Consistency** - All models use the same filtering approach
3. **Maintainability** - Changes to filtering logic can be made in one place
4. **Extensibility** - Easy to add support for new models or filter types
5. **Table Qualification** - Prevents SQL errors from ambiguous column references
6. **Type-specific Filtering** - Different field types are handled appropriately
