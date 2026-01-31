<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('lang') ?? $request->header('Accept-Language') ?? 'ar';

        // دعم لغتين فقط - يمكنك توسعة اللستة
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
