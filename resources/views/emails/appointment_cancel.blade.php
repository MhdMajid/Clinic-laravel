<!DOCTYPE html>
<html>
<head>
    <title>الغاء حجز الموعد</title>
</head>
    <body>
        <h1>تم الغاء موعدك</h1>
        <p>مرحبًا {{ $booked_appointment->patient->name }},</p>
        <p>لقد تم الغاء حجز موعدك مع الدكتور {{ $booked_appointment->doctor->name }}.</p>
        <p><strong>تاريخ الموعد:</strong> {{ $booked_appointment->appointment->date }}</p>
        <p><strong>الوقت:</strong> {{ $booked_appointment->appointment->time }}</p>
        <p>نعتذر من حضرتك لالغاء الموعد
            نأكد لك إنه تم الغاء الموعد لأسباب ضرورية بسبب انشغال الدكتور أو تغيير برنامج مواعيده
        </p>
        <p>في حال تريد حجز موعد أخر يرجى زيارة موقعنا الالكتروني</p>
    </body>
</html>