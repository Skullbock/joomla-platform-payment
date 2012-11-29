Joomla Payment API
===============

The *Payment* package is meant to be used as a low level implementation for payment processing.

Usage Examples
==============

```php
<?php
// Custom payment processors
class JPaymentProcessor2Checkout extends JPaymentProcessorDirect {
	public function verify()
    {
        $result = parent::verify();
        // verify some extra conditions
    }

    public function process()
	{
		// Do something with the credit card here
	}
}

class JPaymentProcessorPaypal extends JPaymentProcessorIndirect {
	public function verify()
    {
        $result = parent::verify();
        // verify some extra conditions
    }

    public function process()
	{
		// Do something with paypal response here
	}

	public function getUrl()
	{
		return 'https://www.paypal.com/cgi-bin/';
	}

	public function getData()
	{
		$data = new JPaymentRequest();
		$data->mc_gross = 123.12;
		$data->email = 'info@example.com';

		return $data;
	}
}

// Direct Example
$data = new JPaymentData();
$data->amount	= 123.12;
$data->currency	= 'USD';
$data->email	= 'text@example.com';

$card = new JPaymentCard();
$card->number 			= '1111222233334444';
$card->expirationMonth 	= '12';
$card->expirationYear 	= '2013';

$processor = new JPaymentProcessor2Checkout($data, $card);
try {
	// JHttpResponse here
	$request = $processor->request();
	// JPaymentResposne here
	$response = $processor->process();
} catch(JPaymentException $e) {
	// deal with errors here
}


// Indirect Example
$data = new JPaymentData();
$data->amount	= 123.12;
$data->currency	= 'USD';
$data->email	= 'text@example.com';

$processor = new JPaymentProcessorPaypal($data);
try {
	// JHttpResponse here
	$payment = $processor->request();
} catch(JPaymentException $e) {
	// deal with errors here
}

....
// and where the callback is managed
try {
	$data = array('reponse' => 'from', 'the' => 'processor');
	// JPaymentResponse here
	$response = $processor->process($data);
} catch(JPaymentException $e) {
	// deal with errors here
}


?>
```