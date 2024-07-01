<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;


class BookingController extends Controller
{

    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('start_date')) {
            $query->where('booking_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('booking_date', '<=', $request->end_date);
        }

				if ($request->has('tour_name')) {
					$query->whereHas('tour', function ($query) use ($request) {
						$query->where('name', 'like', '%' . $request->tour_name . '%');
					});
				}

				if ($request->has('hotel_name')) {
					$query->whereHas('hotel', function ($query) use ($request) {
						$query->where('name', 'like', '%' . $request->hotel_name . '%');
					});
				}

				if ($request->has('customer_name')) {
						$query->where('customer_name', 'like', '%' . $request->customer_name . '%');
				}

				if ($request->has('sort')) { // sort should be 'asc' or 'desc' only, sorts by id
					$query->orderBy('id', $request->sort);
				}

        $bookings = $query->get();

        foreach ($bookings as $booking) {
            $booking->tour;
            $booking->hotel;
        }

        return response()->json($bookings, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'hotel_id' => 'required|exists:hotels,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'number_of_people' => 'required|integer|min:1',
            'booking_date' => 'required|date',
        ]);

        $booking = Booking::create($validatedData);

				$service = new BookingService();
				$booking = $service->sendMailNotification($booking);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        return response()->json($booking, 200);
    }

    public function update(Request $request, Booking $booking)
    {
        $validatedData = $request->validate([
            'tour_id' => 'sometimes|exists:tours,id',
            'hotel_id' => 'sometimes|exists:hotels,id',
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'number_of_people' => 'sometimes|integer|min:1',
            'booking_date' => 'sometimes|date',
        ]);

        $booking->update($validatedData);
        return response()->json($booking, 200);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(null, 204);
    }

		public function exportAllBookings (Request $request) {
			$service = new BookingService();
			$service->exportAllBookings();
		}

		public function cancelBooking(Request $request) {

			$id = $request->id;

			if (!$id) {
				return response()->json(['message' => 'ID is missing'], 400);
			}

			if (!is_numeric($id)) {
				return response()->json(['message' => 'ID must be a number'], 400);
			}

			$query = Booking::query();

			$query->where('id', $id);

			$bookings = $query->get();

			if ($bookings->isEmpty()) {
				return response()->json(['message' => 'Booking not found'], 404);
			}

			// set status as "cancelled"
			foreach ($bookings as $booking) {
				$booking->update(['status' => 'cancelled']);
			}

			return response()->json($bookings, 200);
		}
}
