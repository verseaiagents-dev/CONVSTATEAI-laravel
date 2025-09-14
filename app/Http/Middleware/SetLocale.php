<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Closure): (\Illuminate\Http\Response|\Illuminate\Http\Response)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated and has language preference
        if (Auth::check() && Auth::user()->language) {
            $locale = Auth::user()->language;
        }
        // Check if language is set in session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Check browser language
        else {
            $browserLanguage = substr($request->header('Accept-Language', 'tr'), 0, 2);
            
            // Map browser languages to supported languages
            $languageMap = [
                'tr' => 'tr',
                'en' => 'en',
                'de' => 'tr', // German -> Turkish
                'fr' => 'tr', // French -> Turkish
                'es' => 'tr', // Spanish -> Turkish
                'it' => 'tr', // Italian -> Turkish
                'pt' => 'tr', // Portuguese -> Turkish
                'ru' => 'tr', // Russian -> Turkish
                'ar' => 'tr', // Arabic -> Turkish
                'zh' => 'tr', // Chinese -> Turkish
                'ja' => 'tr', // Japanese -> Turkish
                'ko' => 'tr', // Korean -> Turkish
            ];
            
            $locale = $languageMap[$browserLanguage] ?? 'tr';
            
            // Store in session for future requests
            Session::put('locale', $locale);
        }
        
        // Validate locale
        $allowedLocales = ['tr', 'en'];
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'tr';
        }
        
        // Set application locale
        App::setLocale($locale);
        
        return $next($request);
    }
}
