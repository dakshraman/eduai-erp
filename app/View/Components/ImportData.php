<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ImportData extends Component
{
    public $url;

    public $sample;

    public $instructions;

    public function __construct($url, $sample, $instructions)
    {
        $this->url = $url;
        $this->sample = $sample;
        $this->instructions = $instructions;
    }

    public function render()
    {
        return view('components.import-data');
    }
}
