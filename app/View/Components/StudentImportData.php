<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StudentImportData extends Component
{
    public $url;

    public $sample;

    public $instructions;

    public $sessions;

    public $classes;

    public function __construct($url, $sample, $instructions, $sessions, $classes)
    {
        $this->url = $url;
        $this->sample = $sample;
        $this->instructions = $instructions;
        $this->sessions = $sessions;
        $this->classes = $classes;
    }

    public function render()
    {
        return view('components.student-import-data');
    }
}
