<?php
/**
 * Class for change order status on Lazada
 */
class APILazada_SetOrderStatus extends APILazada_Orders {
  private $AllowStatuses = array("ToCanceled", "ToReadyToShip", "ToShipped", "ToFailedDelivery", "ToDelivered");
  private $ParamsKey = array("OrderItemId");

  public function __construct($site, $login, $api_token) {
    parent::__construct($site, $login, $api_token);
    $this->MethodName = "SetStatus";
  }

  public function query($params = array()) {
    if (is_array($params)) {
      if (isset($params["status"])) {
        $status = $params["status"];
        unset($params["status"]);
        if ($status != "" && in_array($status, $this->AllowStatuses)) {
          $this->MethodName .= $status;
        }
      }
      if (isset($params["id"]) && $params["id"] != "") {
        $this->RequestParams["OrderItemId"] = $params["id"];
        unset($params["id"]);
      }
    }
    return parent::query($params);
  }

  protected  function params() {
    $params = parent::params();
    foreach ($this->ParamsKey AS $key) {
      if (isset($this->RequestParams[$key]) && $this->RequestParams[$key] != "") {
        $params[$key] = $this->RequestParams[$key];
      }
    }
    return $params;
  }
}