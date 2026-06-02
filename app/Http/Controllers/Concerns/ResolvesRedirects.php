<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait ResolvesRedirects
{
    protected function redirectTarget(Request $request, string $fallback): string
    {
        $target = $request->input('redirect') ?: $request->query('redirect');

        if (! is_string($target) || trim($target) === '') {
            return $fallback;
        }

        $target = trim($target);

        if (str_starts_with($target, url('/'))) {
            return $target;
        }

        if (str_starts_with($target, '/') && ! str_starts_with($target, '//')) {
            return url($target);
        }

        return $fallback;
    }
}
