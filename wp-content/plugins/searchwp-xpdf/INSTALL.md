# SearchWP Xpdf Integration

Using this extension you can utilize [Xpdf](http://www.foolabs.com/xpdf/) to extract the content from your PDFs.

-----

**IMPORTANT:** Xpdf is not provided in this download.

You _must_ download Xpdf and upload it to a **non-public** (outside your Web root) location

-----

Xpdf offers binary distributions for both Windows and Linux at [http://www.foolabs.com/xpdf/download.html](http://www.foolabs.com/xpdf/download.html).

```php
<?php
	echo 'test!';
```

## Installation

Once downloaded:

1. Extract `xpdfbin-linux-3.03.tar.gz` (the version number may be different)
1. Upload the `pdftotext`
binary (found in either the `bin32` or `bin64` directory after extracting) to a **non-public** location, outside your Web root
1. Ensure you have set the proper permissions to the file


The last step is to tell SearchWP Xpdf Integration where you installed Xpdf. Add the following to your theme's `functions.php`, replacing */path/to/pdftotext* with the actual path on your server.

`function mySearchWPXpdfPath() {
  return '/path/to/pdftotext';
}

add_filter( 'searchwp_xpdf_path', 'mySearchWPXpdfPath' );`

That's it!
