<?php
/**
 * Project: CafePress PHP SDK
 *
 * This file is part of CafePressApiProducts.
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
 * @version v0.0.00b
 * @copyright 2011-2012
 * @author Qen Empaces,
 * @email qempaces@cafepress.com
 * @date 2011.06.30
 *
 */

class CafePressApiProducts
{
    const API_HOST      = 'open-api.cafepress.com';
    const API_VERSION   = '3';
    
    public static $Config = array(
        'appkey'    => '',
        'email'     => '',
        'password'  => ''
    );

    private $appkey     = '';
    private $email      = '';
    private $password   = '';

    public function __construct(array $config = array())
    {
        $this->appkey   = self::$Config['appkey'];
        $this->email    = self::$Config['email'];
        $this->password = self::$Config['password'];
        
        $configurations = array('appkey', 'email', 'password');
        foreach ($config as $k => $v) {
            if (empty($v) || !in_array($k, $configurations) ) continue;
            $this->$k = $v;
        }// end foreach

        foreach ($configurations as $k => $v) {
            if (empty($this->$v)) throw new Exception("{$v} is empty");
        }// end foreach
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

        $get['sort']            = $sort;
        $get['pageNumber']      = $pageNumber;
        $get['resultsPerPage']  = $resultsPerPage;
        $get['maxProductsPerDesign'] = $maxProductsPerDesign;

        if (!empty($merchandiseIds)) $get['merchandiseIds'] = (string) $merchandiseIds;

        if (empty($query)) throw new Exception('query empty');
        if (!is_array($query)) $query = array($query);

        $results = array();
        foreach ($query as $k => $v) {
            if (empty($v)) continue;
            
            $get['query'] = $v;
            $result = $this->curly('product.search.cp', array(
                'get' => $get
            ));

            if (empty($result['response'])) continue;

           $results[] = $result['response'];

        }// end foreach
        
        if (empty($results)) return array();
        
        $products = array();
        foreach ($results as $k => $xml) {
            $xml = new SimpleXMLElement($xml);
            
            $loop = $xml->xpath('/searchResultSet/mainResults/searchResultItem');
            foreach ($loop as $k => $result) {
                $result_products = $result->xpath('products/product');

                foreach ($result_products as $k => $product) {
                    
                    $push = array(
                        'designUrl'         => (string) $result['marketplaceUrl'],
                        'designImageUrl'    => (string) $result['mediaUrl'],
                        'designId'          => (string) $result['mediaId'],
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

                    $products[] = $push;
                }// end foreach
                
            }// end foreach
        }// end foreach

        return $products;
    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_productids($query, array $options = array())
    {

    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_storeid($query, array $options = array())
    {

    }

    /**
     *
     * @access
     * @var
     */
    public function get_by_designids($query, array $options = array())
    {

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
        $api_host           = self::API_HOST;
        
        extract($options, EXTR_IF_EXISTS);

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
            $post['v']      = self::API_VERSION;
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

}