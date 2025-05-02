<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     *
     * @param  string $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLocale(string $locale)
    {
        if (in_array($locale, config("app.available_locales", ["en", "ar"]))) { // Ensure locale is supported
            Session::put("locale", $locale);
            App::setLocale($locale);
        } else {
            // Optionally flash an error message if locale is invalid
            // flash(__("Invalid language selected."))->error();
        }
        
        return redirect()->back();
    }
}

