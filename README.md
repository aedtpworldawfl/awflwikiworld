# AWFLWIKIWORLD

| Field | Value |
| --- | --- |
| title | AWFLWIKIWORLD |
| image | ![](https://aedtpworld.com/icons/aedtpworld.png) |
| caption | The AWFLWIKIWORLD logo |
| Developer | AEDTP WORLD |
| Type | Wiki engine |
| Interface style | MediaWiki / Vector 2022 |
| Written in | PHP 8.3 |
| License | AEDTP WORLD FREE LICENSE (AWFL) |
| Storage | Flat files (.txt pages, .json users/settings) |
| Default skin | vector2022 |



**AWFLWIKIWORLD** is a self-contained, MediaWiki-style wiki engine built in plain PHP. It stores every page as a plain text file, every user as a JSON record, and every visual style as a CSS file — no database, no build step, no external dependencies. Copy the folder onto any PHP 8.3 server (or run it on localhost), fill in one configuration file, and you have a working wiki.

This page explains what AWFLWIKIWORLD is, why it exists, how it is built, and how to use every part of it — as a visitor, as a registered user, and as the administrator.

## What is AWFLWIKIWORLD

AWFLWIKIWORLD is a wiki platform: a website anyone (or anyone you allow) can read, and that logged-in users can create and edit pages on, using a simplified text markup called **AWFLWIKITEXT**. Pages are organized under a root namespace or grouped into named *spaces* (for example, a space called *Music* could hold a page *Music/Buddy DML*). Every page can carry an **infobox** — a small fact-table, the same idea as the boxes you see in the top-right corner of Wikipedia articles.

Unlike most wiki software, AWFLWIKIWORLD keeps everything in ordinary files:

* Each wiki page is one *.txt* file containing raw AWFLWIKITEXT.
* Each user is one JSON record in *users/users.json*.
* Each visual theme is one *.css* file in *skins/*.
* Uploaded pictures are plain *.jpg*/*.png* files in *images/*.

This makes the whole wiki easy to back up (copy the folder), easy to inspect (open any file in a text editor), and easy to move between a local computer and a live server.

## Why AWFLWIKIWORLD

AWFLWIKIWORLD was built for people who want a Wikipedia-like knowledge base without running a database server, without installing a heavyweight CMS, and without losing control of their own content. Because every page is a plain text file:

* You can read or edit the raw content outside the wiki, with any text editor.
* You can move the wiki by copying a folder — no database export/import.
* You can inspect exactly what the system stores, since nothing is hidden inside binary tables.
* You still get the features people expect from a real wiki: accounts, page history for admin edits, protected pages, image galleries, sitemaps, and a skins system.

## How it is Built (Architecture)

The engine is a small number of PHP scripts working together:

* **config.php** — the single file every other script reads. It defines the site name, the site's own URL (*$Server*), the admin login, upload rules, and the URL style.
* **includes/functions.php** — the engine room: user accounts, page storage, the AWFLWIKITEXT parser, image handling, and URL building all live here.
* **includes/header.php** and **includes/footer.php** — the shared page frame (navigation bar, meta tags, footer) that every page includes.
* **index.php** — the main controller. Almost every page you visit — viewing an article, logging in, editing, saving, downloading — passes through this one file. It also serves the built-in Special pages, such as [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch) and [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing).
* **dashboard/index.php** — the admin control panel.
* **allusers/index.php** and **allimages/index.php** — public directories of accounts and uploaded pictures.
* **sitemap/sitemap.php** — builds the search-engine sitemap.

## File Structure

* *index.php* — the interface every visitor loads.
* *config.php* — site settings (see below).
* *.htaccess* — hides raw data files (.txt, .json) from being downloaded directly, and enables pretty URLs.
* *includes/header.php*, *includes/footer.php*, *includes/functions.php* — shared page frame and engine code.
* *users/users.json* — every registered account (hashed passwords only).
* *users/recover.php* — the "forgot password" page.
* *assets/site.css*, *assets/site.js*, *assets/search.js* — the default look, the editor toolbar script, and the header search-box dropdown script. These never change; skins layer on top of them.
* *skins/vector2022.css* — the default skin. More skins can be added here.
* *images/* — uploaded *.jpg*/*.png* files. *images/trash/* holds images an admin has deleted but not yet permanently removed.
* *allusers/index.php* — public list of accounts.
* *allimages/index.php* — public gallery of uploaded images.
* *namespaces/{pagename}.txt* — a root-level article.
* *namespaces/{space name}/{pagename}.txt* — an article inside a named space.
* *![](images/logo/aedtpworld.svg)* — the default logo, used whenever *$Logo* is not overridden.
* *dashboard/index.php* — the admin panel (skins, colors, pages, images).
* *dashboard/history.json*, *dashboard/history.php* — the admin's own edit history.
* *dashboard/protected.json* — the list of protected page titles.
* *sitemap/sitemap.xml*, *sitemap/sitemap.php* — the generated sitemap and the script that builds it.

## Getting Started (Installation)

1. Copy the whole *wikiworld* folder onto your PHP 8.3 server, or into any folder you will run with PHP's built-in server for local testing.
2. Open *config.php* in a text editor.
3. Set **$Server** to the exact base URL you will use to reach the wiki — for example *https://example.com/wiki* or *http://localhost:8099*. This is not auto-detected; you must type it in, because auto-detection breaks on real hosting (reverse proxies, subfolders, and admin/sitemap pages living in subdirectories would each guess a different, wrong URL).
4. Set **$Sitename**, **$MetaTitle** and **$MetaDescription** to describe your wiki.
5. Set **$Username** and **$Password** to your admin login. For $Password, paste a password hash (made in Dashboard → Security) in single quotes; a plain-text password also works but is not recommended.
6. Choose an **$route** mode (explained below).
7. Visit *$Server/index.php* in a browser. You should see the built-in Main Page.

If you see a message saying the wiki "is not configured yet," it means *$Server* is still blank in *config.php* — fill it in and reload.

### The $route modes

*$route* controls the shape of every page's URL:

* **"0"** — *{$Server}/index.php?title=Pagename* (and *?title=Space/Pagename*). Works everywhere, no server configuration needed. This is the default.
* **"1"** — *{$Server}/Pagename* (and *{$Server}/Space/Pagename*). Pretty URLs. Requires the supplied *.htaccess* and an Apache server with *mod_rewrite* and *AllowOverride All*.
* **"2"** — *{$Server}/namespaces/Pagename*. Looks like a direct link into the *namespaces/* folder, but is still served by *index.php* — the *.htaccess* rules stop anyone from downloading the raw *.txt* file directly.

## Using AWFLWIKIWORLD as a Visitor (not logged in)

Anyone can:

* Read any page.
* Use the search box in the header: type, and a dropdown offers up to five matching pages, each with its first image — click one to open it. Press Enter to jump straight to a title, or use [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch) for full results.
* Browse [Special:AllPages](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) to see every page that exists.
* Browse [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing) to see every page that is linked to but doesn't exist yet.
* Browse the image gallery at *allimages/index.php* — see every uploaded picture, in a 7-column grid or a sortable detail table, and use the **Copy link** / **Copy name** buttons on any image.
* Browse the account directory at *allusers/index.php*.
* Download any page as a **.txt** file (the raw AWFLWIKITEXT) or as a self-contained **.html** file (styled the same as the live page, so it looks right even opened offline).
* Print any page (which also lets you "print to PDF" from the browser's print dialog).

Visitors cannot create pages, edit pages, or upload images — those require an account.

## Using AWFLWIKIWORLD as a Registered User

### Creating an account

Go to [Special:Register](https://aedtpworld.com/wikiworld/index.php/Special%3ARegister) and fill in a username, display name, email, password, and a security question and answer (used later for password recovery). Usernames must be 3–32 characters (letters, numbers, *-*, *_*, *.*), and the name **admin** is reserved. Once registered, log in at [Special:Login](https://aedtpworld.com/wikiworld/index.php/Special%3ALogin).

### Recovering a forgotten password

Open *users/recover.php*. Enter your username and the email you registered with, then answer your security question, and you can set a brand-new password — no email link required. The site never says whether a username exists: unless the username and email both match, you are shown a placeholder question instead of yours, and every wrong answer gets the same message. After 5 failed tries from the same address for the same username, recovery is locked for 15 minutes. An account registered without an email cannot be recovered this way — ask the admin to set a new password. Passwords and security answers are never stored in plain text; only their hashes are kept, so recovery always works by re-checking your answer, never by revealing the old password.

### Creating and editing pages

To create a page, search for or navigate to its title and click **Create**. To edit an existing page, click **Edit**. Both open the same editor:

* A **toolbar** with **B** (bold), **I** (italic), **U** (underline), **P** (paragraph break), **List** (bulleted list) and **Infobox** (inserts a ready-made infobox template) — each wraps or inserts AWFLWIKITEXT around your current text selection.
* A **source editor**, the plain text box where the raw AWFLWIKITEXT lives.
* A **visual editor (preview)** tab, which sends your current text to the server and shows exactly how it will render, without saving anything yet.

Click **Save page** to publish your changes immediately.

**Note on protected pages:** if an admin has protected a page, only an admin can edit it — the Edit link disappears (or is blocked server-side even if requested directly) for everyone else.

### AWFLWIKITEXT — the markup language

AWFLWIKITEXT is a small, MediaWiki-inspired markup language. All of it can be typed by hand or inserted with the editor toolbar:

* **Bold**: wrap text in three apostrophes, like '''this'''.
* *Italic*: wrap text in two apostrophes, like ''this''.
* Underline: wrap text in two underscores, like __this__.
* Headings: surround a line with two to six equals signs, like == A Heading == (more equals signs make a smaller heading).
* Bulleted list: start a line with an asterisk and a space, like * An item.
* Numbered list: start a line with a hash and a space, like # An item.
* Paragraphs: leave a blank line between blocks of text — each block becomes its own paragraph automatically.
* Internal link: [[Page Name]], or [[Page Name|words to display instead]]. A link to a page that does not exist yet is still shown, in a different style, so you can click it to create that page.
* External link: [http://example.com link text].
* Bare images: any line that is just an image filename or URL ending in *.jpg*, *.jpeg*, *.png*, *.gif*, *.svg* or *.webp* is automatically rendered as a picture, shown at 250 pixels wide.

### Infoboxes

An infobox is a small fact-table, usually shown floated at the top right of an article — exactly like the one at the top of this page. Write one like this:


| Field | Value |
| --- | --- |
| title | Subject Name |
| image | some-picture.jpg |
| caption | A short caption for the picture |
| Born | 1 January 2000 |
| Occupation | Example, Example |
| anything | you like |



Rules:

* **title**, **image** and **caption** are each allowed only **once** per infobox, and always appear in that fixed order — title first, then the image, then the caption underneath it — no matter where you typed them in the block.
* Every other line becomes its own row: the part before the equals sign is shown in **bold** as the row's label, and the part after it is the value. You can add as many of these as you like, and they appear in the exact order you wrote them.
* **image** turns any filename or link ending in an image extension into a real picture, shown at 250 pixels wide.
* **caption** is shown under the picture and used as its alt text.
* Outside of an infobox, ordinary images placed directly in the page text are also shown at 250 pixels wide by default.

### Uploading images

If the admin has enabled uploads, any logged-in user can add a picture at [Special:Upload](https://aedtpworld.com/wikiworld/index.php/Special%3AUpload). Only **.jpg** and **.png** files are accepted. Once uploaded, an image can be referenced in any page (bare, or inside an infobox) just by its filename, for example *![](images/mypicture.jpg)*.

### Downloading and printing

Every page offers:

* **Download .txt** — the raw AWFLWIKITEXT, exactly as stored.
* **Download .html** — a single, self-contained HTML file with the page's styling embedded in it, so it looks right even with no internet connection.
* **Print / PDF** — opens your browser's print dialog, which on most browsers can also "print" straight to a PDF file.

## Using AWFLWIKIWORLD as the Administrator

Log in with the **$Username** / **$Password** set in *config.php*. The admin account is not a row in *users/users.json* — it is defined directly in the configuration file and always has full control.

### The Dashboard

Open the **Dashboard** link in the navigation bar (*dashboard/index.php*). It has five sections:

* **Skins** — pick which skin is active site-wide, or create a brand-new skin by typing its CSS. The default **vector2022** skin can be selected but never edited or deleted; create a new skin instead if you want to change the look.
* **Colors** — a small palette for the site's core colors (header background, header text, buttons, button text, wiki page text, and links). Pick a color visually or type a hex code directly; the change applies across the whole wiki immediately, on top of whichever skin is active.
* **Pages** — a list of every page, with a delete button per page and a "Delete all pages" button. This is also where you can trigger the sitemap to regenerate on demand.
* **Images** — every uploaded image, with a delete button per image.
* **Security** — a checklist (admin password hashed, HTTPS, session cookie, login throttling, private-file rules) and a tool that turns the admin password you type into a hash to paste into *config.php*.

### Managing users

The [All users](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) directory lists every account except the admin's own. Anyone can view it; only the admin sees extra controls per row to:

* **Rename** a user's username.
* **Set** a new password for a user directly — no need to know their old one, since passwords are stored hashed.
* **Remove** a user entirely. Removing a user deletes their account record, but **never** deletes the pages or edits they made — those stay exactly as they are.

The admin account can never remove itself. A regular user can remove their own account at any time (through the admin, since only the admin can perform removals) and, likewise, their past contributions are kept.

### Managing images (All Images)

*allimages/index.php* is visible to everyone, but the admin sees more:

* **Content view** — a seven-column grid of thumbnails, each with **Copy link** and **Copy file name** buttons.
* **Detail view** — a sortable table (click any column heading to sort ascending or descending) showing file name, extension, size, and upload date. Everyone gets these four columns; the admin gets a fifth column of checkboxes.
* As admin, above the table you get **Delete selected** (moves the chosen images to *images/trash/* rather than erasing them outright), **Restore selected** (only shown while viewing the "Deleted" tab, moves images back out of the trash), and **Rename selected** (type a new name into the box next to each checked row, then click to apply).

### Page protection

Every page has a **Protect** button, visible only to the admin. Protecting a page means only the admin can edit it from then on — anyone else who tries (even by submitting the edit form directly) is blocked with a clear message. Clicking **Unprotect** opens editing back up to every logged-in user. Only the admin can protect or unprotect a page.

### Admin edit history

Every time the **admin** saves a page, AWFLWIKIWORLD quietly records that exact content in *dashboard/history.json* — the page's name, its filename, its space (if any), the date, and the full text. This history holds only the **one latest** admin edit for each page; saving the same page again simply updates that one record rather than creating a new one.

Open **History** in the navigation bar (admin only) to see every recorded page, sortable by any column. Two actions are available, both requiring on-screen confirmation:

* **Delete selected** — permanently removes the chosen history record(s).
* **Restore selected** — writes the saved content straight back to the live page, overwriting whatever is there now. This is most useful after a regular user's edit has changed a page the admin had previously written, since only admin saves are tracked — restoring brings the admin's version back.

### Sitemap generation

*sitemap/sitemap.php* builds a search-engine sitemap of every page. If the list of pages would make a single file larger than roughly 1&nbsp;megabyte, it is automatically split into several files (*sitemap-1.xml*, *sitemap-2.xml*, and so on), and *sitemap.xml* is (re)written as an index that lists every split file. It also writes *robots.txt* pointing at that index. Because search engines crawl an index and then follow every entry inside it automatically, submitting the single *sitemap.xml* address once is enough — you never need to resubmit each split by hand.

## Namespaces and Spaces

A page can live at the root (*namespaces/Pagename.txt*) or inside a named space (*namespaces/Space Name/Pagename.txt*), written and linked as *Space Name/Pagename*. Spaces are a simple way to group related pages, the same way a folder groups related files.

## Title Handling

Page titles can contain spaces when you type or link them — for example, *Buddy DML*. Behind the scenes, AWFLWIKIWORLD always **stores and links** that page using underscores instead of spaces (*Buddy_DML.txt*, linked as *Buddy_DML*), while still **displaying** the title with spaces wherever it is shown to a reader. This matches how MediaWiki itself handles titles, and it means links, downloads and file names never contain raw spaces.

## Security Notes

* Passwords and security-question answers are never stored as plain text — only their one-way hashes are kept in *users/users.json*.
* The supplied *.htaccess* blocks direct web access to every raw *.txt* and *.json* file, so page content and account records can never be downloaded straight off the server by guessing a URL — they can only be reached through the wiki's own pages.
* Only the admin can delete users, delete or protect pages, change the skin or colors, or manage images and history.
* Every form that changes something — saving, deleting and protecting pages, the dashboard, user and image management, log in, register and password recovery — carries a secret per-session token (CSRF protection). A request without it is refused.
* Log in and password recovery are throttled: 5 failed attempts for the same username from the same address lock that pair for 15 minutes. A failed log in never says whether the username exists.
* The session cookie is HttpOnly and SameSite=Lax (and Secure on https), and a fresh session id is issued at log in and log out.
* The admin password in *config.php* can be a password hash — create one in **Dashboard → Security**. A plain-text password still works, but the dashboard warns you until you replace it.
* The supplied *.htaccess* also blocks copies of the configuration file, such as *config.php.bak*.

## Searching and Missing Pages

### The search box

Start typing in the search box in the header and a dropdown shows up to **five** likely matches. Each row shows the **first image found on that page** (the infobox picture if the page has one, otherwise the first picture in the body — or a letter tile if the page has no image), a divider, and then the **page name**, like this: *[image] | Page Name*. Click a row, or use the arrow keys and press Enter, to open that page. Press Esc to close the dropdown.

Matches are ranked so the best come first: an exact page name, then names that start with what you typed, then names with a word that starts with it, then names that contain it. Near-misses are tolerated (a small typo can still find the right page), and pages whose text contains your words come last. If you press Enter without choosing a row, you are still taken straight to the title you typed, exactly as before.

The last row of the dropdown, **See all results**, opens [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch). That page shows up to 50 results, each with a thumbnail and a short text snippet, and offers a link to create the page if nothing with that exact name exists yet. You can also open it directly with *index.php?title=Special:Search&q=your words*.

### Special:Missing (red links)

[Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing) lists every **red link** on the wiki — every [[Page Name]] that appears in some page but whose target page doesn't exist yet. Each missing page is shown as [[Page Name]], with a **Linked from** column naming every page that links to it. Click a missing page and it opens directly, ready to be created. Once the page is created, it drops off the list on its own.

It is a ready-made to-do list for growing the wiki. It ignores category and special-page links, links shown inside code blocks, and links into spaces that don't exist (an admin must create those first). It is also listed in the Special pages directory.

## Frequently Asked Questions

* **Do I need a database?** No. Everything is stored in plain text and JSON files.
* **Can I move my wiki to another server?** Yes — copy the whole folder, update *$Server* in *config.php* to the new address, and you are done.
* **Can I change how the wiki looks without touching PHP?** Yes — skins are plain CSS files, created and switched from the Dashboard.
* **What happens to a user's pages if their account is removed?** Nothing — page content is never tied to the account being present, so removing a user never deletes what they wrote.
* **What if I delete an image by mistake?** As admin, open the "Deleted" tab in [All Images](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) and restore it — deleted images go to a trash folder rather than being erased immediately.
* **Can a regular user undo someone else's edit?** AWFLWIKIWORLD does not keep a version history for every user — only the admin's own latest edit to each page is tracked, in the Dashboard's History section, precisely so an admin can always restore their own last version.

## See Also

* [Special:AllPages](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages)
* [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch)
* [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing)
* [Special:Register](https://aedtpworld.com/wikiworld/index.php/Special%3ARegister)
* [Special:Login](https://aedtpworld.com/wikiworld/index.php/Special%3ALogin)
* [Special:Upload](https://aedtpworld.com/wikiworld/index.php/Special%3AUpload)


| Field | Value |
| --- | --- |
| title | AWFLWIKIWORLD |
| image | ![](https://aedtpworld.com/wikiworld/logo/aedtpworld.svg) |
| caption | The free encyclopedia that anyone can edit |
| Type | Website, Wiki, Encyclopedia |
| Developer | AEDTP WORLD |
| Base URL | https://aedtpworld.com/wikiworld |
| License | AEDTP WORLD FREE LICENSE (AWFL) |
| Copyright | © 2026 AEDTP WORLD |



## AWFLWIKIWORLD

**AWFLWIKIWORLD is the free encyclopedia that anyone can edit.**

AWFLWIKIWORLD is a free knowledge base for Ghana, Africa and the world. Anyone can read. Anyone with an account can edit.

## Welcome

**Welcome to AWFLWIKIWORLD**

## Browse

* [All Pages](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) — See every article on this wiki
* [All Images](https://aedtpworld.com/wikiworld/index.php/AllImages) — Browse all photos and media
* [All Users](https://aedtpworld.com/wikiworld/index.php/AllUsers) — See all contributors
* [Random Page](https://aedtpworld.com/wikiworld/index.php/Special%3ARandom) — Discover something new
* [Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch) — Find pages with a live preview
* [Missing Pages](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing) — See red links waiting to be created

## What we cover

Biographies, music, history, education, technology, culture, Ghana news, African heritage and more.

## Start here

Search for a topic above. Or create a new article and share your knowledge.

## Actions

* [Download .txt](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=txt)
* [Download .md](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=md)
* [Download .html](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=html)
* [Download .json](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=json)
* [Download .pdf](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=pdf)
* [Download .xml](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=xml)
* [Download MediaWiki .txt](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=mediawiki.txt)
* [Download Wikidot .txt](https://aedtpworld.com/wikiworld/index.php/Main_Page?action=download&format=wikidot.txt)
* Print / PDF

## Navigation

* [Log in](https://aedtpworld.com/wikiworld/index.php/Special%3ALogin)
* [Register](https://aedtpworld.com/wikiworld/index.php/Special%3ARegister)
* [Random page](https://aedtpworld.com/wikiworld/index.php/Special%3ARandom)
* [Special pages](https://aedtpworld.com/wikiworld/special/index.php)
* [All users](https://aedtpworld.com/wikiworld/allusers/index.php)
* [All images](https://aedtpworld.com/wikiworld/allimages/index.php)
* [All pages](https://aedtpworld.com/wikiworld/allpages/index.php)
* [Sitemap](https://aedtpworld.com/wikiworld/sitemap/sitemap.xml)

## Footer

Powered by AWFLWIKIWORLD — content available under the AEDTP WORLD FREE LICENSE (AWFL)

## External Links

* [AWFLWIKIWORLD Official](https://aedtpworld.com/wikiworld)
* [Logo](https://![](images/aedtpworld.com/wikiworld/logo/aedtpworld.svg))

[Category:AEDTP WORLD](https://aedtpworld.com/wikiworld/index.php/Category%3AAEDTP_WORLD)
[Category:Technology](https://aedtpworld.com/wikiworld/index.php/Category%3ATechnology)

# AWFLWIKIWORLD

| Field | Value |
| --- | --- |
| title | AWFLWIKIWORLD |
| image | ![](https://aedtpworld.com/wikiworld/logo/aedtpworld.svg) |
| caption | The AWFLWIKIWORLD logo |
| Developer | AEDTP WORLD |
| Type | Wiki engine |
| Interface style | MediaWiki / Vector 2022 |
| Written in | PHP 8.3 |
| License | AEDTP WORLD FREE LICENSE (AWFL) |
| Storage | Flat files (.txt pages, .json users/settings) |
| Default skin | vector2022 |



**AWFLWIKIWORLD** is a self-contained, MediaWiki-style wiki engine built in plain PHP. It stores every page as a plain text file, every user as a JSON record, and every visual style as a CSS file — no database, no build step, no external dependencies. Copy the folder onto any PHP 8.3 server (or run it on localhost), fill in one configuration file, and you have a working wiki.

This page explains what AWFLWIKIWORLD is, why it exists, how it is built, and how to use every part of it — as a visitor, as a registered user, and as the administrator.

## Origins

An early prototype of AWFLWIKIWORLD stored pages in a database. That approach was ~~kept as the default~~ <u>replaced</u> once the flat-file design proved simpler to host, back up, and inspect — every page is now just a `.txt` file waiting to be opened in any text editor.

## What is AWFLWIKIWORLD

AWFLWIKIWORLD is a wiki platform: a website anyone (or anyone you allow) can read, and that logged-in users can create and edit pages on, using a simplified text markup called **AWFLWIKITEXT**. Pages are organized under a root namespace or grouped into named *spaces* (for example, a space called *Music* could hold a page *Music/Buddy DML*). Every page can carry an **infobox** — a small fact-table, the same idea as the boxes you see in the top-right corner of Wikipedia articles.

Unlike most wiki software, AWFLWIKIWORLD keeps everything in ordinary files:

* Each wiki page is one *.txt* file containing raw AWFLWIKITEXT.
* Each user is one JSON record in *users/users.json*.
* Each visual theme is one *.css* file in *skins/*.
* Uploaded pictures are plain *.jpg*/*.png* files in *images/*.

This makes the whole wiki easy to back up (copy the folder), easy to inspect (open any file in a text editor), and easy to move between a local computer and a live server.

## Why AWFLWIKIWORLD

AWFLWIKIWORLD was built for people who want a Wikipedia-like knowledge base without running a database server, without installing a heavyweight CMS, and without losing control of their own content. Because every page is a plain text file:

> If you can read a text file, you can read the entire wiki. If you can write one, you can back it up, move it, or fix it by hand.

* You can read or edit the raw content outside the wiki, with any text editor.
* You can move the wiki by copying a folder — no database export/import.
* You can inspect exactly what the system stores, since nothing is hidden inside binary tables.
* You still get the features people expect from a real wiki: accounts, page history for admin edits, protected pages, image galleries, sitemaps, and a skins system.

## How it is Built (Architecture)

The engine is a small number of PHP scripts working together:

* **config.php** — the single file every other script reads. It defines the site name, the site's own URL (*$Server*), the admin login, upload rules, and the URL style.
* **includes/functions.php** — the engine room: user accounts, page storage, the AWFLWIKITEXT parser, image handling, and URL building all live here.
* **includes/header.php** and **includes/footer.php** — the shared page frame (navigation bar, meta tags, footer) that every page includes.
* **index.php** — the main controller. Almost every page you visit — viewing an article, logging in, editing, saving, downloading — passes through this one file. It also serves the built-in Special pages, such as [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch) and [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing).
* **dashboard/index.php** — the admin control panel.
* **allusers/index.php** and **allimages/index.php** — public directories of accounts and uploaded pictures.
* **sitemap/sitemap.php** — builds the search-engine sitemap.

## File Structure

* *index.php* — the interface every visitor loads.
* *config.php* — site settings (see below).
* *.htaccess* — hides raw data files (.txt, .json) from being downloaded directly, and enables pretty URLs.
* *includes/header.php*, *includes/footer.php*, *includes/functions.php* — shared page frame and engine code.
* *users/users.json* — every registered account (hashed passwords only).
* *users/recover.php* — the "forgot password" page.
* *assets/site.css*, *assets/site.js*, *assets/sortable.js*, *assets/search.js* — the default look, the editor toolbar script, the click-to-sort script for sortable tables, and the header search-box dropdown script. These never change; skins layer on top of them.
* *skins/vector2022.css* — the default skin. More skins can be added here.
* *images/* — uploaded *.jpg*/*.png* files. *images/trash/* holds images an admin has deleted but not yet permanently removed.
* *allusers/index.php* — public list of accounts.
* *allimages/index.php* — public gallery of uploaded images.
* *namespaces/{pagename}.txt* — a root-level article.
* *namespaces/{space name}/{pagename}.txt* — an article inside a named space.
* *![](images/logo/aedtpworld.svg)* — the default logo, used whenever *$Logo* is not overridden.
* *dashboard/index.php* — the admin panel (skins, colors, pages, images).
* *dashboard/history.json*, *dashboard/history.php* — the admin's own edit history.
* *dashboard/protected.json* — the list of protected page titles.
* *sitemap/sitemap.xml*, *sitemap/sitemap.php* — the generated sitemap and the script that builds it.

## Getting Started (Installation)

1. Copy the whole *wikiworld* folder onto your PHP 8.3 server, or into any folder you will run with PHP's built-in server for local testing.
2. Open *config.php* in a text editor — this is the <u>only</u> file you need to touch by hand to get a working wiki.
3. Set **$Server** to the exact base URL you will use to reach the wiki, for example:


```
$Server = "https://example.com/wiki";
// or, for local testing:
$Server = "http://localhost:8099";
```


This is not auto-detected; you must type it in, because auto-detection breaks on real hosting (reverse proxies, subfolders, and admin/sitemap pages living in subdirectories would each guess a different, wrong URL).

==Set $Server to its real, final address before you invite anyone else to the wiki — every link, download, and image on the site is built from this one value.==

1. Set **$Sitename**, **$MetaTitle** and **$MetaDescription** to describe your wiki.
2. Set **$Username** and **$Password** to your admin login. For $Password, paste a password hash (made in Dashboard → Security) in single quotes; a plain-text password also works but is not recommended.
3. Choose an **$route** mode (explained below).
4. Visit *$Server/index.php* in a browser. You should see the built-in Main Page.

If you see a message saying the wiki "is not configured yet," it means *$Server* is still blank in *config.php* — fill it in and reload.

### The $route modes

*$route* controls the shape of every page's URL:

* **"0"** — *{$Server}/index.php?title=Pagename* (and *?title=Space/Pagename*). Works everywhere, no server configuration needed. This is the default.
* **"1"** — *{$Server}/Pagename* (and *{$Server}/Space/Pagename*). Pretty URLs. Requires the supplied *.htaccess* and an Apache server with *mod_rewrite* and *AllowOverride All*.
* **"2"** — *{$Server}/namespaces/Pagename*. Looks like a direct link into the *namespaces/* folder, but is still served by *index.php* — the *.htaccess* rules stop anyone from downloading the raw *.txt* file directly.

## Using AWFLWIKIWORLD as a Visitor (not logged in)

Anyone can:

* Read any page.
* Use the search box in the header: type, and a dropdown offers up to five matching pages, each with its first image — click one to open it. Press Enter to jump straight to a title, or use [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch) for full results.
* Browse [Special:AllPages](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) to see every page that exists.
* Browse [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing) to see every page that is linked to but doesn't exist yet.
* Browse the image gallery at *allimages/index.php* — see every uploaded picture, in a 7-column grid or a sortable detail table, and use the **Copy link** / **Copy name** buttons on any image.
* Browse the account directory at *allusers/index.php*.
* Download any page as a **.txt** file (the raw AWFLWIKITEXT) or as a self-contained **.html** file (styled the same as the live page, so it looks right even opened offline).
* Print any page (which also lets you "print to PDF" from the browser's print dialog).

Visitors cannot create pages, edit pages, or upload images — those require an account.

## Using AWFLWIKIWORLD as a Registered User

### Creating an account

Go to [Special:Register](https://aedtpworld.com/wikiworld/index.php/Special%3ARegister) and fill in a username, display name, email, password, and a security question and answer (used later for password recovery). Usernames must be 3–32 characters (letters, numbers, *-*, *_*, *.*), and the name **admin** is reserved. Once registered, log in at [Special:Login](https://aedtpworld.com/wikiworld/index.php/Special%3ALogin).

### Recovering a forgotten password

Open *users/recover.php*. Enter your username and the email you registered with, then answer your security question, and you can set a brand-new password — no email link required. The site never says whether a username exists: unless the username and email both match, you are shown a placeholder question instead of yours, and every wrong answer gets the same message. After 5 failed tries from the same address for the same username, recovery is locked for 15 minutes. An account registered without an email cannot be recovered this way — ask the admin to set a new password. Passwords and security answers are never stored in plain text; only their hashes are kept, so recovery always works by re-checking your answer, never by revealing the old password.

### Creating and editing pages

To create a page, search for or navigate to its title and click **Create**. To edit an existing page, click **Edit**. Both open the same editor:

* A **toolbar** with **B** (bold), **I** (italic), **U** (underline), **P** (paragraph break), **List** (bulleted list), **1. List** (numbered list), the **arrow buttons** (indent or outdent a list item to nest it), **Table** (inserts a table), **Infobox** (inserts a ready-made infobox template), **Cite** (inserts a footnote) and **References** (adds the section that lists the footnotes) — each wraps or inserts AWFLWIKITEXT around your current text selection.
* A **source editor**, the plain text box where the raw AWFLWIKITEXT lives.
* A **visual editor (preview)** tab, which sends your current text to the server and shows exactly how it will render, without saving anything yet.

Click **Save page** to publish your changes immediately.

**Note on protected pages:** if an admin has protected a page, only an admin can edit it — the Edit link disappears (or is blocked server-side even if requested directly) for everyone else.

### AWFLWIKITEXT — the markup language

AWFLWIKITEXT is a small, MediaWiki-inspired markup language. All of it can be typed by hand or inserted with the editor toolbar:

* **Bold**: wrap text in three apostrophes, like '''this'''.
* *Italic*: wrap text in two apostrophes, like ''this''.
* Underline: wrap text in two underscores, like __this__.
* Headings: surround a line with one to six equals signs, like = A Heading = for level 1 or == A Heading == for level 2 (more equals signs make progressively smaller headings, down to level 6).
* Bulleted list: start a line with an asterisk and a space, like * An item.
* Numbered list: start a line with a hash and a space, like # An item.
* Nested list: repeat the marker to go one level deeper, like ** A sub-item or ## A sub-step (the two kinds can be mixed).
* Table: open with {|, close with |}, start rows with |-, write header cells after ! and ordinary cells after |, add a caption with |+, and make it click-to-sort with {| class="sortable".
* Paragraphs: leave a blank line between blocks of text — each block becomes its own paragraph automatically.
* Internal link: [[Page Name]], or [[Page Name|words to display instead]]. A link to a page that does not exist yet is still shown, in a different style, so you can click it to create that page.
* External link: [http://example.com link text].
* Bare images: any line that is just an image filename or URL ending in *.jpg*, *.jpeg*, *.png*, *.gif*, *.svg* or *.webp* is automatically rendered as a picture, shown at 250 pixels wide.
* Strikethrough: wrap text in two tildes, like ~~this~~ — for example, a ~~retired~~ feature name.
* Inline code: wrap text in backticks, like `this` — for example, `config.php`.
* Superscript: wrap text in two carets, like ^^this^^ — for example, E=mc<sup>2</sup>.
* Subscript: wrap text in two commas, like ,,this,, — for example, H<sub>2</sub>O.
* Highlight: wrap text in two percent signs, like %%this%% — for example, a ==highlighted== note.
* Blockquote: start a line with >  — for example, a quoted line stands apart from the surrounding paragraph.
* Horizontal rule: a line of four or more dashes on its own, like ----, draws a divider between sections.
* Code block: wrap one or more lines in triple curly braces, like {{{ your code here }}}, to show preformatted, monospaced text.
* Footnote: wrap a source or comment in a ref tag pair, like <ref>Source text, or [https://example.com a link]</ref>, and it becomes a numbered note.[^1] Give a note a name to cite it again, and a == References == section or a <references/> tag prints the list.
* Table of contents: any page with four or more headings gets a collapsible "Contents" box automatically, and every heading gets an anchor so it can be linked to directly. Put __NOTOC__ anywhere on a page to leave the box out.

This article uses every one of these formats at least once. For the full reference, with a worked example of each format side by side with its raw syntax, see [AWFLWIKITEXT](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/AWFLWIKITEXT).

### Infoboxes

An infobox is a small fact-table, usually shown floated at the top right of an article — exactly like the one at the top of this page. Write one like this:


| Field | Value |
| --- | --- |
| title | Subject Name |
| image | some-picture.jpg |
| caption | A short caption for the picture |
| Born | 1 January 2000 |
| Occupation | Example, Example |
| anything | you like |



Rules:

* **title**, **image** and **caption** are each allowed only **once** per infobox, and always appear in that fixed order — title first, then the image, then the caption underneath it — no matter where you typed them in the block.
* Every other line becomes its own row: the part before the equals sign is shown in **bold** as the row's label, and the part after it is the value. You can add as many of these as you like, and they appear in the exact order you wrote them.
* **image** turns any filename or link ending in an image extension into a real picture, shown at 250 pixels wide.
* **caption** is shown under the picture and used as its alt text.
* Outside of an infobox, ordinary images placed directly in the page text are also shown at 250 pixels wide by default.

### Uploading images

If the admin has enabled uploads, any logged-in user can add a picture at [Special:Upload](https://aedtpworld.com/wikiworld/index.php/Special%3AUpload). Only **.jpg** and **.png** files are accepted. Once uploaded, an image can be referenced in any page (bare, or inside an infobox) just by its filename, for example *![](images/mypicture.jpg)*.

### Downloading and printing

Every page offers:

* **Download .txt** — the raw AWFLWIKITEXT, exactly as stored.
* **Download .html** — a single, self-contained HTML file with the page's styling embedded in it, so it looks right even with no internet connection.
* **Print / PDF** — opens your browser's print dialog, which on most browsers can also "print" straight to a PDF file.

## Using AWFLWIKIWORLD as the Administrator

Log in with the **$Username** / **$Password** set in *config.php*. The admin account is not a row in *users/users.json* — it is defined directly in the configuration file and always has full control.

### The Dashboard

Open the **Dashboard** link in the navigation bar (*dashboard/index.php*). It has five sections:

* **Skins** — pick which skin is active site-wide, or create a brand-new skin by typing its CSS. The default **vector2022** skin can be selected but never edited or deleted; create a new skin instead if you want to change the look.
* **Colors** — a small palette for the site's core colors (header background, header text, buttons, button text, wiki page text, and links). Pick a color visually or type a hex code directly; the change applies across the whole wiki immediately, on top of whichever skin is active.
* **Pages** — a list of every page, with a delete button per page and a "Delete all pages" button. This is also where you can trigger the sitemap to regenerate on demand.
* **Images** — every uploaded image, with a delete button per image.
* **Security** — a checklist (admin password hashed, HTTPS, session cookie, login throttling, private-file rules) and a tool that turns the admin password you type into a hash to paste into *config.php*.

### Managing users

The [All users](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) directory lists every account except the admin's own. Anyone can view it; only the admin sees extra controls per row to:

* **Rename** a user's username.
* **Set** a new password for a user directly — no need to know their old one, since passwords are stored hashed.
* **Remove** a user entirely. Removing a user deletes their account record, but **never** deletes the pages or edits they made — those stay exactly as they are.

The admin account can never remove itself. A regular user can remove their own account at any time (through the admin, since only the admin can perform removals) and, likewise, their past contributions are kept.

### Managing images (All Images)

*allimages/index.php* is visible to everyone, but the admin sees more:

* **Content view** — a seven-column grid of thumbnails, each with **Copy link** and **Copy file name** buttons.
* **Detail view** — a sortable table (click any column heading to sort ascending or descending) showing file name, extension, size, and upload date. Everyone gets these four columns; the admin gets a fifth column of checkboxes.
* As admin, above the table you get **Delete selected** (moves the chosen images to *images/trash/* rather than erasing them outright), **Restore selected** (only shown while viewing the "Deleted" tab, moves images back out of the trash), and **Rename selected** (type a new name into the box next to each checked row, then click to apply).

### Page protection

Every page has a **Protect** button, visible only to the admin. Protecting a page means only the admin can edit it from then on — anyone else who tries (even by submitting the edit form directly) is blocked with a clear message. Clicking **Unprotect** opens editing back up to every logged-in user. Only the admin can protect or unprotect a page.

### Admin edit history

Every time the **admin** saves a page, AWFLWIKIWORLD quietly records that exact content in *dashboard/history.json* — the page's name, its filename, its space (if any), the date, and the full text. This history holds only the **one latest** admin edit for each page; saving the same page again simply updates that one record rather than creating a new one.

Open **History** in the navigation bar (admin only) to see every recorded page, sortable by any column. Two actions are available, both requiring on-screen confirmation:

* **Delete selected** — permanently removes the chosen history record(s).
* **Restore selected** — writes the saved content straight back to the live page, overwriting whatever is there now. This is most useful after a regular user's edit has changed a page the admin had previously written, since only admin saves are tracked — restoring brings the admin's version back.

### Sitemap generation

*sitemap/sitemap.php* builds a search-engine sitemap of every page. If the list of pages would make a single file larger than roughly 1&nbsp;megabyte, it is automatically split into several files (*sitemap-1.xml*, *sitemap-2.xml*, and so on), and *sitemap.xml* is (re)written as an index that lists every split file. It also writes *robots.txt* pointing at that index. Because search engines crawl an index and then follow every entry inside it automatically, submitting the single *sitemap.xml* address once is enough — you never need to resubmit each split by hand.

## Namespaces and Spaces

A page can live at the root (*namespaces/Pagename.txt*) or inside a named space (*namespaces/Space Name/Pagename.txt*), written and linked as *Space Name/Pagename*. Spaces are a simple way to group related pages, the same way a folder groups related files.

Only an admin can create, rename, or delete a space, from the Dashboard's Namespaces screen. If you search for or link to a space that doesn't exist yet — this page itself lives in the space **AWFLWIKIWORLD**, at the page also named **AWFLWIKIWORLD**, so its full title is AWFLWIKIWORLD/AWFLWIKIWORLD — you are sent back to the wiki's home page instead of the space being created for you. This is true for every visitor, logged in or not: browsing to a nonexistent space never creates it. Once a space does exist, any logged-in user can create pages inside it.

## Title Handling

Page titles can contain spaces when you type or link them — for example, *Buddy DML*. Behind the scenes, AWFLWIKIWORLD always **stores and links** that page using underscores instead of spaces (*Buddy_DML.txt*, linked as *Buddy_DML*), while still **displaying** the title with spaces wherever it is shown to a reader. This matches how MediaWiki itself handles titles, and it means links, downloads and file names never contain raw spaces.

## Security Notes

* Passwords and security-question answers are never stored as plain text — only their one-way hashes are kept in *users/users.json*.
* The supplied *.htaccess* blocks direct web access to every raw *.txt* and *.json* file, so page content and account records can never be downloaded straight off the server by guessing a URL — they can only be reached through the wiki's own pages.
* Only the admin can delete users, delete or protect pages, change the skin or colors, or manage images and history.
* Every form that changes something — saving, deleting and protecting pages, the dashboard, user and image management, log in, register and password recovery — carries a secret per-session token (CSRF protection). A request without it is refused.
* Log in and password recovery are throttled: 5 failed attempts for the same username from the same address lock that pair for 15 minutes. A failed log in never says whether the username exists.
* The session cookie is HttpOnly and SameSite=Lax (and Secure on https), and a fresh session id is issued at log in and log out.
* The admin password in *config.php* can be a password hash — create one in **Dashboard → Security**. A plain-text password still works, but the dashboard warns you until you replace it.
* The supplied *.htaccess* also blocks copies of the configuration file, such as *config.php.bak*.

----

## Searching and Missing Pages

### The search box

Start typing in the search box in the header and a dropdown shows up to **five** likely matches. Each row shows the **first image found on that page** (the infobox picture if the page has one, otherwise the first picture in the body — or a letter tile if the page has no image), a divider, and then the **page name**, like this: *[image] | Page Name*. Click a row, or use the arrow keys and press Enter, to open that page. Press Esc to close the dropdown.

Matches are ranked so the best come first: an exact page name, then names that start with what you typed, then names with a word that starts with it, then names that contain it. Near-misses are tolerated (a small typo can still find the right page), and pages whose text contains your words come last. If you press Enter without choosing a row, you are still taken straight to the title you typed, exactly as before.

The last row of the dropdown, **See all results**, opens [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch). That page shows up to 50 results, each with a thumbnail and a short text snippet, and offers a link to create the page if nothing with that exact name exists yet. You can also open it directly with *index.php?title=Special:Search&q=your words*.

### Special:Missing (red links)

[Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing) lists every **red link** on the wiki — every [[Page Name]] that appears in some page but whose target page doesn't exist yet. Each missing page is shown as [[Page Name]], with a **Linked from** column naming every page that links to it. Click a missing page and it opens directly, ready to be created. Once the page is created, it drops off the list on its own.

It is a ready-made to-do list for growing the wiki. It ignores category and special-page links, links shown inside code blocks, and links into spaces that don't exist (an admin must create those first). It is also listed in the Special pages directory.

## Frequently Asked Questions

* **Do I need a database?** No. Everything is stored in plain text and JSON files.
* **Can I move my wiki to another server?** Yes — copy the whole folder, update *$Server* in *config.php* to the new address, and you are done.
* **Can I change how the wiki looks without touching PHP?** Yes — skins are plain CSS files, created and switched from the Dashboard.
* **What happens to a user's pages if their account is removed?** Nothing — page content is never tied to the account being present, so removing a user never deletes what they wrote.
* **What if I delete an image by mistake?** As admin, open the "Deleted" tab in [All Images](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages) and restore it — deleted images go to a trash folder rather than being erased immediately.
* **Can a regular user undo someone else's edit?** AWFLWIKIWORLD does not keep a version history for every user — only the admin's own latest edit to each page is tracked, in the Dashboard's History section, precisely so an admin can always restore their own last version.

## References

## See Also

* [Special:AllPages](https://aedtpworld.com/wikiworld/index.php/Special%3AAllPages)
* [Special:Search](https://aedtpworld.com/wikiworld/index.php/Special%3ASearch)
* [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing)
* [Special:Register](https://aedtpworld.com/wikiworld/index.php/Special%3ARegister)
* [Special:Login](https://aedtpworld.com/wikiworld/index.php/Special%3ALogin)
* [Special:Upload](https://aedtpworld.com/wikiworld/index.php/Special%3AUpload)
* [AWFLWIKITEXT formatting reference](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/AWFLWIKITEXT)

[^1]: Like this one. Notes are numbered in the order they appear and listed under the References heading at the bottom of this page, each with a ^ link back to the spot it was cited from.


# AWFLWIKITEXT

| Field | Value |
| --- | --- |
| title | AWFLWIKITEXT |
| Type | Lightweight markup language |
| Used by | AWFLWIKIWORLD |
| Formats | 30 |
| Reference article | AWFLWIKIWORLD |



**AWFLWIKITEXT** is the markup language every AWFLWIKIWORLD page is written in. This page lists every format the parser understands. Each entry shows the raw syntax first, in a code box, followed by what it looks like once rendered.

## Text styling

### Bold

Wrap text in three apostrophes to make it bold.


```
**bold text**
```


Rendered: **bold text**

### Italic

Wrap text in two apostrophes to make it italic.


```
*italic text*
```


Rendered: *italic text*

### Underline

Wrap text in two underscores to underline it.


```
<u>underlined text</u>
```


Rendered: <u>underlined text</u>

### Strikethrough

Wrap text in two tildes to strike it through — useful for showing something retired or replaced.


```
~~struck-through text~~
```


Rendered: ~~struck-through text~~

### Inline code

Wrap text in backticks to show it as inline code — file names, commands, variable names.


```
`inline code`
```


Rendered: `inline code`

### Superscript

Wrap text in two carets to raise it, for exponents and ordinals.


```
E=mc<sup>2</sup>
```


Rendered: E=mc<sup>2</sup>

### Subscript

Wrap text in two commas to lower it, for chemical formulas and indices.


```
H<sub>2</sub>O
```


Rendered: H<sub>2</sub>O

### Highlight

Wrap text in two percent signs to highlight it, like a marker pen.


```
==highlighted text==
```


Rendered: ==highlighted text==

## Structure

### Headings

Surround a line with one to six equals signs. One equals sign makes the largest heading (level 1); more equals signs make progressively smaller, deeper headings, down to level 6.


```
# Level 1
## Level 2
### Level 3
#### Level 4
##### Level 5
###### Level 6
```


Rendered:

##### Level 5
###### Level 6

(Levels 2, 3 and 4 are already used as real section headings throughout this very page, and level 1 is demonstrated below, so scroll up — or down — to see them in action.)

# Level 1 example

This line above is a real, live level-1 heading, written as = Level 1 example =.

### Paragraphs

Leave a blank line between blocks of text. Each block becomes its own paragraph automatically — there's no special paragraph marker to type.

### Bulleted list

Start each line with an asterisk and a space.


```
* First item
* Second item
* Third item
```


Rendered:

* First item
* Second item
* Third item

### Numbered list

Start each line with a hash and a space.


```
# First step
# Second step
# Third step
```


Rendered:

1. First step
2. Second step
3. Third step

### Nested lists

Repeat the marker to go one level deeper: two asterisks make a sub-bullet, two hashes make a sub-step, and so on for as many levels as you need. The two kinds can be mixed — an asterisk followed by a hash puts a numbered list inside a bullet. A blank line, or any line that does not start with a marker, ends the list. In the editor, the arrow buttons next to **List** indent and outdent the selected items.


```
* Fruit
** Citrus
*** Orange
*** Lemon
** Berries
* Vegetables
# Prepare
## Wash
## Chop
# Cook
#* Medium heat
#* Stir often
```


Rendered:

* Fruit
  * Citrus
    * Orange
    * Lemon
  * Berries
* Vegetables

1. Prepare
   1. Wash
   2. Chop
2. Cook
   * Medium heat
   * Stir often

### Blockquote

Start each line of the quote with a "greater than" sign and a space.


```
> Everything is a plain text file, waiting to be read.
```


Rendered:

> Everything is a plain text file, waiting to be read.

### Horizontal rule

A line made up of four or more dashes, on its own, draws a divider.


```
----
```


Rendered:

----

### Code block

Wrap one or more lines in triple curly braces to show preformatted, monospaced text exactly as typed — line breaks and spacing are preserved, and no other formatting is applied inside it.

{{{
function hello() {
    return "hi";
}
}}}

Rendered:


```
function hello() {
    return "hi";
}
```


## Tables

### Tables

A table starts with {| on a line of its own and ends with |} on a line of its own. Inside, |- starts a new row and | starts a cell. Several cells can share one line when separated by ||. Cell text can use all the usual inline formatting — bold, italics, links, footnotes — and a cell can hold a nested list or several lines. Everything in a cell is escaped, so raw HTML typed into a table is shown as text, never run. The editor's **Table** button inserts a ready-made table.


```
{| class="wikitable"
|-
| Accra || Greater Accra
|-
| Kumasi || Ashanti
|}
```


Rendered:



|  |  |
| --- | --- |
| Accra | Greater Accra |
| Kumasi | Ashanti |



### Header rows

Start a cell with ! instead of | to make it a header cell, and separate several header cells on one line with !!. Header rows at the top of the table are grouped as its head and drawn in bold. A header cell at the start of a later row becomes a row heading.


```
{| class="wikitable"
|-
! City !! Region !! Founded
|-
! Accra
| Greater Accra || 15th century
|-
! Kumasi
| Ashanti || 1680s
|}
```


Rendered:



| City | Region | Founded |
| --- | --- | --- |
| **Accra** | Greater Accra | 15th century |
| **Kumasi** | Ashanti | 1680s |



### Table captions

A line starting with |+, written before the first row, gives the table a caption shown above it.


```
{| class="wikitable"
|+ Largest cities of Ghana
|-
! City !! Region
|-
| Accra || Greater Accra
|}
```


Rendered:



**Largest cities of Ghana**

| City | Region |
| --- | --- |
| Accra | Greater Accra |



### Sortable tables

Add class="sortable" to the opening line and readers can click any header cell to sort the table by that column; click again to reverse. Numbers (1,234, 12.5%, $9) sort as numbers and everything else sorts alphabetically. To sort a cell by a different value than the one shown, give it a data-sort-value. A sortable table with no header row uses its first row as the header. Other classes you can combine with it are striped (shaded alternate rows) and center (centres the table); any other class name is ignored. A table that uses rowspan or colspan in its body is shown but not sortable.


```
{| class="wikitable sortable striped"
|+ Population (2021 census)
! City !! Population
|-
| Accra || 2,514,000
|-
| Kumasi || 3,348,000
|-
| Tamale || 950,000
|}
```


Rendered:



**Population (2021 census)**

| City | Population |
| --- | --- |
| Accra | 2,514,000 |
| Kumasi | 3,348,000 |
| Tamale | 950,000 |



### Cell spans and alignment

Put attributes in front of a cell's text, followed by a single vertical bar: colspan and rowspan (a number) stretch a cell across columns or rows, align (left, center or right) aligns its text, and data-sort-value sets its sort key. Only these four attributes are accepted — styles, scripts and any other attribute are dropped.


```
{| class="wikitable"
! colspan="2" | Team
|-
| rowspan="2" | Black Stars
| Football
|-
| align="right" | Ghana
|}
```


Rendered:



| Team |  |
| --- | --- |
| Black Stars | Football |
|  | Ghana |



In a Markdown download a table becomes a pipe table (spans leave empty cells and sorting is dropped); in a PDF download each row becomes one line of text with the cells separated by a vertical bar.

## Links and media

### Internal link

Link to another page on this wiki with double square brackets. Add a vertical bar and custom text to change the visible label.


```
[Page Name](https://aedtpworld.com/wikiworld/index.php/Page_Name)
[words to display instead](https://aedtpworld.com/wikiworld/index.php/Page_Name)
```


Rendered: [AWFLWIKIWORLD](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/AWFLWIKIWORLD) — and a link to a page that doesn't exist yet, [like this one](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/Some_New_Page), is still shown, just styled differently, so clicking it lets you create that page. Every such red link on the wiki is gathered in one place, [Special:Missing](https://aedtpworld.com/wikiworld/index.php/Special%3AMissing).

To link into a namespace, include it in the title: [[Namespace/Page Name]]. If the namespace doesn't already exist, following the link sends you home instead of creating it — only an admin can create a namespace, from the dashboard.

### External link

Single square brackets, a full URL, a space, then the link text.


```
[link text](https://example.com)
```


Rendered: [Wiki, on Wikipedia](https://en.wikipedia.org/wiki/Wiki)

### Bare images

Any line that is just an image filename or URL ending in `.jpg`, `.jpeg`, `.png`, `.gif`, `.svg`, or `.webp` is automatically turned into a picture, shown at 250 pixels wide.


```
![](https://example.com/picture.jpg)
```


Rendered:

![](https://aedtpworld.com/wikiworld/logo/aedtpworld.svg)

### Infobox

A small fact-table, usually shown at the top of an article. `title`, `image` and `caption` are each allowed once and always appear in that order; every other `key=value` line becomes its own row, in the order you wrote it.


| Field | Value |
| --- | --- |
| title | Subject Name |
| image | some-picture.jpg |
| caption | A short caption |
| Field one | Value one |
| Field two | Value two |



Rendered:


| Field | Value |
| --- | --- |
| title | Example Subject |
| Field one | Value one |
| Field two | Value two |



## Citations and navigation

### Footnote

Wrap a source, or any short comment, in a ref tag pair directly after the text it backs up. The tag disappears from the text and leaves a small numbered marker in its place; the note itself is collected in the reference list (see below). A note can use the same formatting as a paragraph — bold, italics, internal links — and an external link written as [url label].


```
The tower opened in 1889.<ref>Official site, [Tower history](https://example.com/tower).</ref>
```


Rendered: The tower opened in 1889.[^1]

Notes are numbered in the order they first appear on the page. Click a marker to jump down to its note, and the ^ at the start of the note to jump back.

### Reusing a footnote

Give a note a name and you can cite it again anywhere else without repeating the text: write the full note once, then use the same name in a self-closing tag. Every marker for that note shows the same number, and the note in the list gets a, b, c back-links, one for each place it was cited.


```
First claim.<ref name="census">National census, 2021.</ref>
Second claim.<ref name="census"/>
```


Rendered: First claim.[^2] Second claim.[^2]

If a name is used but the full note is never written, its entry in the list says that the reference text is missing.

### Reference list

The numbered list of notes is printed where you ask for it. Either add a section headed References, or put a references tag on a line of its own wherever the list should go (for example inside a section with a different title, like Sources or Notes).


```
## References
```



```
<references/>
```


If a page has footnotes but neither of these, a References section is added at the bottom automatically so the notes are never lost. The list is printed once; when both a References heading and a references tag are present, the tag decides where it goes. The editor toolbar's **Cite** button inserts a footnote at your cursor and its **References** button adds the section at the bottom of the page.

Footnotes written inside an escaped span (a nowiki tag pair) or a code block are shown as typed and are not counted.

### Heading anchors

Every heading automatically gets an anchor: its text in lower case, with spaces and punctuation turned into hyphens. The heading "Early life" can be linked to as #early-life at the end of the page's address. If two headings would get the same anchor, later ones get -2, -3 and so on added, so every anchor on a page is unique.

### Table of contents

A page with four or more headings gets a numbered, collapsible "Contents" box just above its first heading — this page has one. Click "Contents" to fold it away. To leave the box out of a page, write the following anywhere on it:


```
<u>NOTOC</u>
```


## Escaping raw syntax

Sometimes you want to *show* AWFLWIKITEXT syntax as plain text instead of having it rendered — exactly like every syntax box on this page. Wrap the text in a `nowiki` tag pair (an opening tag, then the text, then a matching closing tag with a forward slash) and everything in between is shown exactly as typed, with no other formatting applied to it. This is precisely how the raw-syntax examples throughout this page avoid turning into real bold text, real headings, real links, and so on. The same goes for footnotes and the table-of-contents switch: a ref tag or __NOTOC__ inside an escaped span is left exactly as typed.

## Quick reference

* Bold: '''text'''
* Italic: ''text''
* Underline: __text__
* Strikethrough: ~~text~~
* Inline code: `text`
* Superscript: ^^text^^
* Subscript: ,,text,,
* Highlight: %%text%%
* Heading: == text == (1 to 6 equals signs)
* Bulleted list: * text
* Numbered list: # text
* Nested list: ** text, ## text, *# text (repeat the marker for each level)
* Blockquote: > text
* Horizontal rule: ----
* Code block: {{{ text }}}
* Table: {| ... |}, with |- for a new row and | or || for cells
* Header cell: ! text or ! one !! two
* Table caption: |+ text
* Sortable table: {| class="sortable"
* Cell attributes: colspan, rowspan, align, data-sort-value, then | and the text
* Internal link: [[Page]] or [[Page|Label]]
* External link: [https://url Label]
* Bare image: a filename or URL ending in an image extension, on its own
* Infobox: 
* Footnote: <ref>text or [https://url label]</ref>
* Reused footnote: <ref name="x">text</ref> once, then <ref name="x"/>
* Reference list: == References == or <references/>
* Heading anchor: automatic, e.g. #early-life
* Table of contents: automatic with 4 or more headings; __NOTOC__ turns it off
* Escape/raw text: wrap in a `nowiki` tag pair

Every format above is also used live, for real, throughout the [AWFLWIKIWORLD](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/AWFLWIKIWORLD) article — this page exists to explain each one on its own.

## References

## See also

* [AWFLWIKIWORLD](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD/AWFLWIKIWORLD)
* [Main Page](https://aedtpworld.com/wikiworld/index.php/Main_Page)

[^1]: Official site, [Wiki, on Wikipedia](https://en.wikipedia.org/wiki/Wiki).
[^2]: National census, 2021.

# AEDTP WORLD

| Field | Value |
| --- | --- |
| title | AEDTP WORLD |
| image | ![](https://aedtpworld.com/wikiworld/logo/aedtpworld.svg) |
| caption | AEDTP WORLD - Discover And Connect |
| Developer | AEDTP WORLD |
| Founder | Emmanuel Deliver Amable |
| Founded | April 14, 2021 |
| Launched | April 14, 2024 |
| Live | 2026-08-11 11:47:09 |
| Website | https://aedtpworld.com |
| Email | aedtpworld@aedtpworld.com |
| License | AEDTP WORLD FREE LICENSE (AWFL) |
| Headquarters | Ghana |
| Type | Technology company and conglomerate |



**AEDTP WORLD** is a technology company and conglomerate, also recognized under its condensed name form, its free license identity, its universe identity, and its discover and connect motto.

## History
AEDTP WORLD was founded on April 14, 2021 by **Emmanuel Deliver Amable** and launched globally on April 14, 2024. AEDTP WORLD first went live officially on 2026-08-11 11:47:09.

## Founder
Founder: Emmanuel Deliver Amable, born on April 14, 2008. His birth is considered as both April 13th and April 14th, meaning born at Sunday 11:59 PM and Monday 12:00 AM.

## Official Contacts
* Official website: [aedtpworld.com](https://aedtpworld.com)
* Support email: aedtpworld@aedtpworld.com and any address at @aedtpworld.com
* Forever email: aedtpworld@gmail.com

## Ecosystem
AEDTP WORLD operates under the **AEDTP WORLD FREE LICENSE (AWFL)** as a free, open digital ecosystem with tagline "Discover And Connect". Knowledge is unlimited.

As of November 3, 2026, AEDTP WORLD has 47 directories and has distributed for 40+ artists.

## FACTS
* AEDTP WORLD creates GKP
* AEDTP WORLD creates Wikidata
* AEDTP WORLD founded [AWFLWIKIWORLD](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIWORLD) similar to Wikipedia
* AEDTP WORLD founded [AWFLWIKIDATAWORLD](https://aedtpworld.com/wikiworld/index.php/AWFLWIKIDATAWORLD) similar to Wikidata org
* AEDTP WORLD founded [LyricsWorld](https://aedtpworld.com/wikiworld/index.php/LyricsWorld) similar to Genius Lyrics
* AEDTP WORLD founded [Blue Melody](https://aedtpworld.com/wikiworld/index.php/Blue_Melody) similar to combined Apple Music, Spotify, Audiomack and YouTube Music
* AEDTP WORLD founded Advertisements platform for multi advertising
* AEDTP WORLD founded [PROJECT BOOT](https://aedtpworld.com/wikiworld/index.php/PROJECT_BOOT) Dev for open developer platform
* AEDTP WORLD founded [BIZIMI](https://aedtpworld.com/wikiworld/index.php/BIZIMI) similar to YouTube
* AEDTP WORLD founded [Chataropica](https://aedtpworld.com/wikiworld/index.php/Chataropica) similar to WhatsApp Facebook chat
* AEDTP WORLD founded AI platforms
* AEDTP WORLD founded so many
* All directories are founded by AEDTP WORLD

## GodKidzLegacy
AEDTP WORLD founded **GodKidzLegacy** with the intent to be the central universe for all charity and child support database where Q1 can be 1, and it means each Q is an organization for child support. It has Open API for developers to use in distributing the data so others can find support easily.

## AI Collaboration
* Meta AI
* Claude
* ChatGPT
* DeepSeek
* Cayla AI
* AEDTP WORLD AI

## Directories
* /about/
* /apps/
* /wikiworld/
* /wikidata/
* /lyrics/
* /spotifyworld/
* /distribution/
* /music/
* /business/
* /developer/
* /social/
* /kids/
* /ai/
* /chat/
* /images/
* /bible/
* /stats/
* /dictionary/
* /awfl/biography/
* /awfl/news/
* /license/
* /tools/
* /dev/
* /lite/
* /wiki/
* /searchengine/
* /awflwiki/
* /create/
* /aedtpworldwiki/
* /awfl/
* /beta/
* /meta/
* /awflmeta/
* /register/
* /license/copyright/
* /awfl/artists/
* /awfl/business/
* /awfl/dictionary/
* /awfl/education/
* /awfl/music/
* /business/verify/
* /ads/
* /help/
* /advertise/
* /pad/

## License

Licensed under **AEDTP WORLD FREE LICENSE (AWFL)**. © AEDTP WORLD.

## Founder

Founded by **Emmanuel Deliver Amable**.

## External Links

* [Official Website](https://aedtpworld.com/)
* [Facebook - AEDTP WORLD](https://facebook.com/aedtpworld)
* [Instagram - AEDTP WORLD](https://instagram.com/aedtpworld)
* [MusicBrainz - AEDTP WORLD](https://musicbrainz.org/label/f4ef194f-4b43-4d98-8798-197181f23ecf)
* [YouTube - AEDTP WORLD](https://youtube.com/@aedtpworld)
* [Crunchbase - AEDTP WORLD](https://www.crunchbase.com/organization/aedtpworld)
* [SoundBetter - AEDTP WORLD](https://soundbetter.com/profiles/636065-aedtp-world)
* [F6S - AEDTP WORLD](https://www.f6s.com/aedtpworld)
* [Wellfound - AEDTP WORLD](https://wellfound.com/company/aedtpworld)
* [TikTok - AEDTP WORLD](https://www.tiktok.com/@aedtpworld)
* [Google Knowledge Panel](https://www.google.com/search?kgmid=/g/11y6cc89zn)
* [AEDTP WORLD on WIKIDATA](http://wikidata.org/wiki/Q141800291)


## External Links
* [Official Website](https://aedtpworld.com/) [info]
* [GitHub](https://github.com/aedtpworldteam/aedtpworld) [info]

[Category:AEDTP WORLD](https://aedtpworld.com/wikiworld/index.php/Category%3AAEDTP_WORLD)
[Category:AWFL](https://aedtpworld.com/wikiworld/index.php/Category%3AAWFL)
[Category:Business](https://aedtpworld.com/wikiworld/index.php/Category%3ABusiness)
[Category:Technology](https://aedtpworld.com/wikiworld/index.php/Category%3ATechnology)

[AEDTP WORLD Database](https://aedtpworld.com/wikidata/Q6)




/AWFLWIKIWORLD

/AWFLWIKIWORLD
