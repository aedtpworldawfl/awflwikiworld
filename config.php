<?php
/**
 * AWFLWIKIWORLD — Site Configuration
 * Edit the values below to configure your wiki.
 */

// ---- Site identity ----
$Sitename        = "Enter Your Site Name";
$MetaTitle       = "Enter Your Meta Title";
$MetaDescription = "Enter Your Meta Description";

// ---- Server / paths ----
// The base URL of your wiki, no trailing slash (e.g. "https://example.com/wiki"
// or "http://localhost:8080"). This is NOT auto-detected — auto-detection was
// removed because it breaks in real hosting setups (reverse proxies, CLI/cron
// contexts, subdirectory scripts like dashboard/ or sitemap/ computing a
// different "root" than index.php, etc). Set this explicitly and every URL
// and asset link in the system is built from it.
$Server = "Enter Your Site's Base Url";

// Logo path. Can be absolute URL, relative path, or full path.
$Logo = "logo/aedtpworld.svg";

// Active skin CSS. Can be absolute URL, relative, or full path.
$Skin = "skins/vector2022.css";

// ---- Admin account ----
// Exact admin username / password (plaintext here; the system hashes it on
// first use — see includes/functions.php::awfl_verify_admin()).
$Username = "Enter Your Username";
$Password = "Enter Your Password";

// ---- Uploads ----
$ImageUpload      = false;  // allow logged-in users to upload images
$ExternalImageUrl = true;  // allow external image URLs (e.g. in infoboxes)

// ---- URL routing style ----
// "0" => index.php?title=Pagename            (and ?title=Space/Pagename)
// "1" => /Pagename                           (and /Space/Pagename)   [pretty URLs, needs .htaccess]
// "2" => /namespaces/Pagename                (and /namespaces/Space/Pagename)
$route = "Enter Your Route Value";

// ---- Session ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Root path constant (filesystem, not URL) ----
define('AWFL_ROOT', __DIR__);

$Server = rtrim($Server, '/');
if ($Server === '') {
    http_response_code(500);
    die('AWFLWIKIWORLD is not configured yet: set $Server in config.php to your site\'s base URL (e.g. "http://localhost:8099" or "https://example.com/wiki").');
}

// ---- Admin dashboard runtime overrides (active skin + color palette) ----
// Stored separately from this file so the dashboard never has to rewrite
// PHP source — it just writes JSON here.
$AwflColors = [];
$__awfl_overrides_file = __DIR__ . '/dashboard/settings.json';
if (file_exists($__awfl_overrides_file)) {
    $__awfl_overrides = json_decode(file_get_contents($__awfl_overrides_file), true);
    if (is_array($__awfl_overrides)) {
        if (!empty($__awfl_overrides['skin'])) {
            $Skin = 'skins/' . basename($__awfl_overrides['skin']) . '.css';
        }
        if (!empty($__awfl_overrides['colors']) && is_array($__awfl_overrides['colors'])) {
            $AwflColors = $__awfl_overrides['colors'];
        }
    }
}
unset($__awfl_overrides_file, $__awfl_overrides);



















