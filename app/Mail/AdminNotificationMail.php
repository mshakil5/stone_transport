<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $order;

    /**
     * Create a new message instance.
     *
     * @param  string  $message
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct($message, Order $order)
    {
        $this->message = $message;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.notification')
                    ->with([
                        'message' => $this->message,
                        'order' => $this->order,
                    ])
                    ->subject('New Message Regarding Invoice #'.$this->order->invoice);
    }
}