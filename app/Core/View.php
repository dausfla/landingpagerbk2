<?php

namespace App\Core;

class View
{
    private static string $viewsPath = __DIR__ . '/../Views/';

    /**
     * Render a PHP view file with extracted variables
     */
    public static function render(string $template, array $data = []): string
    {
        $templatePath = self::$viewsPath . ltrim($template, '/') . '.php';

        if (!file_exists($templatePath)) {
            if (env('APP_DEBUG', false)) {
                throw new \RuntimeException("View file not found: {$templatePath}");
            }
            return "View not found.";
        }

        extract($data);
        ob_start();
        include $templatePath;
        return ob_get_clean();
    }

    /**
     * Render view wrapped in layout template
     */
    public static function renderWithLayout(string $template, string $layout, array $data = []): string
    {
        $content = self::render($template, $data);
        $data['content'] = $content;
        return self::render($layout, $data);
    }
}
