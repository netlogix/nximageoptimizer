<?php

declare(strict_types=1);

namespace Netlogix\Nximageoptimizer\Tests\Unit\Fixtures;

use Psr\Container\ContainerInterface;

class ContainerFixture implements ContainerInterface
{
    private array $instances = [];

    public function get(string $id)
    {
        return $this->instances[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    public function set(string $id, $instance): void
    {
        $this->instances[$id] = $instance;
    }
}
