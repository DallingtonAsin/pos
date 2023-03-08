<?php

namespace App\Http\Controllers;
use App\Models\RequestResponse;
use Illuminate\Http\Request;

class LogAfterRequest extends Controller
{
   
    public static function LogRequest(Request $request, $responseArr)
    {
     
        $r = new RequestResponse;
        $r->request = json_encode($request->all());
        $r->response = json_encode($responseArr);
        $r->method = $request->method().":".$responseArr["method"];
        $r->url = $request->fullUrl();
        $r->ip_address = $request->ip();
        $r->save();

    }
}
