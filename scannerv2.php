<?php
// shell_scanner_strict.php - Improved scanner to detect stealthy loaders and backdoors

error_reporting(0);
set_time_limit(0);

$base = isset($_GET['path']) ? realpath($_GET['path']) : getcwd();

$patterns = [
    '/eval\s*\(/i',
    '/base64_decode\s*\(/i',
    '/gzinflate\s*\(/i',
    '/assert\s*\(/i',
    '/preg_replace\s*\(.*\/e/i',
    '/(system|shell_exec|passthru|exec)\s*\(/i',
    '/php:\/\/input/i',
    '/curl_(init|exec|setopt)/i',
    '/file_get_contents\s*\(/i',
    '/fopen\s*\(/i',
    '/stream_get_contents/i',
    '/include\s*\(\s*\$[a-z0-9_]+\s*\)/i',
    '/require\s*\(\s*\$[a-z0-9_]+\s*\)/i',
    '/["\']\s*\.\s*["\']/i',
    '/md5\s*\(\s*\$_SERVER\s*\[\s*[\'"]HTTP_HOST[\'"]\s*\]/i',
    '/\/dev\/shm\//i',
    '/raw\.githubusercontent\.com/i',
];

function is_shell($code, $patterns) {
    $score = 0;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $code)) {
            $score++;
        }
    }
    return $score >= 3;
}

function snippet_preview($code, $match) {
    $pos = stripos($code, $match);
    return $pos === false ? '' : substr($code, max(0, $pos - 40), 400);
}

function scan_folder($dir, $patterns, &$results, $base) {
    $items = @scandir($dir);
    if (!$items) return;

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            scan_folder($path, $patterns, $results, $base);
        } elseif (preg_match('/\.(php|phtml)$/i', $item) && filesize($path) < 1000000) {
            $code = @file_get_contents($path);
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $code, $match) && is_shell($code, $patterns)) {
                    $results[] = [
                        'file'    => $path,
                        'match'   => $match[0],
                        'snippet' => snippet_preview($code, $match[0]),
                    ];
                    break;
                }
            }
        }
    }
}

$results = [];
scan_folder($base, $patterns, $results, $base);
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8">
    
    <title>Shell Scanner</title>
    <style>
        body { background:#111; color:#eee; font-family: monospace; padding:20px; }
        h1 { color:#0f0; }
        .result { background:#1e1e1e; border:1px solid #333; padding:15px; margin-bottom:15px; border-radius:8px; }
        .path { color:#4af; font-weight:bold; }
        .bad { color:#ff5252; }
        pre { background:#000; color:#0f0; padding:10px; border-radius:6px; overflow:auto; max-height: 200px; }
        code { color:#0af; }
    </style>
</head>
<body>
    <h1>🛡️ Strict PHP Shell Scanner (.php & .phtml)</h1>
    <p>📂 Scanning directory: <code><?= htmlspecialchars($base) ?></code></p>
    <hr>

    <?php if (empty($results)): ?>
        <p style="color:lime;">✅ No suspicious backdoor or shell found.</p>
    <?php else: ?>
        <?php foreach ($results as $r): ?>
            <div class="result">
                <div class="path">
                    📄 <a href="<?= htmlspecialchars(str_replace($_SERVER['DOCUMENT_ROOT'], '', $r['file'])) ?>" target="_blank"><?= htmlspecialchars($r['file']) ?></a>
                </div>
                <div>🚨 Matched pattern: <span class="bad"><?= htmlspecialchars($r['match']) ?></span></div>
                <pre><?= htmlspecialchars($r['snippet']) ?></pre>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
Close
