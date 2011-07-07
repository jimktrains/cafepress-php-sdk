<?php
require 'simpletest/autorun.php';
require '../init.php';

class TestCafePressApiProducts extends UnitTestCase {

    public function setUp()
    {
    }

    public function testProductsGetMethod()
    {
        $this->assertTrue(true);
        $search = 'alcoholic humor T-Shirts';
        
        //$products = CafePressApi::Products()->get($search, array( 'merchandiseIds' => 152 ) );
        $products = CafePressApi::Products()->get($search);

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));
        
    }


}

