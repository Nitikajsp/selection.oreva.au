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
        return $this->view('emails.order_confirmation')
                    ->with('orderData', $this->orderData)
                    ->attachData($this->pdf, "invoice_{$this->orderData['list']['id']}.pdf");
    }
}
