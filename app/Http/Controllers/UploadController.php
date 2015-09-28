<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class UploadController extends Controller
{

  public function upload(Request $request) {
    \Log::info("In the data server at the doit path");

    $val = $request->get('type');
    return $val;
    echo "The request ip is " . $request->ip() . PHP_EOL;

    echo "The request otherwise is " . json_encode($request->all()) . PHP_EOL;
    return json_encode($request->ip());
  }

}