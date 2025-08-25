<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\JsonEncodingException;

class UnescapedJson implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return is_null($value) ? [] : json_decode($value, true);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw JsonEncodingException::forAttribute($model, $key, json_last_error_msg());
        }

        return $json;
    }
}
