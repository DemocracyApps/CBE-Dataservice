<?php namespace CBEDataService\Domain\Data;


class CSVProcessor
{

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