<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SubjectImportData extends Component
{
    public $url;

    public $sample;

    public $instructions;

    public $classes;

    public function __construct($url, $sample, $instructions, $classes)
    {
        $this->url = $url;
        $this->sample = $sample;
        $this->instructions = $instructions;
        $this->classes = $classes;
    }

    public function render()
    {
        return view('components.subject-import-data');
    }
}
