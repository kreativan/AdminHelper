<?php

/**
 * Auth based on api-auth page template
 * 
 *  api-auth page fields: 
 *  - api_key
 * 
 * apache needs to be configured to pass the authorization header
 * in .htaccess add:
 * RewriteEngine On
 * RewriteCond %{HTTP:Authorization} ^(.*)
 * RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
 * 
 * Api key should be provided in the authorization header
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
   * based on request data: ip, public_ip and referer
   * @param array $req - request data
   */
  public function apiAuthPage($req) {
    $api_key = $req['api_key'];
    $selector = "template=api-auth, api_key=$api_key, api_key!=''";
    return $this->pages->get($selector);
  }
}
