<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppAssets extends Component
{
    public string $css;
    public string $js;

    public function __construct()
    {
        $manifest = json_decode(
            file_get_contents(public_path('build/manifest.json')),
            true
        );

        $this->css = '/build/' . $manifest['resources/css/app.css']['file'];
        $this->js  = '/build/' . $manifest['resources/js/app.js']['file'];
    }

    public function render()
    {
        return view('components.app-assets');
    }
}
