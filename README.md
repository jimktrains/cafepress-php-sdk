# Description

CafePress Api SDK for PHP

# Requirements

- PHP 5.2 or later
- SimpleXML
- CURL
- CafePress API developer key

# Configuration
rename the *config.yml.sample* to config.yml and specify the appkey value

```yaml
appkey: [you're cafepress api developer key]
```

# TLDR Usage example

include the init.php

```php
include "init.php";
$products = CafePressApi::Products([appkey])->get([search string], [options]);
foreach($products as $k => $v) {
    var_export($v);
}
```

## Method CafePressApi::Products.get()

Will search the cafepress product names that matches the search string

### Syntax

```php
include "init.php";
$products = CafePressApi::Products([appkey])->get(search[, options]);
```

### Arguments
- search - (_String_ or _Array of String_) The product name search criteria,
- options - (_Array_) An array with options for the method, see below

### Options
- pageNumber - (_Numeric_ : defaults to 1) the page for the results, default = 1
- resultsPerPage - (_Numeric_ : defaults to 20 ) the number of results per page
- maxProductsPerDesign (_Numeric_ : defaults to 1) the maximum number of products per design
- merchandiseIds - (_Numeric_ : default to null) additional criteria to limit the results per mechandise id

