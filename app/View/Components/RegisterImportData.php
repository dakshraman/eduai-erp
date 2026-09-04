<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RegisterImportData extends Component
{
    public $url;

    public $sample;

    public $instructions;

    public $exams;

    public $classes;

    public function __construct($url, $sample, $instructions, $exams, $classes)
    {
        $this->url = $url;
        $this->sample = $sample;
        $this->instructions = $instructions;
        $this->exams = $exams;
        $this->classes = $classes;
    }

    public function render()
    {
        return view('components.register-import-data');
    }
}
