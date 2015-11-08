<?php namespace CBEDataService\Domain\Data;

class Dataset
{
  const DATASET_OUTPUT_VERSION = "1.0.0";
  protected static $tablename = "datasets";

  public $id = -1;
  public $organization = -1;
  public $datasource = -1;
  public $type = null;
  public $year = -1;
  public $name = null;

  private $counter = 0;
  public $categories = null;
  public $values = null;


  public function __construct($name, $params) //$org, $datasource, $type, $year, $name)
  {
    $this->name         = $name;
    $this->year         = $params['year'];
    $this->type         = $params['type'];
    $this->entity       = $params['entity'];
    $this->entityId     = $params['entityId'];
    $this->datasource   = $params['datasource'];
    $this->datasourceId = $params['datasourceId'];

    $this->categories = [];
    $this->counter = 0;
    $this->values = [];
  }

  public function initializeCategories($categories)
  {
    $this->categories = [];
    for ($i=0; $i<sizeof($categories); ++$i) {
      $this->categories[] = array(
        'name' => $categories[$i],
        'currentId' => 0,
        'values' => []
      );
    }
  }

  private function getCategoryMap()
  {
    $categoriesById = [];
    for ($i=0; $i<sizeof($this->categories); ++$i) {
      $values = [];
      foreach($this->categories[$i]['values'] as $key => $id) {
        $values[$id] = $key;
      }
      $categoriesById[] = array(
        'name' => $this->categories[$i]['name'],
        'values' => $values
      );
    }
    return $categoriesById;
  }

  private function getFileOutput()
  {
    // We need to save the metadata, the categories (flipped to by id), and the data rows
    $output = [];
    $output['version']      = self::DATASET_OUTPUT_VERSION;
    $output['name']         = $this->name;
    $output['id']           = $this->id;
    $output['year']         = $this->year;
    $output['type']         = $this->type;
    $output['entity']       = $this->entity;
    $output['entityId']     = $this->entityId;
    $output['datasource']   = $this->datasource;
    $output['datasourceId'] = $this->datasourceId;
    $output['taxonomy']     = $this->getCategoryMap();
    $output['values']       = $this->values;
    return $output;
  }

  private function addCategories($categories)
  {
    if (sizeof($categories) > sizeof($this->categories)) {
      throw new \Exception("Invalid number of categories " . json_encode($categories));
    }

    $refs = [];
    for ($i = 0; $i < sizeof($categories); ++$i) {
      $id = -1;
      if (!array_key_exists($categories[$i], $this->categories[$i]['values'])) {
        $id = $this->categories[$i]['currentId']++;
        $this->categories[$i]['values'][$categories[$i]] = $id;
      }
      else {
        $id = $this->categories[$i]['values'][$categories[$i]];
      }
      $refs[] = $id;
    }
    return $refs;
  }

  public function addValue($categories, $value)
  {
    $refs = $this->addCategories($categories);
    $dataItem = [
      'categories' => $refs,
      'value' => $value
    ];
    $this->values[] = $dataItem;
  }

  public static function listEntityDatasets($entityId) {
    $sel = "select id,name,type,year,datasource_id,updated_at from " . self::$tablename . " WHERE entity_id = " . $entityId;
    $sel .= " order by id";
    $list = app('db')
            ->select($sel);
    return $list;
  }

  public static function getDataset($id)
  {
    $dataset = null;
    $sel = "select id,name,entity_id from " . self::$tablename . " WHERE id = " . $id;
    $list = app('db')
            ->select($sel);
    if (sizeof($list) > 0) {
      $dataset = self::loadDataset($list[0]->entity_id, $id);
    }
    return $dataset;
  }

  private static function loadDataset($entityId, $dsId)
  {
    $data = null;
    $dir = getenv('DATA_DIRECTORY');
    $dsPath = $dir . "/" . $entityId . "/" . $dsId . "/data";
    if (file_exists($dsPath)) {
      $contents = file_get_contents($dsPath);
      if ($contents) {
        $data = json_decode($contents, true);
      }
    }
    return $data;
  }

  public function save ()
  {
    if ($this->id < 0) {
      $this->id = app('db')->table(self::$tablename)->insertGetId([
        'name'          => $this->name,
        'year'          => $this->year,
        'type'          => $this->type,
        'entity'        => $this->entity,
        'entity_id'     => $this->entityId,
        'datasource'    => $this->datasource,
        'datasource_id' => $this->datasourceId,
        'created_at'    => date('Y-m-d H:i:s'),
        'updated_at'    => date('Y-m-d H:i:s')
      ]);
    }
    else {
      app('db')->table(self::$tablename)->where(['id' => $this->id])->update([
        'name'          => $this->name,
        'year'          => $this->year,
        'type'          => $this->type,
        'entity'        => $this->entity,
        'entity_id'     => $this->entityId,
        'datasource'    => $this->datasource,
        'datasource_id' => $this->datasourceId,
        'updated_at'    => date('Y-m-d H:i:s')
      ]);
    }
    $msg = "OK";
    if ($this->values != null) {
      $dir = getenv('DATA_DIRECTORY');
      $dsPath = $dir . "/" . $this->entityId . "/" . $this->id;
      $msg = $this->checkOrCreateDataDirectory($dsPath);
      file_put_contents($dsPath . "/data", json_encode($this->getFileOutput()));
    }
  }

  protected function checkOrCreateDataDirectory($dsPath)
  {
    $msg = "";
    $msg .= "Try to create directory " . $dsPath;
    if (!file_exists($dsPath)) {
      mkdir($dsPath, 0777, true);
    }
    return $msg;
  }
}