<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Change the application language
     */
    public function changeLanguage(Request $request)
    {
        $language = $request->input('language');
        
        // Validate language
        $allowedLanguages = ['tr', 'en'];
        if (!in_array($language, $allowedLanguages)) {
            $language = 'en'; // Default to English if invalid
        }
        
        // Store language in session
        Session::put('locale', $language);
        
        // Set application locale
        App::setLocale($language);
        
        // If user is authenticated, save language preference
        if (auth()->check()) {
            auth()->user()->update(['language' => $language]);
        }
        
        return Redirect::back()->with('success', __('app.language_changed'));
    }
    
    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'current_language' => App::getLocale(),
            'available_languages' => [
                'tr' => __('app.turkish'),
                'en' => __('app.english')
            ]
        ]);
    }
    
    /**
     * Set language based on browser preference
     */
    public function setBrowserLanguage()
    {
        $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
        
        // Map browser languages to supported languages
        $languageMap = [
            'tr' => 'tr',
            'en' => 'en',
            'de' => 'en', // German -> English
            'fr' => 'en', // French -> English
            'es' => 'en', // Spanish -> English
            'it' => 'en', // Italian -> English
            'pt' => 'en', // Portuguese -> English
            'ru' => 'en', // Russian -> English
            'ar' => 'en', // Arabic -> English
            'zh' => 'en', // Chinese -> English
            'ja' => 'en', // Japanese -> English
            'ko' => 'en', // Korean -> English
        ];
        
        $language = $languageMap[$browserLanguage] ?? 'en';
        
        Session::put('locale', $language);
        App::setLocale($language);
        
        return $language;
    }
}
