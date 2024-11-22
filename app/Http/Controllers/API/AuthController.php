<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Traits\Api_Response_Trait;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    use Api_Response_Trait;

    public function register_doctor(Request $request)
    {
        $request->validate([

            'name' => 'required|string|min:3|max:30',
            'age' =>  'required|Integer|min:1|max:90',
            'phone' => 'required|numeric|digits:10',
            'email' => 'required|email|max:50|unique:doctors,email',
            'governorate' => 'required|string|min:2|max:50',
            'specialty' => 'required|string',
            'academic_certificates' => 'required|string|min:4|max:50',
            'hospital' => 'required|string|min:4|max:50',
            'experience' => 'required|string|min:4|max:500',
            'clinic_location' => 'required|string|min:4|max:50',
            'password' => 'required|string|size:8|Alpha_dash',
        ]);

        try{
            $doctor_data=[
                'name'=>$request->name,
                'age'=>$request->age,
                'phone'=>$request->phone,
                'email'=>$request->email,
                'governorate'=>$request->governorate,
                'specialty_id'=>$request->specialty,
                'academic_certificates'=>$request->academic_certificates,
                'hospital'=>$request->hospital,
                'experience'=>$request->experience,
                'clinic_location'=>$request->clinic_location,
            ];
            $doctor_create = Doctor::create($doctor_data);
        
            $doctor_account = Doctor_Account::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'doctor_id' => $doctor_create->id,
            ]);
    
            if (!$doctor_create || !$doctor_account) 
            {
                return $this->api_response(
                    'False',
                    "The doctor's account has not been created.",
                    null,
                    400 
                );
            } 
    
            //return redirect()->route('admin.dashboard')->with('success', 'Doctor Account created successfully.');
            return $this->api_response(
                'True',
                'Doctor Account created successfully.',
                null,
                201,
            );

        }
        catch(\Exception $erorr){
            
            return $this->api_response(
                'False',
                'An error occurred while add the doctor: ' . $erorr->getMessage(),
                null,
                500 // كود 500 يشير إلى خطأ في النظام
            );
        }
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        try{
            $credentials = $request->only('email', 'password');

            //$token = Auth::guard('admin')->attempt($credentials);
            if($token = Auth::guard('admin')->attempt($credentials))
            {
                $admin = Auth::guard('admin')->user();

                return $this->api_response(
                    'True',
                    'I have successfully logged in',
                    [
                      'Admin' => $admin,
                      'authorization' => [
                         'token' => $token,
                         'type' => 'bearer',
                        ]
                      ],
                    200
                );
                
            }
            elseif($token = Auth::guard('doctor')->attempt($credentials))
            {
                $doctor = Auth::guard('doctor')->user();
                return $this->api_response(
                    'True',
                    'I have successfully logged in',
                    [
                      'Doctor' => $doctor,
                      'authorization' => [
                         'token' => $token,
                         'type' => 'bearer',
                        ]
                    ],
                    200
                );
               
            }
            if (!$token) {
                return $this->api_response(
                    'False',
                    'Unauthorized',
                    null,
                    401
                );

            }
    
        }
        catch(\Exception $erorr){
            
            return $this->api_response(
                'False',
                'An error occurred while login: ' . $erorr->getMessage(),
                [],
                500 // كود 500 يشير إلى خطأ في النظام
            );
        }
       
    }
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:admins', // تغيير users إلى admins
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if($validator->fails() ){
    //         return response()->json($validator->errors()->toJson(), 401);
    //     }

    //     $user = Admin::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'message' => 'User created successfully',
    //         'user' => $user
    //     ]);
    // }

    public function logout()
    {
        try{
            if(Auth::guard('doctor')->check())
            {
                Auth::guard('doctor')->logout();
                return $this->api_response(
                    'True',
                    'Doctor Successfully logged out',
                    null,
                    200
                );
            }
            elseif(Auth::guard('admin')->check())
            {
                Auth::guard('admin')->logout();
                return $this->api_response(
                    'True',
                    'Admin Successfully logged out',
                    null,
                    200
                );
            }
            else
            {
                return $this->api_response(
                    'False',
                    'Not Successfully logged out',
                    null,
                    401
                );
            }
            //Auth::guard('admin')->logout();
            
        }
        catch(\Exception $erorr){
            
            return $this->api_response(
                'False',
                'An error occurred while logout: ' . $erorr->getMessage(),
                [],
                500 // كود 500 يشير إلى خطأ في النظام
            );
        }
        
    }
    
    
    
    public function refresh()
    {
        try{
            $newToken = JWTAuth::parseToken()->refresh();

            if(Auth::guard('doctor')->check())
            {
                return $this->api_response(
                    'True',
                    'I have successfully refresh token',
                    [
                    'Doctor' => Auth::guard('doctor')->user(),
                    'authorization' => [
                        'token' => $newToken,
                        'type' => 'bearer',
                        ]
                    ],
                    200
                );
                // return response()->json([
                //     'user' => Auth::guard('doctor')->user(),
                //     'authorization' => [
                //         'token' => $newToken,
                //         'type' => 'bearer',
                //     ]
                // ]);
            }
            elseif(Auth::guard('admin')->check())
            {
                return $this->api_response(
                    'True',
                    'I have successfully refresh token',
                    [
                    'Admin' => Auth::guard('admin')->user(),
                    'authorization' => [
                        'token' => $newToken,
                        'type' => 'bearer',
                        ]
                    ],
                    200
                );
                // return response()->json([
                //     'user' => Auth::guard('admin')->user(),
                //     'authorization' => [
                //         'token' => $newToken,
                //         'type' => 'bearer',
                //     ]
                // ]);
            }
            else
            {
                return $this->api_response(
                    'False',
                    'Not Successfully refresh tken',
                    null,
                    401
                );
            }
        }
        catch(\Exception $erorr){
            
            return $this->api_response(
                'False',
                'An error occurred while refresh tken: ' . $erorr->getMessage(),
                [],
                500 // كود 500 يشير إلى خطأ في النظام
            );
        }
   }
}
