<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlaceOrder extends Mailable
{
    use Queueable, SerializesModels;

    protected $order_id;
    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $order_id = $this->order_id;
        //dd($this->order_id);
        return $this->view('email-templates.place_order_mail_x', ['order_id' => $order_id]);
    }
}
