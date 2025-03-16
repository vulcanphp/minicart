<?php

namespace App\Modules;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Base class for simple Array-based models.
 *
 * Use this class as a starting point for creating your own array-based models.
 * You can use the methods provided to filter and manipulate the data.
 *
 * @author Shahin Moyshan <shahin.moyshan2@gmail.com>
 * @version 1.0.0
 */
abstract class ArrayModel extends Model
{
    /**
     * The data associated with the model.
     *
     * @var array
     */
    protected static array $data = [];

    /**
     * Get all records from the data array.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all($columns = ['*']): Collection
    {
        return (new Collection(static::$data))
            ->map(fn($item) => new static($item));
    }

    /**
     * Find a record by its ID in the data array.
     *
     * @param mixed $id The ID of the record to find.
     * @param array $columns The columns to be selected.
     * @return static|null The found record or null if not found.
     */
    public static function find($id, $columns = ['*'])
    {
        return static::all()->firstWhere('id', $id);
    }

    /**
     * Filter the array data based on the given conditions and operator.
     * Supported operators: =, >, <, like
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return \Illuminate\Support\Collection
     */
    public static function where(string $column, string $operator, $value = null, string $boolean = 'and'): Collection
    {
        $collection = static::all();

        $value ??= ''; // Set the default value to an empty string

        if ($operator === '=') {
            return $collection->filter(
                fn($item) => isset($item->{$column}) && $item->{$column} == $value
            );
        }

        if ($operator === '>') {
            return $collection->filter(
                fn($item) => isset($item->{$column}) && $item->{$column} > $value
            );
        }

        if ($operator === '<') {
            return $collection->filter(
                fn($item) => isset($item->{$column}) && $item->{$column} < $value
            );
        }

        if ($operator === 'like') {
            $value = trim($value, '%'); // Remove the % from the value

            return $collection->filter(
                fn($item) => isset($item->{$column}) && stripos($item->{$column}, $value) !== false
            );
        }

        // If the operator is not supported, return the original collection
        return $collection;
    }

    /**
     * Paginate the given data set and return a LengthAwarePaginator instance.
     *
     * @param int|null $perPage Number of items per page.
     * @param array $columns Columns to select (not used in array models).
     * @param string $pageName The name of the query parameter for page number.
     * @param int|null $page Current page number.
     * @param int|null $total Total number of items (optional).
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $total = null)
    {
        // Resolve the current page if not provided
        $page ??= Paginator::resolveCurrentPage($pageName);

        // Retrieve all records as a collection
        $collection = static::all($columns);

        // Calculate the total number of items if not provided
        $total = $collection->count();

        // Slice the collection to get items for the current page
        $items = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $options = [
            'path' => Paginator::resolveCurrentPath(), // Set the base path for pagination links
            'pageName' => $pageName, // Set the parameter name for the page number
        ];

        // Create and return a LengthAwarePaginator instance
        return Container::getInstance()
            ->makeWith(
                LengthAwarePaginator::class,
                compact('items', 'total', 'perPage', 'page', 'options')
            );
    }
}
