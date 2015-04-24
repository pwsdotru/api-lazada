<?php
/**
 * Class for load orders list from Lazada
 */
class APILazada_GetOrders extends APILazada_Orders {
  private $ParamsKey = array("CreatedAfter", "CreatedBefore", "UpdatedAfter", "CreatedBefore");
  public function __construct($site, $login, $api_token) {
    parent::__construct($site, $login, $api_token);
    $this->MethodName = "GetOrders";
  }

  public function query($params = array()) {
    if (is_array($params)) {
      foreach ($this->ParamsKey AS $key) {
        if (isset($params[$key]) && $params[$key] != "") {
          $date = new DateTime($params[$key]);
          $this->RequestParams[$key] = $date->format(DateTime::ISO8601);
          unset($params[$key]);
        }
      }
    }
    return parent::query($params);
  }

  protected  function params() {
    $params = parent::params();
    foreach($this->ParamsKey AS $key) {
      if (isset($this->RequestParams[$key]) && $this->RequestParams[$key] != "") {
        $params[$key] = $this->RequestParams[$key];
      }
    }
    return $params;
  }

  protected function prepare($data = array()) {
    if (isset($data["Body"]) && isset($data["Body"]["Orders"]) && isset($data["Body"]["Orders"]["Order"])) {
      return parent::fix($data["Body"]["Orders"]["Order"]);
    }
    return null;
  }

}