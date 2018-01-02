netlogix ImageOptimizer
=======================
TYPO3 CMS package that optimizes images (jpg, png, gif, svg) for web presentation when they are published.

Using jpegtran, optipng, pngquant, gifsicle and svgo for the optimizations.

### Installation

Install the package via composer.

```bash
composer require netlogix/nximageoptimizer
```

### Optimization tools

Install all the optimizers on Ubuntu:

```bash
sudo apt-get install jpegoptim
sudo apt-get install optipng
sudo apt-get install pngquant
sudo npm install -g svgo
sudo apt-get install gifsicle
```

Install the binaries on MacOS (using [Homebrew](https://brew.sh/) or [MacPorts](https://www.macports.org/)):

```bash
brew install jpegoptim
brew install optipng
brew install pngquant
brew install svgo
brew install gifsicle
```

```bash
port install jpegoptim
port install optipng
port install pngquant
port install svgo
port install gifsicle
```

### WebP:

Place the following in your .htaccess file and images will be replaced with WebP version.
```apache
	# Check if browser support WebP images
	# Check if WebP replacement image exists
	# Serve WebP image instead
	RewriteCond %{HTTP_ACCEPT} image/webp
	RewriteCond %{DOCUMENT_ROOT}/$1.webp -f
	RewriteRule (.+)\.(jpe?g|png)$ $1.webp [T=image/webp,E=accept:1]
```

The Ubuntu source package for imagemagick does not declare a build dependency on libwebp-dev.
Thus imagemagick gets built without webp support.
To fix this install the webp package.
```bash
sudo apt-get install webp
```
