# Blog Post Cookie (Cache-Resistant)

A WordPress plugin that adds a cookie named 'cookie_blog' only on blog posts, resistant to various caching scenarios.

## Description

This plugin sets a cookie named 'cookie_blog' when a user visits a single blog post. The cookie is designed to work even in environments with caching plugins enabled.

## Features

- Sets a cookie only on single blog posts
- Cache-resistant implementation
- Removes the cookie on non-blog post pages
- Compatible with popular caching plugins like WP Rocket and WP Super Cache
- Compatible with Apache and LiteSpeed servers

## Installation

1. Download the plugin files from the [GitHub repository](https://github.com/sab-id/blog-post-cookie).
2. Upload the plugin files to the `/wp-content/plugins/blog-post-cookie` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage

The plugin works automatically once activated. No additional configuration is required.

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher (recommended)

## Author

Created by [sab.id](https://sab.id/)

## License

This project is licensed under the GPL v2 or later.
