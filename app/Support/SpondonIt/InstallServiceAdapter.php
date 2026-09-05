<?php

namespace App\Support\SpondonIt;

use Illuminate\Container\Container;
use SpondonIt\Service\Contracts\AppInstallerInterface;

class InstallServiceAdapter implements AppInstallerInterface
{
    private mixed $inner;

    public function __construct()
    {
        $this->inner = Container::getInstance()->make(\SpondonIt\Service\Services\InstallService::class);
    }

    public function install(array $params): void
    {
        $this->inner->install($params);
    }

    public function makeAdmin(array $params): mixed
    {
        return method_exists($this->inner, 'makeAdmin')
            ? $this->inner->makeAdmin($params)
            : null;
    }

    public function __call(string $method, array $args): mixed
    {
        return $this->inner->{$method}(...$args);
    }
}
