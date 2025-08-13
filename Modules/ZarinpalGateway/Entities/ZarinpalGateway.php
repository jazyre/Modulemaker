<?php

namespace Modules\ZarinpalGateway\Entities;

use App\Interfaces\PaymentInterface;
use ZarinPal\Sdk\ZarinPal;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\RequestRequest;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\VerifyRequest;

class ZarinpalGateway implements PaymentInterface
{
    /**
     * @var ZarinPal
     */
    protected $zarinpal;

    public function __construct()
    {
        // We can't instantiate Zarinpal without its dependencies.
        // In a real application, this would be resolved by the service container.
        // For now, we will assume that the Zarinpal object is injected.
        // $this->zarinpal = new ZarinPal();
    }

    /**
     * Initiate a payment request.
     *
     * @param  array  $data
     * @return mixed
     */
    public function pay(array $data)
    {
        // This is a mock implementation.
        // In a real application, we would get the merchant ID from the config.
        $merchantId = 'YOUR_MERCHANT_ID';

        $request = new RequestRequest(
            $merchantId,
            $data['amount'],
            $data['callback_url'],
            $data['description']
        );

        // We can't call the request method without a Zarinpal instance.
        // $response = $this->zarinpal->paymentGateway()->request($request);
        // return $this->zarinpal->paymentGateway()->getRedirectUrl($response->getAuthority());

        // For now, we will return a dummy URL.
        return 'https://www.zarinpal.com/pg/StartPay/DUMMY_AUTHORITY';
    }

    /**
     * Verify a payment.
     *
     * @param  array  $data
     * @return mixed
     */
    public function verify(array $data)
    {
        // This is a mock implementation.
        $merchantId = 'YOUR_MERCHANT_ID';

        $request = new VerifyRequest(
            $merchantId,
            $data['amount'],
            $data['authority']
        );

        // We can't call the verify method without a Zarinpal instance.
        // $response = $this->zarinpal->paymentGateway()->verify($request);
        // return $response->getRefId();

        // For now, we will return a dummy ref ID.
        return 'DUMMY_REF_ID';
    }
}
