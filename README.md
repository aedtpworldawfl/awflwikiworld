# awflwikiworld
AWFLWIKIWORLD is a free encyclopedia, knowledge base for Ghana, Africa and the world. Anyone can read. Anyone with an account can edit. AWFLWIKIWORLD is a lightweight, database-free, MediaWiki-style wiki engine built in plain PHP. A free, open wiki world by AEDTP WORLD. Discover And Connect.
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
* **index.php** — the main controller. Almost every page you visit — viewing an article, logging in, editing, saving, downloading — passes through this one file.
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
* *assets/site.css*, *assets/site.js* — the default look and the editor toolbar script. These never change; skins layer on top of them.
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
2. Set **$Username** and **$Password** to your admin login.
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
* Use the search box in the header to jump straight to a page by title.
* Browse [Special:AllPages](http:/index.php?title=Special%3AAllPages) to see every page that exists.
* Browse the image gallery at *allimages/index.php* — see every uploaded picture, in a 7-column grid or a sortable detail table, and use the **Copy link** / **Copy name** buttons on any image.
* Browse the account directory at *allusers/index.php*.
* Download any page as a **.txt** file (the raw AWFLWIKITEXT) or as a self-contained **.html** file (styled the same as the live page, so it looks right even opened offline).
* Print any page (which also lets you "print to PDF" from the browser's print dialog).

Visitors cannot create pages, edit pages, or upload images — those require an account.

## Using AWFLWIKIWORLD as a Registered User

### Creating an account

Go to [Special:Register](http:/index.php?title=Special%3ARegister) and fill in a username, display name, email, password, and a security question and answer (used later for password recovery). Usernames must be 3–32 characters (letters, numbers, *-*, *_*, *.*), and the name **admin** is reserved. Once registered, log in at [Special:Login](http:/index.php?title=Special%3ALogin).

### Recovering a forgotten password

Open *users/recover.php*. You will be asked for your username, then your registration email and your security answer. If everything matches, you are immediately allowed to set a brand-new password — no email link required. Passwords and security answers are never stored in plain text; only their hashes are kept, so recovery always works by re-checking your answer, never by revealing the old password.

### Creating and editing pages

To create a page, search for or navigate to its title and click **Create**. To edit an existing page, click **Edit**. Both open the same editor:

* A **toolbar** with **B** (bold), **I** (italic), **U** (underline), **P** (paragraph break), **List** (bulleted list) and **Infobox** (inserts a ready-made infobox template) — each wraps or inserts AWFLWIKITEXT around your current text selection.
* A **source editor**, the plain text box where the raw AWFLWIKITEXT lives.
* A **visual editor (preview)** tab, which sends your current text to the server and shows exactly how it will render, without saving anything yet.

Click **Save page** to publish your changes immediately.

**Note on protected pages:** if an admin has protected a page, only an admin can edit it — the Edit link disappears (or is blocked server-side even if requested directly) for everyone else.

### AWFLWIKITEXT — the markup language

AWFLWIKITEXT is a small, MediaWiki-inspired markup language. All of it can be typed by hand or inserted with the editor toolbar:

* **Bold**: wrap text in three apostrophes, like **this**.
* *Italic*: wrap text in two apostrophes, like *this*.
* Underline: wrap text in two underscores, like <u>this</u>.
* Headings: surround a line with one to six equals signs, like = A Heading = for level 1 or == A Heading == for level 2 (more equals signs make progressively smaller headings, down to level 6).
* Bulleted list: start a line with an asterisk and a space, like * An item.
* Numbered list: start a line with a hash and a space, like # An item.
* Paragraphs: leave a blank line between blocks of text — each block becomes its own paragraph automatically.
* Internal link: [Page Name](http:/index.php?title=Page_Name), or [words to display instead](http:/index.php?title=Page_Name). A link to a page that does not exist yet is still shown, in a different style, so you can click it to create that page.
* External link: [link text](http://example.com).
* Bare images: any line that is just an image filename or URL ending in *.jpg*, *.jpeg*, *.png*, *.gif*, *.svg* or *.webp* is automatically rendered as a picture, shown at 250 pixels wide.
* Strikethrough: wrap text in two tildes, like ~~this~~ — for example, a ~~retired~~ feature name.
* Inline code: wrap text in backticks, like `this` — for example, `config.php`.
* Superscript: wrap text in two carets, like <sup>this</sup> — for example, E=mc<sup>2</sup>.
* Subscript: wrap text in two commas, like <sub>this</sub> — for example, H<sub>2</sub>O.
* Highlight: wrap text in two percent signs, like ==this== — for example, a ==highlighted== note.
* Blockquote: start a line with >  — for example, a quoted line stands apart from the surrounding paragraph.
* Horizontal rule: a line of four or more dashes on its own, like ----, draws a divider between sections.
* Code block: wrap one or more lines in triple curly braces, like 
```
 your code here 
```
, to show preformatted, monospaced text.

This article uses every one of these formats at least once. For the full reference, with a worked example of each format side by side with its raw syntax, see [AWFLWIKITEXT](http:/index.php?title=AWFLWIKIWORLD/AWFLWIKITEXT).

### Infoboxes

An infobox is a small fact-table, usually shown floated at the top right of an article — exactly like the one at the top of this page. Write one like this:


| Field | Value |
| --- | --- |
| title | Subject Name |
| image | ![](images/some-picture.jpg) |
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

If the admin has enabled uploads, any logged-in user can add a picture at [Special:Upload](http:/index.php?title=Special%3AUpload). Only **.jpg** and **.png** files are accepted. Once uploaded, an image can be referenced in any page (bare, or inside an infobox) just by its filename, for example *![](images/mypicture.jpg)*.

### Downloading and printing

Every page offers:

* **Download .txt** — the raw AWFLWIKITEXT, exactly as stored.
* **Download .html** — a single, self-contained HTML file with the page's styling embedded in it, so it looks right even with no internet connection.
* **Print / PDF** — opens your browser's print dialog, which on most browsers can also "print" straight to a PDF file.

## Using AWFLWIKIWORLD as the Administrator

Log in with the **$Username** / **$Password** set in *config.php*. The admin account is not a row in *users/users.json* — it is defined directly in the configuration file and always has full control.

### The Dashboard

Open the **Dashboard** link in the navigation bar (*dashboard/index.php*). It has four sections:

* **Skins** — pick which skin is active site-wide, or create a brand-new skin by typing its CSS. The default **vector2022** skin can be selected but never edited or deleted; create a new skin instead if you want to change the look.
* **Colors** — a small palette for the site's core colors (header background, header text, buttons, button text, wiki page text, and links). Pick a color visually or type a hex code directly; the change applies across the whole wiki immediately, on top of whichever skin is active.
* **Pages** — a list of every page, with a delete button per page and a "Delete all pages" button. This is also where you can trigger the sitemap to regenerate on demand.
* **Images** — every uploaded image, with a delete button per image.

### Managing users

The [All users](http:/index.php?title=Special%3AAllPages) directory lists every account except the admin's own. Anyone can view it; only the admin sees extra controls per row to:

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

----

## Frequently Asked Questions

* **Do I need a database?** No. Everything is stored in plain text and JSON files.
* **Can I move my wiki to another server?** Yes — copy the whole folder, update *$Server* in *config.php* to the new address, and you are done.
* **Can I change how the wiki looks without touching PHP?** Yes — skins are plain CSS files, created and switched from the Dashboard.
* **What happens to a user's pages if their account is removed?** Nothing — page content is never tied to the account being present, so removing a user never deletes what they wrote.
* **What if I delete an image by mistake?** As admin, open the "Deleted" tab in [All Images](http:/index.php?title=Special%3AAllPages) and restore it — deleted images go to a trash folder rather than being erased immediately.
* **Can a regular user undo someone else's edit?** AWFLWIKIWORLD does not keep a version history for every user — only the admin's own latest edit to each page is tracked, in the Dashboard's History section, precisely so an admin can always restore their own last version.

## See Also

* [Special:AllPages](http:/index.php?title=Special%3AAllPages)
* [Special:Register](http:/index.php?title=Special%3ARegister)
* [Special:Login](http:/index.php?title=Special%3ALogin)
* [Special:Upload](http:/index.php?title=Special%3AUpload)
* [AWFLWIKITEXT formatting reference](http:/index.php?title=AWFLWIKIWORLD/AWFLWIKITEXT)

# AWFLWIKITEXT

| Field | Value |
| --- | --- |
| title | AWFLWIKITEXT |
| Type | Lightweight markup language |
| Used by | AWFLWIKIWORLD |
| Formats | 19 |
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
1. First step
2. Second step
3. Third step
```


Rendered:

1. First step
2. Second step
3. Third step

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


```
function hello() {
    return "hi";
}
```


Rendered:


```
function hello() {
    return "hi";
}
```


## Links and media

### Internal link

Link to another page on this wiki with double square brackets. Add a vertical bar and custom text to change the visible label.


```
[Page Name](http:/index.php?title=Page_Name)
[words to display instead](http:/index.php?title=Page_Name)
```


Rendered: [AWFLWIKIWORLD](http:/index.php?title=AWFLWIKIWORLD/AWFLWIKIWORLD) — and a link to a page that doesn't exist yet, [like this one](http:/index.php?title=AWFLWIKIWORLD/Some_New_Page), is still shown, just styled differently, so clicking it lets you create that page.

To link into a namespace, include it in the title: [Namespace/Page Name](http:/index.php?title=Namespace/Page_Name). If the namespace doesn't already exist, following the link sends you home instead of creating it — only an admin can create a namespace, from the dashboard.

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
| image | ![](images/some-picture.jpg) |
| caption | A short caption |
| Field one | Value one |
| Field two | Value two |



Rendered:


| Field | Value |
| --- | --- |
| title | Example Subject |
| Field one | Value one |
| Field two | Value two |



## Escaping raw syntax

Sometimes you want to *show* AWFLWIKITEXT syntax as plain text instead of having it rendered — exactly like every syntax box on this page. Wrap the text in a `nowiki` tag pair (an opening tag, then the text, then a matching closing tag with a forward slash) and everything in between is shown exactly as typed, with no other formatting applied to it. This is precisely how the raw-syntax examples throughout this page avoid turning into real bold text, real headings, real links, and so on.

## Quick reference

* Bold: **text**
* Italic: *text*
* Underline: <u>text</u>
* Strikethrough: ~~text~~
* Inline code: `text`
* Superscript: <sup>text</sup>
* Subscript: <sub>text</sub>
* Highlight: ==text==
* Heading: == text == (1 to 6 equals signs)
* Bulleted list: * text
* Numbered list: # text
* Blockquote: > text
* Horizontal rule: ----
* Code block: 
```
 text 
```

* Internal link: [Page](http:/index.php?title=Page) or [Label](http:/index.php?title=Page)
* External link: [Label](https://url)
* Bare image: a filename or URL ending in an image extension, on its own
* Infobox: 
* Escape/raw text: wrap in a `nowiki` tag pair

Every format above is also used live, for real, throughout the [AWFLWIKIWORLD](http:/index.php?title=AWFLWIKIWORLD/AWFLWIKIWORLD) article — this page exists to explain each one on its own.

## See also

* [AWFLWIKIWORLD](http:/index.php?title=AWFLWIKIWORLD/AWFLWIKIWORLD)
* [Main Page](http:/index.php?title=Main_Page)


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
* **index.php** — the main controller. Almost every page you visit — viewing an article, logging in, editing, saving, downloading — passes through this one file.
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
* *assets/site.css*, *assets/site.js* — the default look and the editor toolbar script. These never change; skins layer on top of them.
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
5. Set **$Username** and **$Password** to your admin login.
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
* Use the search box in the header to jump straight to a page by title.
* Browse [Special:AllPages](http:/index.php?title=Special%3AAllPages) to see every page that exists.
* Browse the image gallery at *allimages/index.php* — see every uploaded picture, in a 7-column grid or a sortable detail table, and use the **Copy link** / **Copy name** buttons on any image.
* Browse the account directory at *allusers/index.php*.
* Download any page as a **.txt** file (the raw AWFLWIKITEXT) or as a self-contained **.html** file (styled the same as the live page, so it looks right even opened offline).
* Print any page (which also lets you "print to PDF" from the browser's print dialog).

Visitors cannot create pages, edit pages, or upload images — those require an account.

## Using AWFLWIKIWORLD as a Registered User

### Creating an account

Go to [Special:Register](http:/index.php?title=Special%3ARegister) and fill in a username, display name, email, password, and a security question and answer (used later for password recovery). Usernames must be 3–32 characters (letters, numbers, *-*, *_*, *.*), and the name **admin** is reserved. Once registered, log in at [Special:Login](http:/index.php?title=Special%3ALogin).

### Recovering a forgotten password

Open *users/recover.php*. You will be asked for your username, then your registration email and your security answer. If everything matches, you are immediately allowed to set a brand-new password — no email link required. Passwords and security answers are never stored in plain text; only their hashes are kept, so recovery always works by re-checking your answer, never by revealing the old password.

### Creating and editing pages

To create a page, search for or navigate to its title and click **Create**. To edit an existing page, click **Edit**. Both open the same editor:

* A **toolbar** with **B** (bold), **I** (italic), **U** (underline), **P** (paragraph break), **List** (bulleted list) and **Infobox** (inserts a ready-made infobox template) — each wraps or inserts AWFLWIKITEXT around your current text selection.
* A **source editor**, the plain text box where the raw AWFLWIKITEXT lives.
* A **visual editor (preview)** tab, which sends your current text to the server and shows exactly how it will render, without saving anything yet.

Click **Save page** to publish your changes immediately.

**Note on protected pages:** if an admin has protected a page, only an admin can edit it — the Edit link disappears (or is blocked server-side even if requested directly) for everyone else.

### AWFLWIKITEXT — the markup language

AWFLWIKITEXT is a small, MediaWiki-inspired markup language. All of it can be typed by hand or inserted with the editor toolbar:

* **Bold**: wrap text in three apostrophes, like **this**.
* *Italic*: wrap text in two apostrophes, like *this*.
* Underline: wrap text in two underscores, like <u>this</u>.
* Headings: surround a line with two to six equals signs, like == A Heading == (more equals signs make a smaller heading).
* Bulleted list: start a line with an asterisk and a space, like * An item.
* Numbered list: start a line with a hash and a space, like # An item.
* Paragraphs: leave a blank line between blocks of text — each block becomes its own paragraph automatically.
* Internal link: [Page Name](http:/index.php?title=Page_Name), or [words to display instead](http:/index.php?title=Page_Name). A link to a page that does not exist yet is still shown, in a different style, so you can click it to create that page.
* External link: [link text](http://example.com).
* Bare images: any line that is just an image filename or URL ending in *.jpg*, *.jpeg*, *.png*, *.gif*, *.svg* or *.webp* is automatically rendered as a picture, shown at 250 pixels wide.

### Infoboxes

An infobox is a small fact-table, usually shown floated at the top right of an article — exactly like the one at the top of this page. Write one like this:


| Field | Value |
| --- | --- |
| title | Subject Name |
| image | ![](images/some-picture.jpg) |
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

If the admin has enabled uploads, any logged-in user can add a picture at [Special:Upload](http:/index.php?title=Special%3AUpload). Only **.jpg** and **.png** files are accepted. Once uploaded, an image can be referenced in any page (bare, or inside an infobox) just by its filename, for example *![](images/mypicture.jpg)*.

### Downloading and printing

Every page offers:

* **Download .txt** — the raw AWFLWIKITEXT, exactly as stored.
* **Download .html** — a single, self-contained HTML file with the page's styling embedded in it, so it looks right even with no internet connection.
* **Print / PDF** — opens your browser's print dialog, which on most browsers can also "print" straight to a PDF file.

## Using AWFLWIKIWORLD as the Administrator

Log in with the **$Username** / **$Password** set in *config.php*. The admin account is not a row in *users/users.json* — it is defined directly in the configuration file and always has full control.

### The Dashboard

Open the **Dashboard** link in the navigation bar (*dashboard/index.php*). It has four sections:

* **Skins** — pick which skin is active site-wide, or create a brand-new skin by typing its CSS. The default **vector2022** skin can be selected but never edited or deleted; create a new skin instead if you want to change the look.
* **Colors** — a small palette for the site's core colors (header background, header text, buttons, button text, wiki page text, and links). Pick a color visually or type a hex code directly; the change applies across the whole wiki immediately, on top of whichever skin is active.
* **Pages** — a list of every page, with a delete button per page and a "Delete all pages" button. This is also where you can trigger the sitemap to regenerate on demand.
* **Images** — every uploaded image, with a delete button per image.

### Managing users

The [All users](http:/index.php?title=Special%3AAllPages) directory lists every account except the admin's own. Anyone can view it; only the admin sees extra controls per row to:

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

## Frequently Asked Questions

* **Do I need a database?** No. Everything is stored in plain text and JSON files.
* **Can I move my wiki to another server?** Yes — copy the whole folder, update *$Server* in *config.php* to the new address, and you are done.
* **Can I change how the wiki looks without touching PHP?** Yes — skins are plain CSS files, created and switched from the Dashboard.
* **What happens to a user's pages if their account is removed?** Nothing — page content is never tied to the account being present, so removing a user never deletes what they wrote.
* **What if I delete an image by mistake?** As admin, open the "Deleted" tab in [All Images](http:/index.php?title=Special%3AAllPages) and restore it — deleted images go to a trash folder rather than being erased immediately.
* **Can a regular user undo someone else's edit?** AWFLWIKIWORLD does not keep a version history for every user — only the admin's own latest edit to each page is tracked, in the Dashboard's History section, precisely so an admin can always restore their own last version.

## See Also

* [Special:AllPages](http:/index.php?title=Special%3AAllPages)
* [Special:Register](http:/index.php?title=Special%3ARegister)
* [Special:Login](http:/index.php?title=Special%3ALogin)
* [Special:Upload](http:/index.php?title=Special%3AUpload)
