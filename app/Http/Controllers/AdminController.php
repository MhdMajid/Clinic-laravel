<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllAppointmentsResources;
use App\Http\Resources\AllPatientFilesResources;
use App\Http\Resources\DoctorsResource;
use App\Models\Appointment;
use App\Models\Booked_Appointment;
use App\Models\Doctor;
use App\Models\Completed_Appointment;
use App\Models\Patient;
use App\Models\Patient_File;
use App\Models\Patient_Health_Information;
use App\Models\Specialty;
use App\Traits\Api_Response_Trait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\VarDumper\Caster\DoctrineCaster;
use App\Http\Resources\AppointmentsCountPerDoctorResources;
use App\Http\Resources\topSpecialtiesResources;
use App\Http\Resources\topDoctorsResources;

class AdminController extends Controller
{
  use Api_Response_Trait;

  public function Dashboard(Request $request)
  {
    try {
       
        $today = now()->toDateString();

        
        $Todayvisits = Cache::get("visits_today_{$today}", 0);
        // $TodayCreatedAppointments = Cache::get("appointments_created_today_{$today}", 0);
        // $TodayBookedAppointments = Cache::get("appointments_booked_today_{$today}", 0);
        // $TodayCompletedAppointments = Cache::get("appointments_expired_today_{$today}", 0);

        $TodayCreatedAppointments = Appointment::whereDate('created_at',$today)->count();
        $TodayBookedAppointments = Booked_Appointment::whereDate('created_at', $today)->count();
        $TodayCompletedAppointments = Completed_Appointment::whereDate('created_at', $today)->count();

        
        $TodayAppointmentsCountPerDoctor = Doctor::withCount([
            'booked_appointment' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            },
            'completed_appointment' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            }
        ])->paginate(10);

       
        $topSpecialties = Specialty::withCount([
            'doctor as booked_appointment_count' => function ($query) use ($today) {
                $query->whereHas('booked_appointment', function ($subQuery) use ($today) {
                    $subQuery->whereDate('created_at', $today);
                });
            },
            'doctor as completed_appointment_count' => function ($query) use ($today) {
                $query->whereHas('completed_appointment', function ($subQuery) use ($today) {
                    $subQuery->whereDate('created_at', $today);
                });
            }
        ])->orderByDesc('booked_appointment_count')
          ->orderByDesc('completed_appointment_count')
          ->take(5)
          ->get();

       
        $topDoctors = Doctor::withCount([
            'booked_appointment' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            },
            'completed_appointment' => function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            }
        ])->orderByDesc('booked_appointment_count')
          ->orderByDesc('completed_appointment_count')
          ->take(5)
          ->get();

        return $this->api_response(
            'True',
            'The data has been restored successfully',
            [
                'today' => $today,
                'Today visits' => $Todayvisits,
                'Today Created Appointments' => $TodayCreatedAppointments,
                'Today Booked Appointments' => $TodayBookedAppointments,
                'Today Completed Appointments' => $TodayCompletedAppointments,
                'today Appointments Count PerDoctor' => AppointmentsCountPerDoctorResources::collection($TodayAppointmentsCountPerDoctor) ,
                'top Specialties' => topSpecialtiesResources::collection($topSpecialties),
                'top Doctors' => topDoctorsResources::collection($topDoctors),
            ],
            200
        );
    } catch (\Exception $error) {
        // تسجيل الخطأ في ملفات النظام
        Log::error('Error in dashboard: ' . $error->getMessage());

        return $this->api_response(
            'False',
            'An error occurred while displaying the dashboard: ' . $error->getMessage(),
            null,
            500
        );
    }
  }
  public function WeeklyReport(Request $request)
  {
      try {
          $currentWeek = Carbon::now()->format('Y-W');
          $weeklyData = $this->getReportData('week', $currentWeek, now()->startOfWeek(), now()->endOfWeek());

          return $this->api_response(
              'True',
              'Weekly report data retrieved successfully',
              $weeklyData,
              200
          );
      } catch (\Exception $error) {
          Log::error('Error in Weekly Report: ' . $error->getMessage());
          return $this->api_response(
              'False',
              'An error occurred while generating the weekly report: ' . $error->getMessage(),
              null,
              500
          );
      }
  }

  public function MonthlyReport(Request $request)
  {
    try{
            $currentMonth = Carbon::now()->format('Y-m');
            $monthlyData = $this->getReportData('month', $currentMonth, now()->startOfMonth(), now()->endOfMonth());

            return $this->api_response(
                'True',
                'Monthly report data retrieved successfully',
                $monthlyData,
                200
            );
    } catch (\Exception $error) {
            Log::error('Error in Monthly Report: ' . $error->getMessage());
            return $this->api_response(
                'False',
                'An error occurred while generating the monthly report: ' . $error->getMessage(),
                null,
                500
            );
    }
  }

 
  private function getReportData($period, $cacheKey, $startDate, $endDate)
  {
      $visits = Cache::get("visits_{$period}_{$cacheKey}", 0);

      $createdAppointments = Appointment::whereBetween('created_at', [$startDate, $endDate])->count();
      $bookedAppointments = Booked_Appointment::whereBetween('created_at', [$startDate, $endDate])->count();
      $completedAppointments = Completed_Appointment::whereBetween('created_at', [$startDate, $endDate])->count();

      $bookedCompletedPerDoctor = Doctor::withCount([
          'booked_appointment' => function ($query) use ($startDate, $endDate) {
              $query->whereBetween('created_at', [$startDate, $endDate]);
          },
          'completed_appointment' => function ($query) use ($startDate, $endDate) {
              $query->whereBetween('created_at', [$startDate, $endDate]);
          }
      ])->paginate(10);

      $topSpecialties = Specialty::withCount([
          'doctor as booked_appointment_count' => function ($query) use ($startDate, $endDate) {
              $query->whereHas('booked_appointment', function ($subQuery) use ($startDate, $endDate) {
                  $subQuery->whereBetween('created_at', [$startDate, $endDate]);
              });
          },
          'doctor as completed_appointment_count' => function ($query) use ($startDate, $endDate) {
              $query->whereHas('completed_appointment', function ($subQuery) use ($startDate, $endDate) {
                  $subQuery->whereBetween('created_at', [$startDate, $endDate]);
              });
          }
      ])->orderByDesc('booked_appointment_count')
        ->orderByDesc('completed_appointment_count')
        ->take(3)
        ->get();

      $topDoctors = Doctor::withCount([
          'booked_appointment' => function ($query) use ($startDate, $endDate) {
              $query->whereBetween('created_at', [$startDate, $endDate]);
          },
          'completed_appointment' => function ($query) use ($startDate, $endDate) {
              $query->whereBetween('created_at', [$startDate, $endDate]);
          }
      ])->orderByDesc('booked_appointment_count')
        ->orderByDesc('completed_appointment_count')
        ->take(3)
        ->get();

      return [
          'startDate' => $startDate->toDateString(),
          'endDate' => $endDate->toDateString(),
          'visits' => $visits,
          'createdAppointments' => $createdAppointments,
          'bookedAppointments' => $bookedAppointments,
          'completedAppointments' => $completedAppointments,
          'bookedCompletedPerDoctor' => AppointmentsCountPerDoctorResources::collection($bookedCompletedPerDoctor),
          'topSpecialties' => topSpecialtiesResources::collection($topSpecialties),
          'topDoctors' => topDoctorsResources::collection($topDoctors),

      ];
  } 

  

  public function SearchAppointment(Request $request)
  {
        $request->validate([
            "search"=> "required|string|min:3|max:50"
        ]);
        try {
            $search = $request->search;
            $appointments = Appointment::withTrashed()->where(function($query) use ($search) {
                  $query->whereDate('date', 'like', '%' . $search . '%')
                        ->orWhereTime('time', 'like', '%' . $search . '%');
              })
              ->orWhereHas('Doctor', function($query) use ($search) {
                  $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('governorate', 'like', '%' . $search . '%'); 
              })
              ->paginate(10);
  
          if ($appointments->isEmpty()) {
              return $this->api_response(
                  'False',
                  'There are no results matching your search.',
                  null,
                  404 
              );
          }
  
          return $this->api_response(
              'True',
              'The data has been retrieved successfully',
              [
                  'Appointments' => AllAppointmentsResources::collection($appointments),
                  'Search' => $search
              ],
              200
          ); 
      } catch (\Exception $e) {
          return $this->api_response(
              'False',
              'An error occurred while searching for appointments: ' . $e->getMessage(),
              null,
              500
          );
      }
  }
  public function FilterByStatus(Request $request)
  {
    $request->validate([
        "filter"=> "required|string|min:1|max:50"
      ]);
    try {
        $filter = $request->input('filter');


        $appointments = Appointment::withTrashed()->where('status', 'like', $filter)->paginate(10);


        if ($appointments->isEmpty()) {
            return $this->api_response(
                'False',
                'There are no results matching your search.',
                null,
                404 
            );
        }

        return $this->api_response(
            'True',
            'The data has been retrieved successfully',
            [
                'Appointments' => AllAppointmentsResources::collection($appointments),
                'Filter' => $filter
            ],
            200
        ); 
    } catch (\Exception $e) {
        return $this->api_response(
            'False',
            'An error occurred while searching by status: ' . $e->getMessage(),
            null,
            500
        );
    }
  }

  public function SearchDoctor(Request $request)
  {
    $request->validate([
        "search"=> "required|string|min:3|max:50"
    ]);
    try{
      $search = $request->search;
      $doctors = Doctor::withTrashed()->where(function ($query) use ($search) {
          $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('academic_certificates', 'like', '%' . $search . '%')
                ->orWhere('experience', 'like', '%' . $search . '%')
                ->orWhere('hospital', 'like', '%' . $search . '%')
                ->orWhere('clinic_location', 'like', '%' . $search . '%');
      })->paginate(10); 

      if ($doctors->isEmpty()) 
      {
         return $this->api_response(
            'False',
            'There are no results matching your search.',
            null,
            404 
          );
      }
  
      return $this->api_response(
        'True',
        'The data has been restored successfully',
        [
          'Doctors' =>DoctorsResource::collection($doctors),
          'Search' =>$search
        ],
        200,
      ); 
    }catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while searching by doctors: ' . $e->getMessage(),
          null,
          500
      );
    }
    
  }


  public function FilterByGovernorate(Request $request)
  {
    $request->validate([
        "filter"=> "required|string|min:3|max:50"
      ]);

    try{

      $filter = $request->input('filter');

      $doctors = Doctor::withTrashed()->where('governorate', 'like', $filter)->paginate(10);
     
      if ($doctors->isEmpty()) 
      {
         return $this->api_response(
            'False',
            'There are no results matching your search.',
            null,
            404 
          );
      }
  
      return $this->api_response(
        'True',
        'The data has been restored successfully',
        [
          'Doctors' =>DoctorsResource::collection($doctors),
          'Filter' =>$filter
        ],
        200,
      ); 
    }
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while searching by governorate: ' . $e->getMessage(),
          null,
          500
      );
    }  
  
  }
  public function SearchPatientFiles(Request $request)
  {
    $request->validate([
        "search"=> "required|string|min:3|max:50"
    ]);
    try{
      $search = $request->search;
      $patient_files = Patient_File::withTrashed()
  
      ->orWhereHas('Patient',function($query) use ($search) {
                  $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%'. $search . '%'); 
              
        })
        ->orWhereHas('doctor',function($query) use ($search) {
                  $query->where('name', 'like', '%' . $search . '%') ; 
      
          })
        ->paginate(10);
  
        if ($patient_files->isEmpty()) 
        {
           return $this->api_response(
              'False',
              'There are no results matching your search.',
              null,
              404 
            );
        }

      return $this->api_response(
        'True',
        'The data has been restored successfully',
        [
          'Patient Files' =>AllPatientFilesResources::collection($patient_files),
          'Search' =>$search
        ],
        200,
        ); 
    }catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while searching by patient file: ' . $e->getMessage(),
          null,
          500
      );
    }  
   
  }


  public function FilterByDate(Request $request)
   {
        $request->validate([
            "filter"=> "required|string|min:3|max:50"
        ]);

        try{
            $filter= $request->input('filter');

            switch($filter){
                case 'Last 5 Dayago':
                    $fiveDaysAgo = Carbon::now()->subDays(5);
                    $patient_files = Patient_File::withTrashed()->whereDate('created_at', '>=', $fiveDaysAgo)->paginate(10);
                    $filter="Last 5 Dayago"; 
                    break;
                case 'Last 10  Dayago':
                    $teneDaysAgo = Carbon::now()->subDays(10);
                    $patient_files = Patient_File::withTrashed()->whereDate('created_at', '>=', $teneDaysAgo)->paginate(10);
                    $filter="Last 10  Dayago"; 
                    break;    
                case 'Last 30 Dayago':
                    $monthDaysAgo = Carbon::now()->subDays(30);
                    $patient_files = Patient_File::withTrashed()->whereDate('created_at', '>=', $monthDaysAgo)->paginate(10);
                    $filter="Last 30 Dayago"; 
                    break;   
                default:
                    return $this->api_response(
                      'False',
                      'Invalid status provided.',
                      null,
                      400
                    );    
            }    
     
            if ($patient_files->isEmpty()) 
            {
                return $this->api_response(
                    'False',
                    'There are no results matching your search.',
                    null,
                    404 
                );
            }
            return $this->api_response(
                'True',
                'The data has been restored successfully',
                [
                    'Patient Files' =>AllPatientFilesResources::collection($patient_files),
                    'Filter' =>$filter
                ],
                200,
            ); 

        }
        catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while searching by date: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
   
}
