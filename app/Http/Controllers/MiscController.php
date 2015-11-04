<?php
namespace CBEDataService\Http\Controllers;

use Illuminate\Http\Request;

class MiscController extends ApiController
{

  public function catchall1($cmd, Request $request) {
    $message = "Unknown API request: " . $request->path();
    \Log::info($message);
    return $this->respondNotFound($message);
  }
  public function catchall2($cmd1, $cmd2, Request $request) {
    $message = "Unknown API request: " . $request->path();
    \Log::info($message);
    return $this->respondNotFound($message);
  }
  public function catchall3($cmd1, $cmd2, $cmd3, Request $request) {
    $message = "Unknown API request: " . $request->path();
    \Log::info($message);
    return $this->respondNotFound($message);
  }

}