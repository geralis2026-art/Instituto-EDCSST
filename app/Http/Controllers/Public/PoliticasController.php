<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

/** Páginas legales públicas: política de tratamiento de datos, términos y condiciones, y cookies. */
class PoliticasController extends Controller
{
    public function privacidad()
    {
        return view('public.politicas.privacidad');
    }

    public function terminos()
    {
        return view('public.politicas.terminos');
    }

    public function cookies()
    {
        return view('public.politicas.cookies');
    }
}
