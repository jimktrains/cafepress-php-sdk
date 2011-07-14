<?php
/**
 * Project: CafePress PHP SDK
 *
 * This file is part of CafePress PHP SDK.
 *
 * CafePress PHP SDK is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 *
 * CafePress PHP SDK is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CafePress PHP SDK.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @version v0.0.01b
 * @copyright 2011-2012
 * @author Qen Empaces,
 * @email qempaces@cafepress.com
 * @date 2011.06.30
 *
 */

class CafePressApiProducts implements ArrayAccess, IteratorAggregate
{
    const API_HOST      = 'open-api.cafepress.com';
    const API_VERSION   = '3';

    public static $Config = array(
        'appkey'    => '',
    );

    private $appkey     = '';
    private $attr       = array();

    public $results     = null;
    public $iterator    = null;
    
    public $xmlresults  = array();

    // ** start ** required interface functions
    public final function offsetExists($offset)
    {
        if (is_string($offset)) {
            //if (preg_match('|^:xml\.|', $offset)) return false;
            
            $idx = $this->iterator->key();
            return isset($this->results[$idx][$offset]);
        }// endif

        return isset($this->results[$offset]);
    }

    public final function offsetGet($offset)
    {
        $idx = $this->iterator->key();
        
        if (is_string($offset) && $this->offsetExists($offset))
            return $this->results[$idx][$offset];

        if ($this->offsetExists($offset))
            return $this->results[$offset];
        
        return null;
    }

    public final function offsetSet($offset, $value)
    {
        return true;
    }

    public final function offsetUnset($offset)
    {
        $idx = $this->iterator->key();
        unset($this->results[$idx][$offset]);
    }

    public final function getIterator()
    {
        return $this->iterator;
    }
    // ** end ** required iterator functions

    public function __construct($appkey)
    {
        $config = CafePressApi::Config();
        $this->appkey = $config['appkey'];

        if (!empty($appkey))
            $this->appkey = $appkey;

        if (empty($this->appkey))
            throw new Exception("appkey is empty");
        
    }
    
    public function __get($name)
    {   
        if (array_key_exists($name, $this->attr)) {
            return $this->attr[$name];
        }//end if
        
        return null;
    }

    /**
     *
     * @access
     * @var
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->attr);
    }

    /**
     *
     * @access
     * @var
     */
    public function get($query, array $options = array())
    {
        $pageNumber             = 1;
        $resultsPerPage         = 20;
        $maxProductsPerDesign   = 1;
        $merchandiseIds         = null;
        $sort                   = 'by-date-desc';
        extract($options, EXTR_IF_EXISTS);

        $this->xml = array();
        $this->attr = array();
        $this->setResult(array());

        $get['sort']            = $sort;
        $get['pageNumber']      = $pageNumber;
        $get['resultsPerPage']  = $resultsPerPage;
        $get['maxProductsPerDesign'] = $maxProductsPerDesign;

        if (!empty($merchandiseIds)) $get['merchandiseIds'] = (string) $merchandiseIds;

        if (empty($query)) throw new Exception('query empty');
        if (!is_array($query)) $query = array($query);

        $results = array();
        foreach ($query as $k => $v) {
            if (empty($v) || array_key_exists($v, $results)) continue;
            
            $get['query'] = $v;
            $result = $this->curly('product.search.cp', array(
                'get' => $get
            ));

            if (empty($result['response'])) continue;

           $results[$v] = $result['response'];
        }// end foreach
        
        if (empty($results)) return $this;
        
        $products   = array();
        $this->attr = array(
            'totalDesigns'  => 0,
            'totalProducts' => 0,
            'startResult'   => 0,
            'resultLength'  => 0,
        );

        $this->xmlresults = array();
        
        foreach ($results as $k => $xml) {
            $xml = new SimpleXMLElement($xml);
            $this->xmlresults[$k] = $xml;
            
            $loop = $xml->xpath('/searchResultSet/mainResults/searchResultItem');
            
            foreach ($loop as $k => $result) {
                $result_products = $result->xpath('products/product');

                foreach ($result_products as $k => $product) {
                    
                    $push = array(
                        'designUrl'         => (string) $result['marketplaceUrl'],
                        'designImageUrl'    => (string) $result['mediaUrl'],
                        'designId'          => (string) $result['mediaId'],
                        'merchandiseId'     => (string) $product['productTypeNumber'],
                        'productId'         => (string) $product['productNumber'],
                        'name'              => (string) $product['caption'],
                        'colors'            => array(),
                        'sizes'             => array(),
                    );

                    $attr = $product->attributes();
                    foreach ($attr as $k => $v) $push[$k] = (string) $v;

                    /**
                     * colors and sizes
                     */
                    foreach ($product->colors->color as $k => $v) $push['colors'][] = (string) $v;
                    foreach ($product->sizes->size as $k => $v) $push['sizes'][] = (string) $v;

                    unset($push['productTypeNumber']);
                    unset($push['productNumber']);
                    unset($push['caption']);
                    
                    $push[':xml'] = $product;
                    $products[] = $push;
                    
                }// end foreach
                
            }// end foreach

            $this->attr['totalDesigns'] += $xml['totalDesigns'];
            $this->attr['totalProducts']+= $xml['totalProducts'];
            $this->attr['startResult']  += $xml['startResult'];
            $this->attr['resultLength'] += $xml['resultLength'];
            $this->attr['totalDesigns'] += $xml['totalDesigns'];

        }// end foreach

        $this->setResult($products);
        return $this;
    }


    /**
     *
     * @access
     * @var
     */
    private function build_product_result($xml)
    {
        $media = $xml->xpath('mediaConfiguration');

        $push = array(
            'designUrl'         => (string) $xml['defaultProductUri'],
            'designImageUrl'    => (string) "http://www.cafepress.com/dd/{$media[0]['designId']}",
            'designId'          => (string) $media[0]['designId'],
            'merchandiseId'     => (string) $xml['merchandiseId'],
            'productId'         => (string) $xml['id'],
            'colors'            => array(),
            'sizes'             => array(),
        );

        $attr = $xml->attributes();
        foreach ($attr as $k => $v) $push[$k] = (string) $v;

        /**
         * colors and sizes
         */
        $color = $xml->xpath('color');
        foreach ($color as $k => $v) $push['colors'][] = (string) $v['name'];
        $size = $xml->xpath('size');
        foreach ($size as $k => $v) $push['sizes'][] = (string) $v['name'];

        $push[':xml'] = $xml;

        return $push;
    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_productids($query)
    {
        $this->xml = array();
        $this->attr = array();
        $this->setResult(array());

        if (empty($query)) throw new Exception('query empty');
        if (!is_array($query)) $query = array($query);

        $results = array();
        foreach ($query as $k => $v) {
            if (empty($v) || array_key_exists($v, $results)) continue;

            $get = array('id' => $v);
            $result = $this->curly('product.find.cp', array(
                'get' => $get
            ));

            if (empty($result['response'])) continue;

           $results[$v] = $result['response'];
        }// end foreach

        if (empty($results)) return $this;

        $products   = array();
        $this->attr = array(
            'totalDesigns'  => 0,
            'totalProducts' => 0,
            'startResult'   => 0,
            'resultLength'  => 0,
        );

        $this->xmlresults = array();

        foreach ($results as $k => $xml) {
            $xml = new SimpleXMLElement($xml);
            $this->xmlresults[$k] = $xml;

            $products[] = $this->build_product_result($xml);

            $this->attr['totalProducts']+= 1;
            $this->attr['resultLength'] += 1;

        }// end foreach

        $this->setResult($products);
        return $this;
    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_storeid($query, array $options = array())
    {
        $pageNumber             = 1;
        $resultsPerPage         = 20;
        extract($options, EXTR_IF_EXISTS);
        
        $this->xml = array();
        $this->attr = array();
        $this->setResult(array());

        if (empty($query)) throw new Exception('query empty');
        if (!is_array($query)) $query = array($query);
        
        $results = array();
        foreach ($query as $k => $v) {
            if (empty($v) || array_key_exists($v, $results)) continue;

            $get = array(
                'storeId'   => $v,
                'page'      => $pageNumber,
                'pageSize'  => $resultsPerPage,
            );
            $result = $this->curly('product.listByStore.cp', array(
                'get' => $get
            ));

            if (empty($result['response'])) continue;

           $results[$v] = $result['response'];
        }// end foreach

        if (empty($results)) return $this;

        $products   = array();
        $this->attr = array(
            'totalDesigns'  => 0,
            'totalProducts' => 0,
            'startResult'   => 0,
            'resultLength'  => 0,
        );

        $this->xmlresults = array();

        foreach ($results as $k => $xml) {
            $xml = new SimpleXMLElement($xml);
            $this->xmlresults[$k] = $xml;

            $loop = $xml->xpath('/products/product');

            foreach ($loop as $k => $product) {
                $products[] = $this->build_product_result($product);

                $this->attr['totalProducts']+= 1;
                $this->attr['resultLength'] += 1;
            }//end foreach

        }// end foreach

        $this->setResult($products);
        return $this;
    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_designids($query, array $options = array())
    {
        $this->xml = array();
        $this->attr = array();
        $this->setResult(array());

        if (empty($query)) throw new Exception('query empty');
        if (!is_array($query)) $query = array($query);

        $results = array();
        foreach ($query as $k => $v) {
            if (empty($v) || array_key_exists($v, $results)) continue;

            $get = array('designId' => $v);
            $result = $this->curly('product.findByDesignId.cp', array(
                'get' => $get
            ));

            if (empty($result['response'])) continue;

           $results[$v] = $result['response'];
        }// end foreach

        if (empty($results)) return $this;

        $products   = array();
        $this->attr = array(
            'totalDesigns'  => 0,
            'totalProducts' => 0,
            'startResult'   => 0,
            'resultLength'  => 0,
        );

        $this->xmlresults = array();

        foreach ($results as $k => $xml) {
            $xml = new SimpleXMLElement($xml);
            $this->xmlresults[$k] = $xml;

            $loop = $xml->xpath('/products/product');

            foreach ($loop as $k => $product) {
                $products[] = $this->build_product_result($product);

                $this->attr['totalProducts']+= 1;
                $this->attr['resultLength'] += 1;
            }//end foreach

        }// end foreach
        
        $this->setResult($products);
        return $this;
    }

    /**
     *
     * @access
     * @var
     */
    private function curly($uri, array $options = array())
    {
        $ssl_verify         = false;
        $ssl_cainfo_file    = "";

        $return_header      = false;
        $follow_location    = true;

        $post               = array();
        $get                = array();

        $timeout            = 18;
        
        extract($options, EXTR_IF_EXISTS);

        $api_host = CafePressApi::HOST;
        $url = "http://{$api_host}/{$uri}";
        
        /**
         * init stuff
         */
        $ch             = curl_init();
        $retval         = array(
            'response'  => NULL,
            'error'     => NULL,
            'headers'   => NULL,
        );

        /**
         * process parameters
         */
        if (!empty($post)) {
            $post['v']      = CafePressApi::VERSION;
            $post['appKey'] = $this->appkey;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post, '', '&'));
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        } else {
            $get['v']       = self::API_VERSION;
            $get['appKey']  = $this->appkey;
            if (strpos($url, '?') === false) $url .= '?';
            $url .= http_build_query($get, '', '&');
        }//end if

        /**
         * curl settings
         */
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 9);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_HEADER, $return_header);

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);

        /**
         * setup headers
         */
        $header = array();
        $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,* /*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        /**
         * SSL VERIFICATION
         */
        switch($ssl_verify) {
            case false:
                curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
                break;

            case NULL:
                break;

        }//end switch

        /**
         * call curl, now na!
         */
        $response           = curl_exec($ch);
        $retval['response'] = $response;

        /**
         * error check
         */
        $retval['error']    = array(curl_errno($ch), curl_error($ch));

        /**
         * close curl
         */
        curl_close($ch);

        /**
         * grab header
         */
        if ($return_header === true) {
            $retval = array();

            // Extract headers from response
            $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
            preg_match_all($pattern, $response, $matches);
            $headers = split("\r\n", str_replace("\r\n\r\n", '', array_pop($matches[0])));

            // Extract the version and status from the first header
            $version_and_status = array_shift($headers);
            preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
            $retval['headers']['Http-Version'] = $matches[1];
            $retval['headers']['Status-Code'] = $matches[2];
            $retval['headers']['Status'] = $matches[2].' '.$matches[3];

            // Convert headers into an associative array
            foreach ($headers as $header) {
                preg_match('#(.*?)\:\s(.*)#', $header, $matches);
                $retval['headers'][$matches[1]][] = $matches[2];
            }//end foreach

            // Remove the headers from the response body
            $retval['response'] = preg_replace($pattern, '', $response);
        }//end if

        return $retval;
    }

    private function setResult($results)
    {
        $this->results = $results;
        $this->iterator = new ArrayIterator($this->results);
        $this->iterator->rewind();
    }

}