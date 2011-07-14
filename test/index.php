<?php
require 'simpletest/autorun.php';
require '../init.php';

class TestCafePressApiProducts extends UnitTestCase {

    public function setUp()
    {
    }

    public function testProductsGetMethod()
    {
        $search = 'alcoholic humor T-Shirts';
        $length = 20;
        
        $products = CafePressApi::Products()->get($search, array('merchandiseIds' => 152, 'pageNumber' => 1, 'resultsPerPage' => $length));

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));
        
        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v) 
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));
        

        $this->assertEqual($length, $products->resultLength);
        $this->assertEqual($length, $loop);
        
    }

    public function testProductsGetByProductIdMethod()
    {
        $search = 261785985;

        //$products = CafePressApi::Products()->get($search, array( 'merchandiseIds' => 152 ) );
        $products = CafePressApi::Products()->get_by_productids($search);

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));

        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v)
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);
        
        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));


        $this->assertEqual(1, $products->resultLength);
        $this->assertEqual(1, $loop);

    }

    public function testProductsGetByProductIdsMethod()
    {
        $search = array(261785985, 270161481, 270161480);

        //$products = CafePressApi::Products()->get($search, array( 'merchandiseIds' => 152 ) );
        $products = CafePressApi::Products()->get_by_productids($search);

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));

        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v)
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));


        $this->assertEqual(count($search), $products->resultLength);
        $this->assertEqual(count($search), $loop);

    }

    public function testProductsGetByDesignIdMethod()
    {

        $search = 27690132;

        $products = CafePressApi::Products()->get_by_designids($search);

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));


        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v)
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));

    }

    public function testProductsGetByDesignIdsMethod()
    {
        $search = array(53146960, 28478617);
        
        $products = CafePressApi::Products()->get_by_designids($search);

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));


        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v)
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));

    }

    public function testProductsGetByStoreIdMethod()
    {
        $search = 'kansasfest';
        
        $products = CafePressApi::Products()->get_by_storeid($search, array('pageNumber' => 2));

        /**
         * product id should exists
         */
        $this->assertTrue(isset($products['productId']));
        $this->assertTrue(isset($products['merchandiseId']));
        $this->assertTrue(isset($products['designId']));


        /**
         * :xml key is the product simple xml element object
         */
        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        /**
         * xmlresults contains all the simple xml element
         * object that was called from get method
         */
        foreach ($products->xmlresults as $k => $v)
            $this->assertTrue($v instanceof SimpleXMLElement);

        $this->assertTrue($products[':xml'] instanceof SimpleXMLElement);

        $total_loop = $products->resultLength ;
        $loop = 0;
        foreach ($products as $k => $v) {
            $loop++;
            $this->assertTrue(isset($v['productId']));
            $this->assertTrue(isset($v['merchandiseId']));
            $this->assertTrue(isset($v['designId']));
            $this->assertTrue($v[':xml'] instanceof SimpleXMLElement);
        }// end foreach

        $this->assertEqual($loop, $total_loop);

        $this->assertTrue(isset($products->resultLength));
        $this->assertTrue(isset($products->startResult));
        $this->assertTrue(isset($products->totalProducts));
        $this->assertTrue(isset($products->totalDesigns));
        $this->assertTrue(isset($products->xml));

    }

}

