<?php

namespace App\Support\SpondonIt;

use SpondonIt\Service\Contracts\AppInitializerInterface;
use SpondonIt\Service\Services\InitService;

class InitServiceAdapter implements AppInitializerInterface
{
    private InitService $inner;

    public function __construct()
    {
        $this->inner = new InitService();
    }

    public function init(): void
    {
        // no-op: app is already initialized
    }

    public function config(): void
    {
        // no-op: config already loaded
    }

    public function dailyCheck(): mixed
    {
        return $this->inner->dailyCheck();
    }

    public function dailyApiCheck(): mixed
    {
        return $this->inner->dailyApiCheck();
    }
}
