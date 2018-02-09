<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc><?= $base_url ?></loc>
        <priority>0.5</priority>
        <changefreq>daily</changefreq>
    </url>
    <?php foreach ($uris as $uri): ?>
        <url>
            <loc><?= $base_url . ( $defaul_language != $uri->language_code ? $uri->language_code . '/' : '') . $uri->page_uri . $url_suffix ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
    <?php endforeach ?>
    <?php foreach ($static as $uri): ?>
        <url>
            <loc><?= $base_url . $uri ?></loc>
            <priority>0.5</priority>
            <changefreq>daily</changefreq>
        </url>
    <?php endforeach ?>
</urlset>