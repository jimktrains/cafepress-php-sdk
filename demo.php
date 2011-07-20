<?php

include "init.php";
/**
 * copy the config.yml.sample to config.yml and
 * provide you're cafepress developer appkey
 */
$products = CafePressApi::Products()->get('dogs');

/**
 * uncomment this code to show all available field names
 */
//var_export($products[0]);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>CafePress PHP SDK Demo Page</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <style type="text/css">
        table tr th, table tr td{
            padding: 5px 10px;
            text-align: left;
        }
    </style>
  </head>

  <body>
      <table>
          <tr>
              <th>Product Id</th>
              <th>Design Id</th>
              <th>Product Name</th>
              <th>Product Link</th>
              <th>Store Link</th>
          </tr>
          <?php
          if (!$products->is_empty()) {
                foreach ($products as $k => $v) {
          ?>
          <tr>
              <td><?= $v['productId'] ?></td>
              <td><a href="<?= $v['designUrl'] ?>"><?= $v['designId'] ?></a></td>
              <td>
                  <img src="<?= $v['thumbnailUrl'] ?>"/>
              </td>
              <td><a href="<?= $v['marketplaceUrl'] ?>"><?= $v['name'] ?></a></td>
              <td><a href="<?= $v['storeUrl'] ?>"><?= $v['storeName'] ?></a></td>
              
          </tr>
          <?php
                }// end foreach
          }//end if
          ?>
      </table>
  </body>
</html>
