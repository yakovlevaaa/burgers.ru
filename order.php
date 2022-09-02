<?php
include './src/config.php';
include './src/class.db.php';
include './src/functions.php';
include './src/class.order.php';

$burger_order = new burgerOrder ();

$email = $_POST['email'];
$user_name = $_POST['name'];
$phone = $_POST['phone'];
$address_data = ['street', 'home', 'part', 'appt', 'floor'];
$address = '';
foreach ($_POST as $key => $value) {
    if ($value && in_array($key, $address_data)) {
        $address .= $value . ', ';
    }
}
$data = ['address' => $address];

$user = $burger_order->getUserByEmail($email);

if ($user) {
    $user_id = $user['id'];
    $burger_order->incOrders($user['id']);
    $order_num = $user['orders_count'] +1;
} else {
    $user_id = $burger_order->createUser($email, $user_name);
    $order_num = 1;
}

$order_id = $burger_order->addOrder($user_id, $phone, $data);

echo "Cпасибо, $user_name! </br>
Ваш заказ будет доставлен по адресу: $address </br>
Номер вашего заказа:  $order_id </br>
Это ваш $order_num-й заказ!";

