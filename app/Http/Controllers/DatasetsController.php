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

  private function parseIds($idList)
  {
    $idSet = [];
    $all = explode(',',$idList);
    foreach ($all as $item) {
      $rangeEnds = explode('-', $item);
      if (sizeof($rangeEnds) == 1) {
        $idSet[] = $item;
      }
      else {
        for ($i = $rangeEnds[0]; $i<=$rangeEnds[1]; ++$i) {
          $idSet[] = $i;
        }
      }
    }
    return $idSet;
  }

  public function show($id, Request $request)
  {
    $idSet = $this->parseIds($id);
    $datasetList = array();
    foreach ($idSet as $id) {
      $dataset = Dataset::getDataset($id);
      if ($dataset == null) return $this->respondNotFound('No such dataset ' . $id);
      $datasetList[] = $dataset;
    }
    return $this->respondItem('Datasets ' . json_encode($idSet), $datasetList);
  }
}
