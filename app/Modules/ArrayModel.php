<?php

namespace App\Modules;

use Illuminate\Database\Eloquent\Model;
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
     * @return \Illuminate\Support\Collection|null The found record or null if not found.
     */
    public static function find($id, $columns = ['*'])
    {
        return static::all()->firstWhere('id', $id);
    }
}
