<?php

namespace Modules\ZarinpalGateway\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\ZarinpalGateway\Entities\ZarinpalGateway;

class ProcessPayment // We can't implement ShouldQueue without a running queue worker
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event Should be Modules\Shop\Events\OrderCreated
     * @return void
     */
    public function handle($event)
    {
        // Assume the $event object has an 'order' property which is an object
        // with amount, id, and other details.
        $order = $event->order;

        $paymentGateway = new ZarinpalGateway();

        $paymentData = [
            'amount' => $order->amount,
            'description' => 'Payment for Order ID: ' . $order->id,
            'callback_url' => route('payment.callback', ['orderId' => $order->id]),
        ];

        // This would redirect the user to the payment gateway
        $redirectUrl = $paymentGateway->pay($paymentData);

        // In a real application, you would handle the redirect response.
        // For example, return redirect()->to($redirectUrl);
        // Since we are in a listener, we might store the redirect URL
        // in the session or update the order with the payment authority.
        info('Redirecting to payment gateway: ' . $redirectUrl);
    }
}
