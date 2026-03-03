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

        $list = $this->orderData['list'] ?? null;
        $addressParts = [];

        if ($list) {
            if (!empty($list->name)) {
                $addressParts[] = $list->name;
            }

            if (!empty($list->suburb)) {
                $addressParts[] = $list->suburb;
            }

            if (!empty($list->state)) {
                $addressParts[] = $list->state;
            }

            if (!empty($list->pincod)) {
                $addressParts[] = $list->pincod;
            }
        }

        $addressString = implode(', ', array_values(array_filter($addressParts, function ($v) {
            return $v !== null && $v !== '';
        })));

        $subject = "Product List Received from {$customerName}";

        if (!empty($addressString)) {
            $subject .= " - {$addressString}";
        }

        $subject .= " - Oreva Selection";

        return $this->subject($subject)
            ->view('emails.order_confirmation')
            ->with('orderData', $this->orderData)
            ->attachData($this->pdf, "Selection_Oreva_{$this->orderData['list']->id}.pdf");
    }
}
