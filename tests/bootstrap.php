<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';

use Illuminate\Support\Arr;
use KodiComponents\Navigation\Badge;
use KodiComponents\Navigation\Contracts\BadgeInterface;
use KodiComponents\Navigation\Contracts\PageInterface;
use KodiComponents\Navigation\Page;

if (! function_exists('app')) {
    function app(?string $abstract = null)
    {
        $bindings = [
            PageInterface::class => Page::class,
            BadgeInterface::class => Badge::class,
        ];

        if ($abstract === null) {
            return null;
        }

        $class = $bindings[$abstract] ?? $abstract;

        return new $class();
    }
}

if (! function_exists('url')) {
    function url(?string $path = null)
    {
        $base = 'http://localhost';

        if ($path !== null) {
            $path = ltrim($path, '/');
            return $base . ($path === '' ? '' : '/' . $path);
        }

        return new class($base) {
            private string $base;

            public function __construct(string $base)
            {
                $this->base = $base;
            }

            public function current(): string
            {
                return $this->base;
            }
        };
    }
}

if (! function_exists('config')) {
    function config(?string $key = null, $default = null)
    {
        static $config = null;

        if ($config === null) {
            $config = ['navigation' => require __DIR__ . '/../config/navigation.php'];
        }

        if ($key === null) {
            return $config;
        }

        return Arr::get($config, $key, $default);
    }
}

if (! function_exists('view')) {
    function view(string $view, array $data = [])
    {
        return new class {
            public function render(): string
            {
                return '';
            }

            public function __toString(): string
            {
                return $this->render();
            }
        };
    }
}
