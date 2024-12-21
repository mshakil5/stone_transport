<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfUrl;

    public function __construct($order, $pdfUrl)
    {
        $this->order = $order;
        $this->pdfUrl = $pdfUrl;
    }

    public function build()
    {
        return $this->markdown('emails.orders.confirmation')
                    ->subject('Order Confirmation - ' . $this->order->invoice)
                    ->with([
                        'order' => $this->order,
                        'pdfUrl' => $this->pdfUrl,
                    ]);
    }
}