# Description

CafePress Api SDK for PHP

# Requirements

- PHP 5.2 or later
- SimpleXML
- CURL
- CafePress API developer key



# TLDR Usage example

    include "init.php";
    $products = CafePressApi::Products([cafepress api developer key])->get([search string], [options]);
    foreach($products as $k => $v) {
        var_export($v);
    }




# Configuration
rename the *config.yml.sample* to config.yml and specify the appkey value


    appkey: [you're cafepress api developer key]


by default the appkey value in the config.yml will be used to connect to cafepress api,
however you can override this value in the code by passing the appkey value, example


    include "init.php";
    $products = CafePressApi::Products([appkey key here])->get([search string], [options]);
    foreach($products as $k => $v) {
        var_export($v);
    }



## CafePressApi::Products.get

Will search the cafepress product names that matches the search string

### Syntax

    CafePressApi::Products()->get(search[, options]);


### Arguments
- search - (_String_ or _Array of String_) The product name search criteria,
- options - (_Array_) An array with options for the method, see below

### Options
- pageNumber - (_Numeric_ : defaults to 1) the page for the results, default = 1
- resultsPerPage - (_Numeric_ : defaults to 20 ) the number of results per page
- maxProductsPerDesign (_Numeric_ : defaults to 1) the maximum number of products per design
- merchandiseIds - (_Numeric_ : default to null) additional criteria to limit the results per mechandise id

#### Example

    include "init.php";

    $products = CafePressApi::Products()->get('foobar', array('pageNumber' => 1));
    foreach($products as $k => $v) {
        var_export($v);
    }

    $products = CafePressApi::Products()->get(array('foobar', 'barfoo'), array('pageNumber' => 2));
    foreach($products as $k => $v) {
        var_export($v);
    }



## CafePressApi::Products.get_by_productids

Will search the cafepress product by product ids

### Syntax

    CafePressApi::Products()->get_by_productids(search);

### Arguments
- search - (_Numeric_ or _Array of Number_) The product ids to search

#### Example

    include "init.php";

    $products = CafePressApi::Products()->get_by_productids(1234);
    foreach($products as $k => $v) {
        var_export($v);
    }

    $products = CafePressApi::Products()->get_by_productids(array(1234, 4567, 7789));
    foreach($products as $k => $v) {
        var_export($v);
    }



## CafePressApi::Products.get_by_designids

Returns all cafepress products by design id

### Syntax

    CafePressApi::Products()->get_by_designids(design_id);

### Arguments
- design_id - (_Numeric_ or _Array of Number_) The design ids to search

#### Example

    include "init.php";

    $products = CafePressApi::Products()->get_by_designids(1234);
    foreach($products as $k => $v) {
        var_export($v);
    }

    $products = CafePressApi::Products()->get_by_designids(array(1234, 4567, 7789));
    foreach($products as $k => $v) {
        var_export($v);
    }




## CafePressApi::Products.get_by_storeid

Returns all cafepress products by store id

### Syntax

    CafePressApi::Products()->get_by_storeid(store_id[, options]);

### Arguments
- store_id - (_String_) A valid cafepress store id
- options - (_Array_) An array with options for the method, see below

### Options
- pageNumber - (_Numeric_ : defaults to 1) the page for the results, default = 1
- resultsPerPage - (_Numeric_ : defaults to 20 ) the number of results per page

#### Example

    include "init.php";

    $products = CafePressApi::Products()->get_by_storeid('myfoobarstore', array('pageNumber' => 1, 'resultsPerPage' => 10 ));
    foreach($products as $k => $v) {
        var_export($v);
    }

## CafePressApi::Products.is_empty

Returns true if there's results from the last api call

### Syntax

    CafePressApi::Products()->is_empty();