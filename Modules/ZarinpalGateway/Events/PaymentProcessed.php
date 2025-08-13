<?php

namespace Modules\ZarinpalGateway\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentProcessed
{
    use Dispatchable, SerializesModels;

    public $orderId;
    public $status;
    public $referenceId;

    /**
     * Create a new event instance.
     *
     * @param int $orderId
     * @param string $status
     * @param string|null $referenceId
     * @return void
     */
    public function __construct(int $orderId, string $status, ?string $referenceId)
    {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->referenceId = $referenceId;
    }
}
