<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface IThread
{
    public function orderByRaw(string $statement);

    public function whereLike(string $solumn, string $value);

    public function searchLocation(Request $request);

    public function closest($lat, $lng);

    public function findBySlug(String $slug);
}
