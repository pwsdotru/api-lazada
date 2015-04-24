<?php
/**
 * Class for load order items from Lazada
 */
class APILazada_GetOrderItems extends APILazada_Orders {

  public function __construct($site, $login, $api_token) {
    parent::__construct($site, $login, $api_token);
    $this->MethodName = "GetOrderItems";
  }

  public function query($order_id = null) {
    if ($order_id) {
        $this->RequestParams["OrderId"] = $order_id;
    }
    return parent::query();
  }
  protected  function params() {
    $params = parent::params();
    if (isset($this->RequestParams["OrderId"]) && intval($this->RequestParams["OrderId"]) > 0) {
      $params["OrderId"] =  $this->RequestParams["OrderId"];
    }
    return $params;
  }

  protected function prepare($data = array()) {
    if (isset($data["Body"]) && isset($data["Body"]["OrderItems"]) && isset($data["Body"]["OrderItems"]["OrderItem"])) {
      return parent::fix($data["Body"]["OrderItems"]["OrderItem"]);
    }
    return null;
  }

}