<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $orderData;
    public $pdf;

    /**
     * Create a new message instance.
     *
     * @param array $orderData
     * @param string $pdf
     * @return void
     */
    public function __construct($orderData, $pdf)
    {
        $this->orderData = $orderData;
        $this->pdf = $pdf;
    }


    public function build()
    {
        // customer name $this->orderData['customer_name'] ma aave che assume kari ne
        $customer = $this->orderData['customer'] ?? null;
        $customerName = $this->orderData['customerName'] ?? ($customer->name ?? 'Customer');

        $addressParts = [];

        if ($customer) {
            if (!empty($customer->street)) {
                $addressParts[] = $customer->street;
            }

            if (!empty($customer->house_number)) {
                $addressParts[] = $customer->house_number;
            }

            if (!empty($customer->suburb)) {
                $addressParts[] = $customer->suburb;
            }

            if (!empty($customer->state)) {
                $addressParts[] = $customer->state;
            }

            if (!empty($customer->pincod)) {
                $addressParts[] = $customer->pincod;
            }
        }

        $addressString = implode(', ', $addressParts);

        $subject = "Product List Received from {$customerName}";

        if (!empty($addressString)) {
            $subject .= " - {$addressString}";
        }

        $subject .= " - Oreva Selection";

        return $this->subject($subject)
            ->view('emails.order_confirmation')
            ->with('orderData', $this->orderData)
            ->attachData($this->pdf, "invoice_{$this->orderData['list']->id}.pdf");
    }
}
