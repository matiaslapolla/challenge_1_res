<!-- resources/views/emails/booking_confirmation.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $customer_name }},</p>
    <p>Thank you for booking with us! Here are your booking details:</p>
    <ul>
        <li><strong>Tour name:</strong> {{ $tour_name }}</li>
        <li><strong>Hotel name:</strong> {{ $hotel_name }}</li>
        <li><strong>Number of People:</strong> {{ $number_of_people }}</li>
        <li><strong>Booking Date:</strong> {{ $booking_date }}</li>
    </ul>
    <p>Best regards,</p>
    <p>Persiscal Challenge</p>
</body>
</html>
