<?php

namespace App\Parser;

use Illuminate\Support\LazyCollection;

class ParseJson implements ParserInterface
{
    /**
     * Using cerbero/lazy-json package (https://github.com/cerbero90/lazy-json) for adding macro (extending ability) to Laravel's LazyCollection for parsing from a JSON.
     *
     * @param string $data
     * @return mixed
     */
    public function parse($data)
    {
        $lazyCollectedData = LazyCollection::fromJson($data);

        try {
            $lazyCollectedData->first();
        } catch (\Exception $e) {
            return false;
        }

        return $lazyCollectedData;
    }
}
