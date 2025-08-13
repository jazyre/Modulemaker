<?php

namespace Modules\ZarinpalGateway\Entities;

use App\Interfaces\PaymentInterface;
use Exception;
use Illuminate\Support\Facades\Config;
use Modules\ZarinpalGateway\Events\PaymentProcessed;
use ZarinPal\Sdk\ZarinPal;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\RequestRequest;
use ZarinPal\Sdk\Endpoint\PaymentGateway\RequestTypes\VerifyRequest;
use ZarinPal\Sdk\Options;

class ZarinpalGateway implements PaymentInterface
{
    /**
     * @var ZarinPal
     */
    protected $zarinpal;

    /**
     * @var string
     */
    protected $merchantId;

    public function __construct()
    {
        $this->merchantId = Config::get('zarinpalgateway.merchant_id');
        $options = new Options(
            'https://api.zarinpal.com',
            $this->merchantId,
            '/pg/v4/payment/',
            true,
            'zarinpal'
        );
        $this->zarinpal = new ZarinPal($options);
    }

    /**
     * Initiate a payment request.
     *
     * @param  array  $data Requires 'amount', 'callback_url', 'description'
     * @return string The redirect URL for the payment gateway.
     */
    public function pay(array $data): string
    {
        $request = new RequestRequest(
            $this->merchantId,
            $data['amount'],
            $data['callback_url'],
            $data['description']
        );

        $response = $this->zarinpal->paymentGateway()->request($request);

        return $this->zarinpal->paymentGateway()->getRedirectUrl($response->getAuthority());
    }

    /**
     * Verify a payment.
     *
     * @param  array  $data Requires 'amount', 'authority', and 'orderId'
     * @return array The verification result containing the reference ID.
     */
    public function verify(array $data): array
    {
        try {
            $request = new VerifyRequest(
                $this->merchantId,
                $data['amount'],
                $data['authority']
            );

            $response = $this->zarinpal->paymentGateway()->verify($request);

            // Dispatch a success event
            event(new PaymentProcessed(
                $data['orderId'],
                'success',
                $response->getRefId()
            ));

            return [
                'status' => 'success',
                'ref_id' => $response->getRefId(),
                'card_pan' => $response->getCardPan(),
                'card_hash' => $response->getCardHash(),
                'fee_type' => $response->getFeeType(),
                'fee' => $response->getFee(),
            ];

        } catch (Exception $e) {
            // Dispatch a failure event
            event(new PaymentProcessed(
                $data['orderId'],
                'failed',
                null
            ));

            return [
                'status' => 'failed',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }
}
