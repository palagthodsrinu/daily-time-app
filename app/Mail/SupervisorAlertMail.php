<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupervisorAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $hours;
    public $requiredHours;

    /*
     * Create a new message instance.
     */
    public function __construct($employee, $hours, $requiredHours)
    {
        $this->employee = $employee;
        $this->hours = $hours;
        $this->requiredHours = $requiredHours;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Employee Weekly Hours Alert')
                    ->view('emails.supervisor_alert');
    }
}
