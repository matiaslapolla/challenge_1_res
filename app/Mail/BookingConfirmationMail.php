<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tour_name;
    public $hotel_name;
    public $customer_name;
    public $customer_email;
    public $number_of_people;
    public $booking_date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tour_name, $hotel_name, $customer_name, $customer_email, $number_of_people, $booking_date)
    {
        $this->tour_name = $tour_name;
        $this->hotel_name = $hotel_name;
        $this->customer_name = $customer_name;
        $this->customer_email = $customer_email;
        $this->number_of_people = $number_of_people;
        $this->booking_date = $booking_date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.booking_confirmation')
                    ->subject('Booking Confirmation')
                    ->with([
                        'tour_name' => $this->tour_name,
                        'hotel_name' => $this->hotel_name,
                        'customer_name' => $this->customer_name,
                        'customer_email' => $this->customer_email,
                        'number_of_people' => $this->number_of_people,
                        'booking_date' => $this->booking_date,
                    ]);
    }
}
