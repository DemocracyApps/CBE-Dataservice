<?php namespace CBEDataService\Domain\Data;


class CSVProcessor
{

  public static function processSimpleBudget($data)
  {
    $datasets = [];
    $nCategories = $data['category_count'];
    $fileData = $data['fileData'];
    $header = $fileData[0];
    for ($iDataset = 0; $iDataset<$data['year_count']; ++$iDataset) {
      \Log::info("Create dataset " . $iDataset . ": " . $header[$nCategories + $iDataset]);
      $ds = new Dataset($header[$nCategories + $iDataset], array(
        'year'            => $data['start_year'] + $iDataset,
        'type'            => $data['type'],
        'entity'          => $data['organization'],
        'entityId'        => $data['organization_id'],
        'datasourceId'    => $data['datasource_id']
        )
      );
      $ds->initializeCategories(array_slice($header, 0, $nCategories));
      $datasets[] = $ds;
    }
    $rows = $data['fileData'];
    for ($irow = 1; $irow < sizeof($rows); ++$irow) {
      $row = $rows[$irow];
      $categories = [];
      for ($icat=0; $icat < $nCategories; ++$icat) {
        $categories[] = $row[$icat];
      }
      for ($icol = 0; $icol < $data['year_count']; ++$icol) {
        $ds = $datasets[$icol];
        $value = $row[$nCategories + $icol];
        $ds->addValue($categories, $value);
      }
    }

    for ($iDataset = 0; $iDataset < sizeof($datasets); ++$iDataset) {
      $datasets[$iDataset]->save();
    }
    return $datasets;
  }
}