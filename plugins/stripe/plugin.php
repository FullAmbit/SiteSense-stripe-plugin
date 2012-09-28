<?php
// Stripe payments plugin for SiteSense
class plugin_stripe {
	var $apiKey;
	public function __construct(){
		require_once(__DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Stripe.php');
		$this->apiKey = 'live secret key';
		Stripe::setApiKey($this->apiKey);
	}
	public function capture($params){
		try {
			$chargeObject = Stripe_Charge::create(array(
				'amount'=>(floatval($params['amount'])*100), // amount in pennies
				'currency'=>'usd',
				'customer'=>$params['gatewayid'],
				'description'=>$params['description'],
			));
			return $chargeObject['id'];
		} catch (Exception $e) {
			return FALSE;
		}
	}
	public function saveCustomer($params){
		try {
			$newCustomer = Stripe_Customer::create(array(
				'card'=>array(
					'number'=>$params['cardnum'],
					'exp_month'=>substr($params['cardexp'],0,2),
					'exp_year'=>substr(date('Y'),0,2).substr($params['cardexp'],2,4), // will still work in the year 2100 :D
					'name'=>implode(' ',array($params['clientdetails']['firstname'],$params['clientdetails']['lastname'])),
					'address_line1'=>$params['clientdetails']['address1'],
					'address_line2'=>$params['clientdetails']['address2'],
					'address_zip'=>$params['clientdetails']['postcode'],
					'address_state'=>$params['clientdetails']['state'],
					'address_country'=>$params['clientdetails']['country'],
				),
				'email'=>$params['clientdetails']['email'],
			));
			return $newCustomer['id'];
		} catch (Exception $e) {
			return FALSE;
		}
	}
	public function refund($params){
		try {
			$refundObject = Stripe_Charge::retrieve($params['transid']);
			if ($params['amount']) {
				$refundObject->refund(array('amount'=>floatval($params['amount'])*100));
			} else {
				$refundObject->refund();
			}
			return TRUE;
		} catch (Exception $e) {
			return FALSE;
		}
	}
}