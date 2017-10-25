<?php

namespace Locale\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class Localizable
 *
 * @since 1.0.0
 * @package Locale\Http\Resources
 *
 * @property string locale_id
 */
abstract class Localizable extends Resource
{
    /**
     * Customize the response for a request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\JsonResponse  $response
     */
    public function withResponse($request, $response)
    {
        if ($this->locale_id) {
            $response->header("Content-Language", $this->locale_id);
        }
    }
}
