<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "PHP: " . PHP_VERSION . "\n";
echo "allow_url_fopen = " . (ini_get('allow_url_fopen') ? "On" : "Off") . "\n";
echo "open_basedir    = " . (ini_get('open_basedir') ?: "(none)") . "\n";

// 1) Tes tulis file
$wdir = __DIR__ . "/tmp";
if (!is_dir($wdir)) { @mkdir($wdir, 0700, true); }
$testfile = $wdir . "/write.test";
$wok = @file_put_contents($testfile, "ok\n");
echo "Write to $testfile: " . ($wok !== false ? "OK" : "FAIL") . "\n";

// 2) Tes download via file_get_contents
$u = "https://example.com/";
$dat = @file_get_contents($u);
echo "GET $u via file_get_contents: " . ($dat !== false ? "OK len=".strlen($dat) : "FAIL") . "\n";

// 3) Tes cURL extension (kalau ada)
$hasCurl = function_exists('curl_init');
echo "PHP cURL extension: " . ($hasCurl ? "Present" : "Missing") . "\n";
if ($hasCurl) {
  $ch = curl_init("https://example.com/");
  curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>15]);
  $resp = curl_exec($ch); $err = curl_error($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  echo "GET https://example.com/ via cURL: " . ($resp !== false ? "OK code=$code len=".strlen($resp) : "FAIL ($err)") . "\n";
}

// 4) Tes eksekusi shell sederhana
$disabled = array_map('trim', explode(',', (string)ini_get('disable_functions')));
echo "disable_functions: " . (ini_get('disable_functions') ?: "(none)") . "\n";
$can_shell = function_exists('shell_exec') && !in_array('shell_exec', $disabled);
echo "shell_exec available: " . ($can_shell ? "YES" : "NO") . "\n";
if ($can_shell) {
  echo "whoami: " . trim(shell_exec('whoami 2>&1')) . "\n";
  echo "which sh: " . trim(shell_exec('which sh 2>&1')) . "\n";
  echo "which wget: " . trim(shell_exec('which wget 2>&1')) . "\n";
  echo "sh -c 'echo sh_ok': " . trim(shell_exec("sh -c 'echo sh_ok' 2>&1")) . "\n";
}
echo "</pre>";
