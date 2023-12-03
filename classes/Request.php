<?php

/**
 * Request
 * Get $_SERVER request data
 */

namespace AdminHelper;

class Request {

  /**
   * Array of ($_SERVER) request data
   * @return array
   */
  public function data($key = "") {
    $array = [
      'api_key' => $this->getApiKey(),
      'server' => $_SERVER,
      "method" => $this->getRequestMethod(),
      "ip" => $this->getIPAddress(),
      "public_ip" => $this->getPublicIp(),
      "server_ip" =>  $this->getServerIp(),
      "domain" => $this->getDomainName(),
      "agent" => $this->getUserAgent(),
      "referer" => $this->getReferer(),
      "uri" => $this->getRequestUri(),
      "query" => $this->getQueryString(),
      "port" => $this->getServerPort(),
      'country' => $this->getCountry(),
    ];
    return $key != "" ? $array[$key] : $array;
  }

  /**
   * Api key should be provided in the authorization header
   * apache needs to be configured to pass the authorization header
   * in .htaccess add:
   * RewriteEngine On
   * RewriteCond %{HTTP:Authorization} ^(.*)
   * RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
   */
  public function getApiKey() {
    if (isset($_GET['api_key'])) {
      return $_GET['api_key'];
    }
    return $_SERVER['HTTP_AUTHORIZATION'] ?? '';
  }

  /**
   * Public IP
   * without validating ir
   * @return array
   */
  function getPublicIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }


  /**
   * Get IP Address
   * this also validates the ip address
   * @return string
   */
  public function getIPAddress() {

    // Check for remote IP address
    if (filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
      return $_SERVER['REMOTE_ADDR'];
    }

    // Check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
      return $_SERVER['HTTP_CLIENT_IP'];
    }

    // Check for IP address passed by proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Return a default value if all else fails
    return '0.0.0.0';
  }

  /**
   * Get the server IP address
   * From which server ip req is sent
   * @return string
   */
  public function getServerIp() {
    return $_SERVER['HTTP_X_SERVER_ADDR'] ?? '0.0.0.0';
  }


  /**
   * Get Domain Name
   * domain to which req is sent
   * @return string
   */
  public function getDomainName() {
    if (isset($_SERVER['SERVER_NAME'])) {
      return $_SERVER['SERVER_NAME'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get User Agent
   * @return string
   */
  function getUserAgent() {
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
      return $_SERVER['HTTP_USER_AGENT'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get Referer
   * Domain from from which req is sent
   * @return string
   */
  function getReferer() {
    if (!empty($_SERVER['HTTP_REFERER'])) {
      return $_SERVER['HTTP_REFERER'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get Request Method
   * @return string
   */
  function getRequestMethod() {
    if (!empty($_SERVER['REQUEST_METHOD'])) {
      return $_SERVER['REQUEST_METHOD'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get Content Type
   * Type of content that is sent to the end-point
   * this is used for json requests "application/json"
   * @return string
   */
  function getContentType() {
    return $_SERVER['CONTENT_TYPE'] ?? 'unknown';
  }


  /**
   * Get Request URI
   * @return string
   */
  function getRequestUri() {
    if (!empty($_SERVER['REQUEST_URI'])) {
      return $_SERVER['REQUEST_URI'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get Query String
   * @return string
   */
  function getQueryString() {
    if (!empty($_SERVER['QUERY_STRING'])) {
      return $_SERVER['QUERY_STRING'];
    } else {
      return '';
    }
  }

  /**
   * Get Server Port
   * @return string
   */
  function getServerPort() {
    if (!empty($_SERVER['SERVER_PORT'])) {
      return $_SERVER['SERVER_PORT'];
    } else {
      return 'unknown';
    }
  }

  /**
   * Get country code
   * if cloudflare is enabled
   * @return string
   */
  public function getCountry() {
    return $_SERVER["HTTP_CF_IPCOUNTRY"] ?? 'unknown';
  }
}
