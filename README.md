# TYPO3 Extension `nximageoptimizer`

[![TYPO3 V10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![TYPO3 V11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![GitHub CI status](https://github.com/netlogix/nximageoptimizer/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/netlogix/nximageoptimizer/actions)


This TYPO3 extension optimizes images (jpg, png, gif, svg) for web presentation when they are published. 
It will generate WebP versions of each rendered image and serve them to the client.

## Compatibility

The current version (4.x) of this extension has been tested in TYPO3 10 on PHP 7.4 and TYPO3 11 on PHP 7.4, 8.0, and 8.1.


## Requirements

This extension requires additional third-party tools. 
* [jpegoptim](https://github.com/tjko/jpegoptim): utility to optimize/compress JPEG files
* [optipng](http://optipng.sourceforge.net/): OptiPNG is a PNG optimizer that recompresses image files to a smaller size, without losing any information
* [pngquant](https://pngquant.org/): pngquant is a command-line utility and a library for lossy compression of PNG images
* [svgo](https://github.com/svg/svgo): Node.js tool for optimizing SVG files
* [gifsicle](https://github.com/kohler/gifsicle): Create, manipulate, and optimize GIF images and animations



You should install them using your package manager of choice.

### Ubuntu Example

The following is an example installation on Ubuntu:
```bash
sudo apt-get install jpegoptim optipng pngquant gifsicle
sudo npm install -g svgo
```

### MacOS Example (using [Homebrew](https://brew.sh/))


```bash
brew install jpegoptim
brew install optipng
brew install pngquant
brew install svgo
brew install gifsicle
```

### MacOS Example (using [MacPorts](https://www.macports.org/))

```bash
port install jpegoptim
port install optipng
port install pngquant
port install svgo
port install gifsicle
```

## Installation

Install the package via composer.

```bash
composer require netlogix/nximageoptimizer
```


Place the following in your .htaccess file and images will be replaced with WebP version.
```apache
	# Check if browser support WebP images
	# Check if WebP replacement image exists
	# Serve WebP image instead
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{DOCUMENT_ROOT}/$0.webp -f
	RewriteRule (.+)\.(jpe?g|png)$ $0.webp [T=image/webp,E=accept:1]
```

Tell every caching proxy to cache based on "accept" header
```apache
	RewriteRule (.+)\.(jpe?g|png|webp)$ - [env=POTENTIAL_WEBP_IMAGE:1]
	Header merge vary accept env=POTENTIAL_WEBP_IMAGE
```

## Troubleshooting

The Ubuntu source package for imagemagick does not declare a build dependency on libwebp-dev.
Thus imagemagick gets built without webp support.
To fix this install the webp package.
```bash
sudo apt-get install webp
```
