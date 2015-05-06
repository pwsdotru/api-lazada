<?php
/**
 * Main section for init Lazada API
 */

require_once(__DIR__ . "/settings.php");

/* Common */
require_once(API_LAZADA_CLASS_PATCH . "/Request.php");
/* Orders */
require_once(API_LAZADA_CLASS_PATCH . "/Orders/Orders.php");
require_once(API_LAZADA_CLASS_PATCH . "/Orders/GetOrders.php");
require_once(API_LAZADA_CLASS_PATCH . "/Orders/GetOrder.php");
require_once(API_LAZADA_CLASS_PATCH . "/Orders/GetOrderItems.php");
require_once(API_LAZADA_CLASS_PATCH . "/Orders/SetOrderStatus.php");