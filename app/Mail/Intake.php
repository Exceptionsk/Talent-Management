<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Intake extends Mailable
{
    use Queueable, SerializesModels;
    public $form_link;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($form_link, $name)
    {
        $this->form_link = $form_link;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.intake');
    }
}
