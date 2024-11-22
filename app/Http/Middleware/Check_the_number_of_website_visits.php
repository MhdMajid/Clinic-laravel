<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class Check_the_number_of_website_visits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {       
            //today
            // الحصول على تاريخ اليوم الحالي
            $today = now()->toDateString();
            // تحديث عدد الزيارات في Cache
            Cache::increment("visits_today_{$today}");

            //week
            // الحصول على السنة الحالية ورقم الأسبوع الحالي
            $currentWeek = Carbon::now()->format('Y-W'); // Y للسنة و W لرقم الأسبوع
            // زيادة عدد الزيارات في ذاكرة التخزين المؤقتة
            Cache::increment("visits_week_{$currentWeek}");

            //Month
            // الحصول على الشهر الحالي والسنة الحالية
            $currentMonth = Carbon::now()->format('Y-m');
            // زيادة عدد الزيارات في ذاكرة التخزين المؤقتة
             Cache::increment("visits_month_{$currentMonth}");
        return $next($request);
    }
}
