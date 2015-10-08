<?php namespace CBEDataService\Domain\Data;


class Dataset
{
  protected static $tablename = "datasets";

  public $id = -1;
  public $organization = -1;
  public $datasource = -1;
  public $type = null;
  public $year = -1;
  public $name = null;
  public $values = null;

  public function __construct($org, $datasource, $type, $year, $name)
  {
    $this->organization = $org;
    $this->datasource = $datasource;
    $this->type = $type;
    $this->year = $year;
    $this->name = $name;
  }

  public function addValue($categories, $value)
  {
    if ($this->values == null) $this->values = [];
    $d = [
      'categories' => $categories,
      'value' => $value
    ];
    $this->values[] = $d;
  }

  public function save ()
  {
    if ($this->id < 0) {
      $this->id = app('db')->table(self::$tablename)->insertGetId([
        'organization'  => $this->organization,
        'datasource'    => $this->datasource,
        'type'          => $this->type,
        'year'          => $this->year,
        'name'          => $this->name,
        'created_at'    => date('Y-m-d H:i:s'),
        'updated_at'    => date('Y-m-d H:i:s')
      ]);
    }
    else {
      app('db')->table(self::$tablename)->update([
        'organization'  => $this->organization,
        'datasource'    => $this->datasource,
        'type'          => $this->type,
        'year'          => $this->year,
        'name'          => $this->name,
        'updated_at'    => date('Y-m-d H:i:s')
      ]);
    }
    $msg = "OK";
    if ($this->values != null) {
      $dir = getenv('DATA_DIRECTORY');
      $dsPath = $dir . "/" . $this->organization . "/" . $this->id;
      $msg = $this->checkOrCreateDataDirectory($dsPath);
      \Log::info("We'll write it to " . $dsPath . "/data");
      file_put_contents($dsPath . "/data", json_encode($this->values));
    }
  }

  protected function checkOrCreateDataDirectory($dsPath)
  {
    $msg = "";
    $msg .= "Try to create directory " . $dsPath;
    \Log::info($msg);
    if (!file_exists($dsPath)) {
      mkdir($dsPath, 0777, true);
      $msg .= PHP_EOL . "Created directory " . $dsPath;
    }
    return $msg;
  }
}