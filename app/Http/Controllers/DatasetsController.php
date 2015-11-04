<?php
namespace CBEDataService\Http\Controllers;

use CBEDataService\Domain\Data\Dataset;
use Illuminate\Http\Request;

class DatasetsController extends ApiController
{
  public function index (Request $request)
  {
    $entityId = 3;
    $list = Dataset::listEntityDatasets($entityId);
    return $this->respondIndex("Success", $list);
  }
}
