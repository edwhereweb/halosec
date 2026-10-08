<?php

declare(strict_types=1);

namespace HaloSec;

final class View
{
    /** @var array<string, mixed> */
    private array $shared = [];

    public function __construct(private string $viewPath)
    {
    }

    /**
     * @param array<string, mixed> $shared
     */
    public function share(array $shared): void
    {
        $this->shared = array_merge($this->shared, $shared);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = [], string $layout = 'layout'): string
    {
        $content = $this->include($template, $data);

        return $this->include($layout, array_merge($data, ['content' => $content]));
    }

    /**
     * @param array<string, mixed> $data
     */
    public function include(string $template, array $data = []): string
    {
        $file = $this->viewPath . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        $render = function (string $__file, array $__data): string {
            extract($__data, EXTR_SKIP);
            ob_start();
            try {
                include $__file;
            } catch (\Throwable $e) {
                ob_end_clean();
                throw $e;
            }

            return (string) ob_get_clean();
        };

        return $render($file, array_merge($this->shared, $data));
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }
}
