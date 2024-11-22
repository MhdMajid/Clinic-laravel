<?php

namespace App\Traits;

trait Api_Response_Trait
{
    public function api_response($success= null ,$message=null ,$data=null ,$status=200)
    {
        
        $array= [ 
           'success' =>$success ,
           'message' =>$message ,
           'data' =>$data ,
        ];

        return response()->json($array,$status);
    }
}