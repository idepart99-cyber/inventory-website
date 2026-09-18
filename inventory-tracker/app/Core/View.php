<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data);
        $config = require __DIR__ . '/../../config/app.php';
        $flashes = Session::getFlashes();
        $currentUser = Auth::user();

        // Buffer the view content
        ob_start();
        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = __DIR__ . '/../Views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    public static function e(mixed $string): string
    {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }

    public static function currency(float|int|string $amount): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $sym = $config['currency'] ?? '$';
        return $sym . number_format((float)$amount, 2);
    }

    public static function date(?string $dateStr, string $format = 'M d, Y H:i'): string
    {
        if (empty($dateStr)) {
            return '—';
        }
        $ts = strtotime($dateStr);
        return $ts ? date($format, $ts) : $dateStr;
    }

    public static function badge(string $type, string $text): string
    {
        $classes = match(strtolower($type)) {
            'success', 'active', 'approved', 'fulfilled', 'in' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'warning', 'pending', 'transfer', 'low' => 'bg-amber-100 text-amber-800 border-amber-200',
            'danger', 'inactive', 'rejected', 'out', 'critical', 'discontinued' => 'bg-rose-100 text-rose-800 border-rose-200',
            'info', 'adjustment', 'medium' => 'bg-sky-100 text-sky-800 border-sky-200',
            'purple', 'warehouse', 'high', 'urgent' => 'bg-purple-100 text-purple-800 border-purple-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200'
        };
        return sprintf('<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border %s">%s</span>', $classes, self::e($text));
    }
}
