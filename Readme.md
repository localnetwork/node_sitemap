# Drupal Node Sitemap 

## About the module
Generates sitemap for nodes.

## How to use
1. Download the file and upload it to /modules folder
2. Enable the module Node Sitemap
3. Configure the module /admin/config/node_sitemap/content-type-config
4. You can access the sitemap in /node/sitemap-index.xml
5. You can access the individual sitemap pages in /node/sitemaps.xml
 

## Pros of this module.
1. Works with domain access module.
2. Works with multilanguage. https://yoursite.com/fil/node/sitemap-index.xml
3. Custom Index Configuration per Content Type. 
4. Custom Priority Configuration per Content Type.
5. Doesn't need cron to generate sitemap urls. 
6. Supports pager /node/sitemaps.xml?page=0
7. Supports filter per content type /node/sitemaps.xml?type=article


## CONS of this module
1. It's not that user-friendly since this is a plain xml file unlike other popular modules like xmlsitemap, simple_sitemap.

# NEW
- Added a filter per content type in sitemap. 
