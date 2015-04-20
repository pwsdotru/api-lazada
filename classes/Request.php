<?php
/**
 * Common class for API request
 */
class APILazada_Request {
  private $UserId = null;
  private $MainSite = null;
  private $ApiToken;

  protected $MethodName = null;
  protected $RequestParams = array();
  protected $ErrorResponse = array();

  public function __construct($site, $user, $api_token) {
    $this->UserId = $user;
    $this->MainSite = $site;
    $this->ApiToken = $api_token;
  }
  public function query($params) {
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
  protected function prepare($data = array()) {
    if (isset($data["Body"])) {
      return $data["Body"];
    }
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

  private function convert($xml) {
    $obj = simplexml_load_string($xml);
    return json_decode(json_encode($obj), true);
  }
}