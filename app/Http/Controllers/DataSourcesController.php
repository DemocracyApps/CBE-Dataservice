<?php
namespace CBEDataService\Http\Controllers;

use CBEDataService\Domain\Data\CSVProcessor;
use Illuminate\Http\Request;

class DataSourcesController extends ApiController
{

    public function register(Request $request) {
        \Log::info("The request is " . json_encode($request));
        return $this->respondOK("Got your request");
    }
}
