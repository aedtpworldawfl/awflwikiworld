<?php
require_once __DIR__ . '/includes/functions.php';

$action = $_GET['action'] ?? 'view';

/* ---------------------------------------------------------
   Global actions (not tied to a specific wiki page)
   --------------------------------------------------------- */
if ($action === 'logout') {
    awfl_logout();
    awfl_redirect('index.php');
    exit;
}

if ($action === 'preview' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/html; charset=utf-8');
    echo awfl_parse_wiki($_POST['wikitext'] ?? '');
    exit;
}

$title = awfl_current_title();
if ($title === '') $title = 'Main Page';

// --- AWFLWIKIWORLD NAMESPACE GUARD ---
// Nobody — viewer, logged-in user, or admin — may bring a namespace folder
// into existence just by visiting, searching, or linking to it. Namespaces
// are created/renamed/deleted exclusively by an admin via
// dashboard/namespaces.php. If the namespace portion of the requested title
// doesn't already exist as a real folder, send everyone home; if it does
// exist, fall through to the normal view/edit flow below (which already
// requires login to create/edit a page).
$__awflNs = awfl_title_namespace($title);
if ($__awflNs !== null && !awfl_namespace_exists($__awflNs)) {
    header('Location: ' . rtrim($Server, '/') . '/');
    exit;
}
unset($__awflNs);




/* ---------------------------------------------------------
   Special pages
   --------------------------------------------------------- */
if (str_starts_with($title, 'Special:')) {
    $special = substr($title, 8);

    switch ($special) {

        case 'Login': {
            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                [$ok, $msg] = awfl_login($_POST['username'] ?? '', $_POST['password'] ?? '');
                if ($ok) { awfl_redirect('Main_Page'); exit; }
                $error = $msg;
            }
            $pagename = 'Log in'; $headerMode = 'interface';
            require __DIR__ . '/includes/header.php'; ?>
            <main class="awfl-main awfl-narrow">
                <h1>Log in</h1>
                <?php if ($error): ?><p class="awfl-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <form method="post" class="awfl-form">
                    <label>Username<br><input type="text" name="username" required autofocus></label>
                    <label>Password<br><input type="password" name="password" required></label>
                    <button type="submit">Log in</button>
                </form>
                <p><a href="<?= htmlspecialchars(awfl_page_url_raw('Special:Register')) ?>">Create an account</a> · <a href="<?= htmlspecialchars(awfl_asset_url('users/recover.php')) ?>">Forgotten password?</a></p>
            </main>
            <?php require __DIR__ . '/includes/footer.php';
            exit;
        }

        case 'Register': {
            $error = ''; $ok = false;
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (($_POST['password'] ?? '') !== ($_POST['confirm_password'] ?? '')) {
                    $error = 'Passwords do not match.';
                } else {
                    [$ok, $msg] = awfl_register_user(
                        $_POST['username'] ?? '', $_POST['password'] ?? '', $_POST['email'] ?? '',
                        $_POST['name'] ?? '', $_POST['security_question'] ?? '', $_POST['security_answer'] ?? ''
                    );
                    if (!$ok) $error = $msg; else $error = $msg;
                }
            }
            $pagename = 'Register'; $headerMode = 'interface';
            require __DIR__ . '/includes/header.php'; ?>
            <main class="awfl-main awfl-narrow">
                <h1>Create an account</h1>
                <?php if ($error): ?><p class="<?= $ok ? 'awfl-success' : 'awfl-error' ?>"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <?php if (!$ok): ?>
                <form method="post" class="awfl-form">
                    <label>Username<br><input type="text" name="username" required></label>
                    <label>Display name<br><input type="text" name="name"></label>
                    <label>Email<br><input type="email" name="email"></label>
                    <label>Password<br><input type="password" name="password" required></label>
                    <label>Confirm password<br><input type="password" name="confirm_password" required></label>
                    <label>Security question<br><input type="text" name="security_question" placeholder="e.g. What was your first pet's name?" required></label>
                    <label>Security answer<br><input type="text" name="security_answer" required></label>
                    <button type="submit">Register</button>
                </form>
                <?php else: ?>
                    <p><a href="<?= htmlspecialchars(awfl_page_url_raw('Special:Login')) ?>">Go to log in</a></p>
                <?php endif; ?>
            </main>
            <?php require __DIR__ . '/includes/footer.php';
            exit;
        }

        case 'Upload': {
            awfl_require_login();
            $error = ''; $success = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
                [$ok, $msg] = awfl_handle_image_upload($_FILES['image']);
                if ($ok) $success = "Uploaded as images/$msg"; else $error = $msg;
            }
            $pagename = 'Upload image'; $headerMode = 'interface';
            require __DIR__ . '/includes/header.php'; ?>
            <main class="awfl-main awfl-narrow">
                <h1>Upload image</h1>
                <?php if (!$ImageUpload): ?>
                    <p class="awfl-error">Image uploads are currently disabled by the site admin.</p>
                <?php else: ?>
                    <?php if ($error): ?><p class="awfl-error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                    <?php if ($success): ?><p class="awfl-success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
                    <form method="post" enctype="multipart/form-data" class="awfl-form">
                        <label>Image file (.jpg / .png)<br><input type="file" name="image" accept=".jpg,.jpeg,.png" required></label>
                        <button type="submit">Upload</button>
                    </form>
                <?php endif; ?>
            </main>
            <?php require __DIR__ . '/includes/footer.php';
            exit;
        }

        case 'AllPages': {
            $pages = awfl_list_pages();
            $pagename = 'All pages'; $headerMode = 'interface';
            require __DIR__ . '/includes/header.php'; ?>
            <main class="awfl-main">
                <h1>All pages</h1>
                <ul class="awfl-pagelist">
                <?php foreach ($pages as $p): ?>
                    <li><a href="<?= htmlspecialchars(awfl_page_url_raw($p)) ?>"><?= htmlspecialchars($p) ?></a></li>
                <?php endforeach; ?>
                <?php if (!$pages): ?><li><em>No pages yet.</em></li><?php endif; ?>
                </ul>
            </main>
            <?php require __DIR__ . '/includes/footer.php';
            exit;
        }

        default:
            http_response_code(404);
            $pagename = 'Special page not found'; $headerMode = 'interface';
            require __DIR__ . '/includes/header.php';
            echo '<main class="awfl-main"><h1>Special page not found</h1></main>';
            require __DIR__ . '/includes/footer.php';
            exit;
    }
}

/* ---------------------------------------------------------
   Normal wiki page: view / edit / save / delete / download
   --------------------------------------------------------- */
[$space, $shortName] = awfl_split_title($title);
$pageDisplayName = $shortName;

/* ---- SAVE (POST) ---- */
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    awfl_require_login();
    if (awfl_is_protected($title) && !awfl_is_admin()) {
        http_response_code(403);
        $pagename = $pageDisplayName; $headerMode = 'page';
        require __DIR__ . '/includes/header.php';
        echo '<main class="awfl-main"><h1>Page protected</h1><p class="awfl-error">This page is protected and can only be edited by an administrator.</p>';
        echo '<p><a href="' . htmlspecialchars(awfl_page_url_raw($title)) . '">Back to page</a></p></main>';
        require __DIR__ . '/includes/footer.php';
        exit;
    }
    $content = $_POST['wikitext'] ?? '';
    awfl_save_page($title, $content);
    if (awfl_is_admin()) {
        awfl_record_history($title, $content);
    }
    awfl_redirect(awfl_page_url_raw($title));
    exit;
}

/* ---- PROTECT / UNPROTECT (admin only) ---- */
if ($action === 'protect' || $action === 'unprotect') {
    awfl_require_login();
    awfl_require_admin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        awfl_set_protected($title, $action === 'protect');
        awfl_redirect(awfl_page_url_raw($title));
        exit;
    }
    $verb = $action === 'protect' ? 'Protect' : 'Unprotect';
    $pagename = "$verb \"$pageDisplayName\"?"; $headerMode = 'interface';
    require __DIR__ . '/includes/header.php'; ?>
    <main class="awfl-main awfl-narrow">
        <h1><?= $verb ?> "<?= htmlspecialchars($pageDisplayName) ?>"?</h1>
        <?php if ($action === 'protect'): ?>
            <p>Once protected, only an admin will be able to edit this page.</p>
        <?php else: ?>
            <p>Once unprotected, any logged-in user will be able to edit this page again.</p>
        <?php endif; ?>
        <form method="post">
            <button type="submit"><?= $verb ?></button>
            <a href="<?= htmlspecialchars(awfl_page_url_raw($title)) ?>">Cancel</a>
        </form>
    </main>
    <?php require __DIR__ . '/includes/footer.php';
    exit;
}

/* ---- DELETE ---- */
if ($action === 'delete') {
    awfl_require_admin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        awfl_delete_page($title);
        awfl_redirect('index.php');
        exit;
    }
    $pagename = "Delete \"$pageDisplayName\"?"; $headerMode = 'interface';
    require __DIR__ . '/includes/header.php'; ?>
    <main class="awfl-main awfl-narrow">
        <h1>Delete "<?= htmlspecialchars($pageDisplayName) ?>"?</h1>
        <p>This cannot be undone.</p>
        <form method="post">
            <button type="submit" class="awfl-danger">Yes, delete this page</button>
            <a href="<?= htmlspecialchars(awfl_page_url_raw($title)) ?>">Cancel</a>
        </form>
    </main>
    <?php require __DIR__ . '/includes/footer.php';
    exit;
}

/* ---- DOWNLOAD (txt / md / html / json / pdf / xml) ---- */
if ($action === 'download') {
    $format = $_GET['format'] ?? 'txt';
    $raw = awfl_load_page($title) ?? '';
    $fname = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $shortName);
    $renderedHtml = awfl_parse_wiki($raw);

    switch ($format) {
        case 'html':
            header('Content-Type: text/html; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$fname.html\"");
            $css = awfl_build_download_css();
            $skinIsExternal = preg_match('#^https?://#i', $Skin);
            echo "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"utf-8\">\n";
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
            echo "<title>" . htmlspecialchars($pageDisplayName) . " — " . htmlspecialchars($MetaTitle) . "</title>\n";
            echo "<style>\n" . $css . "\n</style>\n";
            if ($skinIsExternal) {
                echo "<link rel=\"stylesheet\" href=\"" . htmlspecialchars($Skin) . "\">\n";
            }
            echo "</head>\n<body>\n";
            echo "<div class=\"awfl-wrapper\"><div class=\"awfl-body\"><main class=\"awfl-main\">\n";
            echo "<h1>" . htmlspecialchars($pageDisplayName) . "</h1>\n";
            echo "<article class=\"awfl-article\">" . $renderedHtml . "</article>\n";
            echo "</main></div></div>\n";
            echo "</body>\n</html>";
            break;

        case 'md':
            header('Content-Type: text/markdown; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$fname.md\"");
            echo "# " . $pageDisplayName . "\n\n" . awfl_wiki_to_markdown($raw);
            break;

        case 'json':
            header('Content-Type: application/json; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$fname.json\"");
            echo json_encode([
                'title'     => $pageDisplayName,
                'space'     => $space ?: null,
                'exported'  => date('c'),
                'wikitext'  => $raw,
                'html'      => $renderedHtml,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            break;

        case 'xml':
            header('Content-Type: application/xml; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$fname.xml\"");
            echo awfl_generate_page_xml($pageDisplayName, $space ?: null, $raw, $renderedHtml);
            break;

        case 'pdf':
            $plain = awfl_html_to_plaintext($renderedHtml);
            $pdfData = awfl_generate_simple_pdf($pageDisplayName, $plain);
            header('Content-Type: application/pdf');
            header("Content-Disposition: attachment; filename=\"$fname.pdf\"");
            header('Content-Length: ' . strlen($pdfData));
            echo $pdfData;
            break;

        case 'txt':
        default:
            header('Content-Type: text/plain; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$fname.txt\"");
            echo $raw;
            break;
    }
    exit;
}

/* ---- EDIT (view the editor) ---- */
if ($action === 'edit') {
    awfl_require_login();
    if (awfl_is_protected($title) && !awfl_is_admin()) {
        http_response_code(403);
        $pagename = $pageDisplayName; $headerMode = 'page';
        require __DIR__ . '/includes/header.php';
        echo '<main class="awfl-main"><h1>Page protected</h1><p class="awfl-error">This page is protected and can only be edited by an administrator.</p>';
        echo '<p><a href="' . htmlspecialchars(awfl_page_url_raw($title)) . '">Back to page</a></p></main>';
        require __DIR__ . '/includes/footer.php';
        exit;
    }
    $raw = awfl_load_page($title) ?? '';
    $pagename = $pageDisplayName; $headerMode = 'page'; $pageDescription = $MetaDescription;
    require __DIR__ . '/includes/header.php'; ?>
    <main class="awfl-main">
        <h1><?= htmlspecialchars($pageDisplayName) ?><small class="awfl-editing-label">(editing)</small></h1>
        <div class="awfl-editor-toolbar">
            <button type="button" data-wrap="'''" title="Bold"><b>B</b></button>
            <button type="button" data-wrap="''" title="Italic"><i>I</i></button>
            <button type="button" data-wrap="__" title="Underline"><u>U</u></button>
            <button type="button" data-wrap="~~" title="Strikethrough"><s>S</s></button>
            <button type="button" data-wrap="`" title="Inline code"><code>&lt;/&gt;</code></button>
            <button type="button" data-wrap="^^" title="Superscript">x<sup>2</sup></button>
            <button type="button" data-wrap=",," title="Subscript">x<sub>2</sub></button>
            <button type="button" data-wrap="%%" title="Highlight"><mark>H</mark></button>
            <span class="awfl-toolbar-sep"></span>
            <button type="button" id="awfl-btn-p" title="Paragraph">P</button>
            <button type="button" id="awfl-btn-list" title="Bulleted list">List</button>
            <button type="button" id="awfl-btn-quote" title="Blockquote">&ldquo;&rdquo;</button>
            <button type="button" id="awfl-btn-hr" title="Horizontal rule">&mdash;</button>
            <button type="button" id="awfl-btn-codeblock" title="Code block">{ }</button>
            <button type="button" id="awfl-btn-infobox" title="Insert infobox">Infobox</button>
            <span class="awfl-toolbar-sep"></span>
            <button type="button" id="awfl-tab-source" class="awfl-tab active">Source editor</button>
            <button type="button" id="awfl-tab-visual" class="awfl-tab">Visual editor</button>
        </div>
        <form method="post" action="<?= htmlspecialchars(awfl_action_url($title, 'save')) ?>" id="awfl-edit-form">
            <textarea name="wikitext" id="awfl-wikitext" rows="22"><?= htmlspecialchars($raw) ?></textarea>
            <div id="awfl-visual-preview" class="awfl-visual-preview" contenteditable="true" style="display:none;"></div>
            <div class="awfl-editor-actions">
                <button type="submit">Save page</button>
                <a href="<?= htmlspecialchars(awfl_page_url_raw($title)) ?>">Cancel</a>
            </div>
        </form>
    </main>
    <script>
        window.AWFL_SERVER = <?= json_encode(rtrim($Server, '/')) ?>;
        window.AWFL_ROUTE = <?= json_encode($route) ?>;
    </script>
    <?php require __DIR__ . '/includes/footer.php';
    exit;
}

/* ---- VIEW ---- */
$raw = awfl_load_page($title);
$exists = $raw !== null;
$rendered = $exists ? awfl_parse_wiki($raw) : '';
$pageDescription = $exists ? trim(substr(strip_tags($rendered), 0, 160)) : $MetaDescription;
if ($pageDescription === '') $pageDescription = $MetaDescription;
$pagename = $pageDisplayName;
$headerMode = 'page';
$isProtected = awfl_is_protected($title);
require __DIR__ . '/includes/header.php';
?>
<main class="awfl-main">
    <div class="awfl-page-titlebar">
        <h1><?= htmlspecialchars($pageDisplayName) ?><?php if ($space): ?><span class="awfl-space-tag"><?= htmlspecialchars($space) ?></span><?php endif; ?><?php if ($isProtected): ?><span class="awfl-space-tag awfl-protected-tag" title="Only an admin can edit this page">🔒 Protected</span><?php endif; ?></h1>
        <div class="awfl-page-actions">
<?php if ($exists): ?>
            <div class="awfl-download-menu">
                <a href="javascript:void(0)" class="awfl-download-toggle" onclick="this.parentElement.classList.toggle('open')">Download ▾</a>
                <div class="awfl-download-list">
<?php foreach (awfl_download_formats() as $fmt => $info): ?>
                    <a href="<?= htmlspecialchars(awfl_action_url($title, 'download')) ?>&format=<?= htmlspecialchars($fmt) ?>"><?= htmlspecialchars($info['label']) ?></a>
<?php endforeach; ?>
                </div>
            </div>
            <a href="javascript:window.print()">Print / PDF</a>
<?php endif; ?>
<?php if (awfl_is_logged_in() && (!$isProtected || awfl_is_admin())): ?>
            <a href="<?= htmlspecialchars(awfl_action_url($title, 'edit')) ?>"><?= $exists ? 'Edit' : 'Create' ?></a>
<?php endif; ?>
<?php if (awfl_is_admin() && $exists): ?>
            <a href="<?= htmlspecialchars(awfl_action_url($title, 'delete')) ?>" class="awfl-danger-link">Delete</a>
            <a href="<?= htmlspecialchars(awfl_action_url($title, $isProtected ? 'unprotect' : 'protect')) ?>"><?= $isProtected ? 'Unprotect' : 'Protect' ?></a>
<?php endif; ?>
        </div>
    </div>
<?php if ($exists): ?>
    <article class="awfl-article"><?= $rendered ?></article>
<?php else: ?>
    <p class="awfl-missing">There is currently no text in this page.</p>
    <?php if (awfl_is_logged_in()): ?>
        <p><a href="<?= htmlspecialchars(awfl_action_url($title, 'edit')) ?>">Create this page</a></p>
    <?php else: ?>
        <p><a href="<?= htmlspecialchars(awfl_page_url_raw('Special:Login')) ?>">Log in</a> to create this page.</p>
    <?php endif; ?>
<?php endif; ?>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>



















