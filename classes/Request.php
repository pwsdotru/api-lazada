<?php
/**
 * Common class for API request
 */
class APILazada_Request {
  private $UserId = null;
  private $MainSite = null;
  private $ApiToken;

  private $ParamsKey = array();
  //:TODO: Add support to params method

  protected $MethodName = null;
  protected $RequestParams = array();
  protected $ErrorResponse = array();

  public function __construct($site, $user, $api_token) {
    $this->UserId = $user;
    $this->MainSite = $site;
    $this->ApiToken = $api_token;
  }
  public function query($params = null) {
    $request_params = $this->params();
    $request_params = $this->sign($request_params);

    $xml = $this->curl($request_params);

    $data = $this->convert($xml);

    if (isset($data["Head"]) && isset($data["Head"]["ErrorCode"])) {
      $this->ErrorResponse = $data["Head"];
      return null;
    }
    return $this->prepare($data);
  }

    /**
     * Return error message for last API call
     * @return string
     */
  public function error() {
    if (isset($this->ErrorResponse) && is_array($this->ErrorResponse) && isset($this->ErrorResponse["ErrorCode"])) {
      return $this->ErrorResponse["ErrorMessage"];
    }
    return "";
  }

  /**
   * Return error code for last API call.
   * Return integer number
   * @return string
   */
  public function error_code() {
    if (isset($this->ErrorResponse) && is_array($this->ErrorResponse) && isset($this->ErrorResponse["ErrorCode"])) {
      return $this->ErrorResponse["ErrorCode"];
    }
    return "";
  }
  /**
   * Extract data from response array
   * @param array $data
   * @return null|array
   */
  protected function prepare($data = array()) {
    if (isset($data["Body"])) {
      return $data["Body"];
    } else {
      return null;
    }
  }

  /**
   * Fix issue with single result in response
   * @param array $arr
   * @return array
   */
  protected function fix($arr = array()) {
    if (isset($arr[0])) {
      return $arr;
    }
    return array(0 => $arr);
  }
  /**
   * Init common params
   * @return array
   */
  protected function params() {
    $now = new DateTime();

    $result = array(
      "Action" => $this->MethodName,
      "UserID" => $this->UserId,
      "Version" => API_LAZADA_VERSION,
      "Timestamp" => $now->format(DateTime::ISO8601),
    );
    return $result;
  }

  /**
   * Sign request parameters
   * @param $params array
   * @return array
   */
  private function sign($params) {
    ksort($params);
    $strToSign = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    $signature = rawurlencode(hash_hmac('sha256', $strToSign, $this->ApiToken, false));
    $params['Signature'] = $signature;
    return $params;
  }

  /**
   * Make request to API url
   * @param $params array
   * @return string
   */
  private function curl($params) {
    $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    // Open Curl connection
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_LAZADA_URL . $this->MainSite . "?" . $queryString);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
  }

  /**
   * Convert response XML to associative array
   * @param $xml string
   * @return array
   */
  private function convert($xml) {
    $obj = simplexml_load_string($xml);
    $array = json_decode(json_encode($obj), true);
    $array = $this->sanitize($array);
    return $array;
  }

  /**
   * Clear array after convert. Remove empty arrays and change to string
   * @param $arr array
   * @return array
   */
  private function sanitize($arr) {
    foreach($arr AS $k => $v) {
      if (is_array($v)) {
        if (count($v) > 0) {
          $arr[$k] = $this->sanitize($v);
        } else {
          $arr[$k] = "";
        }
      }
    }
    return $arr;
  }
}