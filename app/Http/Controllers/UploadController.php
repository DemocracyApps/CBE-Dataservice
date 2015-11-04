<?php
namespace CBEDataService\Http\Controllers;

use CBEDataService\Domain\Data\CSVProcessor;
use Illuminate\Http\Request;

class UploadController extends ApiController
{

  public function upload(Request $request) {

    $datasets = CSVProcessor::ProcessSimpleBudget($request->all());
    foreach ($datasets as $ds) {
      $ds->save();
    }
    return $this->respondOK(sizeof($datasets) . " datasets created.");
  }
}