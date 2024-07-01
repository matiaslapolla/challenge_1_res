<?php

namespace App\Services;

use App\Mail\BookingConfirmationMail;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Tour;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ExportBookingsToCsv;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class BookingService
{

	public function __construct()
	{
	}

	public function sendMailNotification(Booking $booking)
	{

		$email = $booking->customer_email;
		$tour_name = Tour::find($booking->tour_id)->name;
		$hotel_name = Hotel::find($booking->hotel_id)->name;
		$booking_date = $booking->booking_date;
		$number_of_people = $booking->number_of_people;
		$client_name = $booking->customer_name;

		$data = [
			'tour_name' => $tour_name,
			'hotel_name' => $hotel_name,
			'booking_date' => $booking_date,
			'number_of_people' => $number_of_people,
			'client_name' => $client_name,
			'customer_email' => $email
		];

		Mail::to($data['customer_email'])->send(new BookingConfirmationMail(
			$data['tour_name'],
			$data['hotel_name'],
			$data['client_name'],
			$data['customer_email'],
			$data['number_of_people'],
			$data['booking_date']
		));
	}

	public function exportAllBookings()
	{
		ExportBookingsToCsv::dispatch();
	}

	public function handleBookingExport()
	{
		try {
			$bookings = Booking::all();
			if ($bookings->count() == 0) {
				return 'No bookings found';
			}
			$filePath = $this->buildBookingsCsv($bookings);
			Storage::disk('local')->put('exported_files.csv', $filePath);
		} catch (\Throwable $th) {
			echo $th->getMessage();
			throw $th;
		}
	}

	public function buildBookingsCsv($bookings)
	{
		try {
			$fileName = 'bookings_' . date('Ymd_His') . '.csv';
			$filePath = 'exports/' . $fileName;
	
			$file = fopen(storage_path('app/' . $filePath), 'w');
	
			fputcsv($file, ['Booking ID', 'Tour ID', 'Tour Name', 'Hotel ID', 'Hotel Name', 'Customer Name', 'Customer Email', 'Number of People', 'Booking Date']);
	
			foreach ($bookings as $booking) {
				fputcsv($file, [
					$booking->id,
					$booking->tour_id,
					$booking->tour->name,
					$booking->hotel_id,
					$booking->hotel->name,
					$booking->customer_name,
					$booking->customer_email,
					$booking->number_of_people,
					$booking->booking_date,
				]);
			}
	
			fclose($file);
	
			return $filePath;
		} catch (\Throwable $th) {
			throw $th;
		}

	}
}
