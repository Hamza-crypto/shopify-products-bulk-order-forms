<?php

// app/Mail/ProductSelectionMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductSelectionMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Selected Products')
                    ->view('emails.product_selection')
                    ->attach($this->filePath);
    }
}