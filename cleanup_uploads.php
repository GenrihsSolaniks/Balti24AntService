<?php
// cleanup_uploads.php
$dir = __DIR__ . '/uploads';
$maxAge = 30 * 24 * 60 * 60; // 30 дней в секундах (ЕС стандарт)
$now = time();

$deleted = [];

foreach (glob($dir . '/*') as $file) {
    if (is_file($file) && ($now - filemtime($file)) > $maxAge) {
        if (unlink($file)) {
            $deleted[] = basename($file);
        }
    }
}

// Для логирования (опционально)
file_put_contents(__DIR__ . '/cleanup_log.txt', date('Y-m-d H:i:s') . " — удалено " . count($deleted) . " файлов: " . implode(', ', $deleted) . "\n", FILE_APPEND);

// Закомментируй, если пока нельзя использовать:
# echo "Удалено " . count($deleted) . " старых файлов.\n";
