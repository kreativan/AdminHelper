<?php

/**
 * Auth based on api-auth page template
 * 
 *  api-auth page fields: 
 *  - api_key
 *  - ip
 *  - website 
 * 
 * Apache needs to be configured to pass the authorization header
 * in .htaccess add:
 * RewriteEngine On
 * RewriteCond %{HTTP:Authorization} ^(.*)
 * RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
 * 
 * Api key should be provided in the authorization header
 * 
 * SERVER IP NOTE: 
 * Be careful with the IP auth. If the IP is the same as the server ip of the app, direct access will be allowed true the browser.
 * If you want to send request from the same server as the app, use referrer or api_key to authenticate.
 * 
 */

namespace AdminHelper;

use \ProcessWire\WireData;

class Auth extends WireData {

  public $request;

  public function __construct() {
    $this->request = $this->AdminHelper->Request();
  }

  // Allow CORS
  public function CORS() {
    header("Access-Control-Allow-Origin: *");  // Replace * with your actual domain for better security
    header("Access-Control-Allow-Methods: POST, GET");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    // allow iframes
    header("Content-Security-Policy: frame-ancestors *");
  }

  /**
   * Authenticate request
   * This will run isAuth() method
   * if not auth return json error
   */
  public function auth($debug = false) {
    if ($this->isAuth()) return;
    $this->AdminHelper->json_response([
      'status' => 'error',
      'message' => 'Unauthorized',
      'api_key' => $this->request->data('api_key'),
      'debug' => $debug ? $this->request->data() : false,

    ]);
    exit();
  }

  /**
   * Authenticate request
   * - check if user is superuser
   * - check if request ip is in allowed ips
   * - check if request domain is in allowed domains by req refferer
   * @return bool
   */
  public function isAuth() {

    // get requestData
    $req = $this->request->data();

    // if Superuser
    if ($this->user->isSuperuser()) return true;

    // If api login route return true
    if ($this->input->urlSegment1 == 'login') return true;

    // if super user return true
    if ($this->user->isSuperuser()) return true;

    // if there is api-auth page return true
    $apiAuthPage = $this->apiAuthPage($req);
    if ($apiAuthPage != '') return true;

    // end is always false
    return false;
  }

  /**
   * Get api auth page
   * based on request data: api_key, server_ip, referer
   * @param array $req - request data
   */
  public function apiAuthPage($req) {
    // api-key
    $api_key = $req['api_key'];
    $api_key_page = $this->pages->findOne("template=api-auth, api_key=$api_key, api_key!='");
    if ($api_key_page != '') return $api_key_page;

    // server-ip
    $server_ip = $req['server_ip'];
    $server_ip_page = $this->pages->findOne("template=api-auth, ip=$server_ip, ip!=''");
    if ($server_ip_page != '') return $server_ip_page;

    // referer
    $referer = $req['referer'];
    $referer_page = $this->pages->findOne("template=api-auth, website=$referer, website!=''");
    if ($referer_page != '') return $referer_page;

    return "";
  }
}
