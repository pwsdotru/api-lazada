<?php
/**
 * Class for load orders list from Lazada
 */
class APILazada_GetOrders extends APILazada_Orders {

  public function __construct($site, $login, $api_token) {
    parent::__construct($site, $login, $api_token);
    $this->MethodName = "GetOrders";
  }

  public function query($params = array()) {
    if (is_array($params)) {
      if (isset($params["CreatedAfter"]) && $params["CreatedAfter"]) {
        $created = new DateTime($params["CreatedAfter"]);
        $this->RequestParams["CreatedAfter"] = $created->format(DateTime::ISO8601);
        unset($params["CreatedAfter"]);
      }
    }
    return parent::query($params);
  }
  protected  function params() {
    $params = parent::params();
    if (isset($this->RequestParams["CreatedAfter"]) && $this->RequestParams["CreatedAfter"] != "") {
      $params["CreatedAfter"] =  $this->RequestParams["CreatedAfter"];
      $created = true;
    } else {
      $created = false;
    }
    return $params;
  }

  protected function prepare($data = array()) {
    if (isset($data["Body"]) && isset($data["Body"]["Orders"]) && isset($data["Body"]["Orders"]["Order"])) {
      return $data["Body"]["Orders"]["Order"];
    }
    return null;
  }

}