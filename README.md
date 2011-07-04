# CafePress PHP SDK

```php
include "init.php";
$products = CafePressApi::Products([appkey])->get([search string]);
foreach($products as $k => $v) {
    var_export($v);
}
```