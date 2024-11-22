<!DOCTYPE html>
<html>
<head>
    <title>تحديث على موعدك</title>
</head>
    <body>
        <h1>تم تحديث موعدك</h1>
        <p>مرحبًا {{ $appointment->booked_appointment->patient->name }},</p>
        <p>لقد تم تعديل موعدك مع الدكتور {{ $appointment->booked_appointment->doctor->name }}.</p>
        <p><strong>التاريخ الجديد:</strong> {{ $appointment->date }}</p>
        <p><strong>الوقت الجديد:</strong> {{ $appointment->time }}</p>
        <p>يرجى التأكد من الوقت الجديد والاستعداد قبل الموعد بـ 10 دقائق.</p>
    </body>
</html>