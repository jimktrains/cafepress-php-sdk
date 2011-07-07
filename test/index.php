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
        $length = 20;
        
        //$products = CafePressApi::Products()->get($search, array( 'merchandiseIds' => 152 ) );
        $products = CafePressApi::Products()->get($search, array('resultsPerPage' => $length));

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));
        $this->assertTrue($products->xml instanceof SimpleXMLElement);

        $this->assertEqual($length, $products->resultLength);
        $this->assertEqual($length, $loop);
        
    }


}

