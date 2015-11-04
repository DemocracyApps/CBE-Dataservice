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
      $ds = new Dataset($header[$nCategories + $iDataset], array(
        'year'            => $data['start_year'] + $iDataset,
        'type'            => $data['type'],
        'entity'          => $data['organization'],
        'entityId'        => $data['organization_id'],
        'datasource'      => $data['datasource_name'],
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

  public static function ProcessSimpleBudgetCSV ($data, $parameters)
  {
    $datasets = [];
    $header = $data[0];
    $nCat = $parameters['category_count'];
    for ($iy = 0; $iy<$parameters['year_count']; ++$iy) {
      $year = $parameters['start_year'] + $iy;
      $ds = new Dataset($parameters['organization'],$parameters['data_source'], $parameters['type'], $year, $header[$nCat + $iy]);
      $datasets[] = $ds;
    }
    $length = sizeof($data);
    for ($irow = 1; $irow < $length; ++$irow) {
      $row = $data[$irow];
      $categories = [];
      for ($icat=0; $icat < $nCat; ++$icat) {
        $categories[] = $row[$icat];
      }
      for ($icol = 0; $icol < $parameters['year_count']; ++$icol) {
        $ds = $datasets[$icol];
        $value = $row[$nCat + $icol];
        $ds->addValue($categories, $value);
      }
    }
    return $datasets;
  }
}