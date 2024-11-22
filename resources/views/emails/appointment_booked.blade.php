<!DOCTYPE html>
<html>
<head>
    <title>تأكيد حجز موعدك</title>
</head>
    <body>
        <h1>تم حجز موعد جديد</h1>
        <p>مرحبًا {{ $appointment->booked_appointment->patient->name }},</p>
        <p>لقد تم تأكيد حجز موعدك مع الدكتور {{ $appointment->booked_appointment->doctor->name }}.</p>
        <p><strong>تاريخ الموعد:</strong> {{ $appointment->date }}</p>
        <p><strong>الوقت:</strong> {{ $appointment->time }}</p>
        <p><strong>العنوان:</strong>{{ $appointment->booked_appointment->doctor->clinic_location }}.</p>
        <p>يرجى الحضور قبل الموعد بـ 10 دقائق.</p>
    </body>
</html>