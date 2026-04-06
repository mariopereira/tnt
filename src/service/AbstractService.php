<?php

/**
 * Abstract Service
 *
 * @author Wojciech Brozyna <http://vobro.systems>
 * @license https://github.com/200MPH/tnt/blob/master/LICENCE MIT
 */

namespace thm\tnt_ec\service;

use thm\tnt_ec\MyXMLWriter;
use thm\tnt_ec\TNTException;

abstract class AbstractService
{
    
    /**
     * XML Request
     *
     * @var MyXMLWriter
     */
    protected $xml;
    
    /**
     * Account number
     *
     * @var int
     */
    protected $account = 0;
    
    /**
     * Account country code
     *
     * @var string
     */
    protected $accountCountryCode = 'GB';
    
    /**
     * Origin (destination) country code
     *
     * @var string
     */
    protected $originCountryCode = 'GB';
            
    /**
     * User ID
     *
     * @var string
     */
    protected $userId;
    
    /**
     * Password
     *
     * @var string
     */
    protected $password;
    
    /**
     * Disable SSL verification
     *
     * @var bool
     */
    private $verifySSL = true;
    
    /**
     * Get TNT service URL
     *
     * @var string
     */
    abstract public function getServiceUrl();
    
    /**
     * Initialise service
     *
     * @param string $userId
     * @param string $password
     * @throw TNTException
     */
    public function __construct($userId, $password)
    {
        
        if (empty($userId) === true) {
            throw new TNTException(TNTException::USERNAME_EMPTY);
        }
        
        if (empty($password) === true) {
            throw new TNTException(TNTException::PASS_EMPTY);
        }
        
        $this->userId = $userId;
        $this->password = $password;
        $this->initXml();
    }
    
    /**
     * Initialize XML object
     *
     * @return void
     */
    public function initXml()
    {
        $this->xml = new MyXMLWriter();
        $this->xml->openMemory();
        $this->xml->setIndent(true);
    }
           
    /**
     * Set account number.
     * Will be provided by your TNT representative.
     *
     * @param int $accountNumber
     * @return AbstractService
     */
    public function setAccountNumber($accountNumber)
    {
        
        $this->account = $accountNumber;
        
        return $this;
    }
    
    /**
     * Set account country code
     *
     * @param string $countryCode
     * @return AbstractService
     */
    public function setAccountCountryCode($countryCode)
    {
        
        $this->accountCountryCode = $countryCode;
        return $this;
    }
    
    /**
     * Set origin (destination) country code
     *
     * @param string $countryCode
     * @return AbstractService
     */
    public function setOriginCountryCode($countryCode)
    {
        
        $this->originCountryCode = $countryCode;
        return $this;
    }
    
    /**
     * Disable SSL verification
     *
     * @return AbstractService
     */
    public function disableSSLVerify()
    {
        
        $this->verifySSL = false;
        return $this;
    }
    
    /**
     * Build/start document
     *
     * @return void
     */
    protected function startDocument()
    {
        
        $this->xml->startDocument('1.0', 'UTF-8', 'no');
    }
    
    /**
     * Build/end document
     *
     * @return void
     */
    protected function endDocument()
    {
        
        $this->xml->endDocument();
    }
    
    /**
     * Set XML content.
     * This is useful when you want to send your own prepared XML document.
     *
     * @param string $xml
     * @return bool
     */
    public function setXmlContent($xml)
    {
        
        $this->xml->flush();
        return $this->xml->writeRaw($xml);
    }
    
    /**
     * Get XML content
     *
     * @return string
     */
    protected function getXmlContent()
    {
        
        return trim($this->xml->flush(false));
    }
    
/**
     * Send request
     *
     * @return string Returns TNT Response string as XML
     */
    protected function sendRequest()
    {
        /*
        $headers[] = "Content-type: application/x-www-form-urlencoded";
        $headers[] = "Authorization: Basic " . base64_encode("$this->userId:$this->password");

        $context = stream_context_create(array(
                'http' => array(
                    'header' => $headers,
                    'method' => 'POST',
                    'content' => $this->buildHttpPostData()
                ),
                'ssl' => array(
                     'verify_peer' => 0, // $this->verifySSL,
                     'verify_peer_name' => 0) // $this->verifySSL)
                ));

        $output = @file_get_contents($this->getServiceUrl(), false, $context);

        // $http_response_header comes from PHP engine,
        // it's not a part of this code
        // http://php.net/manual/en/reserved.variables.httpresponseheader.php
        if (empty($http_response_header) === false) {
            HTTPHeaders::$headers = $http_response_header;
        }

        return $output;
        */

        // new methor beloow, not used now for tests
        $headers = [];
        $output = null;
        $stderr = null;
        $dump = '';

        $url = $this->getServiceUrl();
        $postData = $this->buildHttpPostData();

        $curl_opts = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => (string) $postData,
            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_HTTPHEADER => [
                "Content-type: application/x-www-form-urlencoded",
                "Expect:"
            ],

            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$this->userId:$this->password",

            CURLOPT_SSL_VERIFYPEER => $this->verifySSL,
            CURLOPT_SSL_VERIFYHOST => $this->verifySSL ? 2 : 0,

            CURLOPT_HEADERFUNCTION => function($curl, $header) use (&$headers) {
                $len = strlen($header);
                $headers[] = trim($header);
                return $len;
            }
        ];

        $stderr = fopen('php://temp', 'rw+');
        $curl_opts[CURLOPT_VERBOSE] = true;
        $curl_opts[CURLOPT_STDERR] = $stderr;

        $ch = curl_init();
        curl_setopt_array($ch, $curl_opts);

        $output = curl_exec($ch);

        if (function_exists('log_message')) {
            log_message('error', 'TNT Output: ' . print_r($output, true));
        }

        if (!empty($headers)) {
            HTTPHeaders::$headers = $headers;
        }

        $dump = null;

        if (!is_null($stderr)) {
            rewind($stderr);
            $dump = stream_get_contents($stderr);
            fclose($stderr);
        }

        $error = curl_errno($ch) || ($output === false);

        if ($error && function_exists('log_message')) {
            $msg = 'CURL error #' . curl_errno($ch) . ': ' . curl_error($ch) . ' on ' . __CLASS__ . '::' . __METHOD__ . ' (' . __LINE__ . ')';
            log_message('error', $msg);
            log_message('error', 'Info: ' . json_encode(curl_getinfo($ch)));

            if (!empty($dump)) {
                log_message('error', 'Dump: ' . PHP_EOL . $dump);
            }
        }

        curl_close($ch);

        return $error ? false : $output;
    }
 
    /**
     * Build HTTP Post data
     *
     * @return string
     */
    private function buildHttpPostData()
    {
        $xmlContent = $this->getXmlContent();
        $post = http_build_query(array('xml_in' => $xmlContent));
        return $post;
    }
}
