<?php
die('test script');
require_once('plugin.php');
$stripe = new plugin_stripe();
$customer = $stripe->saveCustomer(array(
	'cardnum' => '4242424242424242',
	'cardexp' => '1114',
	'clientdetails' => array(
		'email' => 'zbloomquist@fullambit.com',
		'firstname' => 'Yohan',
		'lastname' => 'Johan',
		'address1' => '1 Infinite Loop',
		'address2' => '',
		'postcode' => '30548',
		'state' => 'GA',
		'country' => 'USA',
	),
));
var_dump($customer);
$capture = $stripe->capture(array(
	'amount' => 3.50,
	'gatewayid' => $customer,
	'description' => 'Test Transaction',
));
var_dump($capture);
$refund = $stripe->refund(array(
	'amount' => 3.50,
	'transid' => $capture,
));
var_dump($refund);
