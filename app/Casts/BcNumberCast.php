<?php

namespace App\Casts;

use BcMath\Number;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Throwable;

/**
 * A custom Eloquent cast for BCMath\Number.
 *
 * Copied from https://github.com/takeshiyu/laravel-bcmath-cast.
 */
class BcNumberCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Number
    {
        try {
            return $value === null ? null : new Number((string) $value);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException(
                "Failed to cast {$key} to BCMath\\Number: {$exception->getMessage()}",
                previous: $exception
            );
        }
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        // Already a Number object - extract its string value
        if ($value instanceof Number) {
            return $value->value;
        }

        // Integer, float, or numeric string - convert to string
        if (is_string($value) || is_float($value) || is_int($value)) {
            $str = (string) $value;

            if (! is_numeric($str)) {
                throw new InvalidArgumentException(
                    "The {$key} attribute must be a numeric string. Non-numeric string given."
                );
            }

            return $str;
        }

        // Unsupported type
        throw new InvalidArgumentException(
            sprintf(
                'The %s attribute must be a BCMath\\Number, numeric value, or numeric string. %s given.',
                $key,
                get_debug_type($value)
            )
        );
    }
}
