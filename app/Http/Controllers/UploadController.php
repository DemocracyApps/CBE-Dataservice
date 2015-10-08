<?php


namespace CBEDataService\Http\Controllers;


use CBEDataService\Domain\Data\CSVProcessor;
use Illuminate\Http\Request;

class UploadController extends Controller
{

  public function upload(Request $request) {
    \Log::info("In the data server at the doit path");

    $datasets = CSVProcessor::ProcessSimpleBudgetCSV($request->get('fileData'), $request->all());
    foreach ($datasets as $ds) {
      $ds->save();
    }
    return "Processed total datasets: " . sizeof($datasets);
  }

}