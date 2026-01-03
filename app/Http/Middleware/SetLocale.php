<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('locale')) {
            $locale = $request->get('locale');

            if (in_array($locale, ['en', 'id'])) {
                session(['locale' => $locale]);
            }
        }

        $locale = session('locale', config('app.locale', 'id'));
        app()->setLocale($locale);

        return $next($request);
    }
}
