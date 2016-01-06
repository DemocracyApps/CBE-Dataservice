<?php namespace CBEDataService\Domain\Data;

use CBEDataService\Domain\Fetch\FetchTask;

class DataSource 
{
    protected static $tablename = "data_sources";

    public $id = -1;
    public $name = null;
    public $status = null;
    public $last = null;
    public $lastDate = null;
    public $sourceType = null;
    public $description = null;
    public $entityId = -1;
    public $entity = null;
    public $dataFormat = null;
    public $endpoint = null;
    public $apiFormat = null;
    public $frequency = null;
    public $properties = null;

    public function __construct() 
    {
        $this->status       = "inactive";
        $this->frequency    = "ondemand";
    }

    public function setValue ($key, $value, $save = false) 
    {
        if (! property_exists('CBEDataService\Domain\Data\DataSource', $key) ||
            in_array($key, array('id', 'last', 'lastDate'))) {
            throw new \Exception("Attempt to set invalid DataSource property $key");
        }
        $this->{$key} = $value;
        if ($save) $this->save();
    }

    public function activate()
    {
        if ($this->frequency != "ondemand") {
            $ft = new FetchTask();
            $ft->dataSource = $this->id;
            $ft->endpoint = $this->endpoint;
            if ($this->apiFormat == 'json') {
                $ft->fetcher = 'SimpleJSON';
            }
            $ft->dataFormat = $this->dataFormat;
            $ft->count = 1; // For now, just keep it simple even tho fetcher can support more.
            if ($this->frequency == 'day') {
                $ft->frequency = 'day';
            }
            else if ($this->frequency == 'hour') {
                $ft->frequency = 'hour';
            }
            else if ($this->frequency == 'week') {
                $ft->frequency = 'week';
            }
            else {
                $ft->frequency = 'day';
            }
            $ft->scheduleNextFetch();
            $ft->save();
        }
        $this->status = 'active';
        $this->save();
    }

    public function deactivate()
    {
        $s = "delete from " . FetchTask::$tablename . " where datasource_id=" . $this->id;
        $result = app('db')->delete($s);
        $this->status = 'inactive';
        $this->save();
    }

    public function initializeFromMap($params) 
    {
        if (array_key_exists('id', $params)) {
            $this->id = $params['id'];
        }
        $this->name         = $params['name'];
        $this->status       = "inactive";
        $this->sourceType   = $params['sourceType'];
        if (array_key_exists('description', $params)) {
            $this->description = $params['description'];
        }
        else {
            $this->description = "";
        }
        $this->entity       = $params['entity'];
        $this->entityId     = $params['entityId'];
        $this->dataFormat   = $params['dataFormat'];
        $this->frequency    = "ondemand";
        $this->endpoint     = null;
        $this->apiFormat    = null;
        if ($this->sourceType == 'api') {
            $this->apiFormat = $params['apiFormat'];
            $this->endpoint  = $params['endpoint'];
            $this->frequency = $params['frequency'];
        }
        if ($this->frequency == 'ondemand') $this->status = 'active';
        $this->properties = json_encode(array());
    }

    public function initializeFromObject($obj) 
    {
//        \Log::info("The object is " . json_encode($obj));
        $this->id           = $obj->id;
        $this->name         = $obj->name;
        $this->status       = $obj->status;
        $this->sourceType   = $obj->source_type;
        $this->description  = $obj->description;
        $this->entity       = $obj->entity;
        $this->entityId     = $obj->entity_id;
        $this->dataFormat   = $obj->data_format;
        $this->frequency    = $obj->frequency;
        if ($this->sourceType == 'api') {
            $this->apiFormat = $obj->api_format;
            $this->endpoint  = $obj->endpoint;
            $this->frequency = $obj->frequency;
        }
        $this->properties = $obj->properties;
    }

    public static function find ($id) {
        $s = "select id,name,status,source_type,description,entity,entity_id,api_format,data_format,endpoint,frequency,properties,created_at,updated_at from " . self::$tablename . " WHERE id = " . $id;
        $result = app('db')->select($s);
        $ds = null;
        if ($result != null) {
            $ds = new DataSource();
            $ds->initializeFromObject($result[0]);
        }
        return $ds;
    }

    public static function listEntityDataSources ($entityId) {
        $s = "select id,name,status,source_type,description,entity,entity_id,api_format,data_format,endpoint,frequency,properties,created_at,updated_at from " . self::$tablename . " WHERE entity_id = " . $entityId;
        $s .= " order by id";
        $datasources = array();

        $list = app('db')->select($s);

        for ($i=0; $i<sizeof($list); ++$i) {
            $ds = new DataSource();
            $ds->initializeFromObject($list[$i]);
            $datasources[] = $ds;
        }
        return $datasources;
    }

    public function save ()
    {
        if ($this->id < 0) {
            $this->id = app('db')->table(self::$tablename)->insertGetId([
                'name'          => $this->name,
                'status'        => $this->status,
                'source_type'   => $this->sourceType,
                'description'   => $this->description,
                'entity'        => $this->entity,
                'entity_id'     => $this->entityId,
                'api_format'    => $this->apiFormat,
                'data_format'   => $this->dataFormat,
                'endpoint'      => $this->endpoint,
                'frequency'     => $this->frequency,
                'properties'    => $this->properties,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
              ]);
        }
        else {
            app('db')->table(self::$tablename)->where(['id' => $this->id])->update([
                'name'          => $this->name,
                'status'        => $this->status,
                'source_type'   => $this->sourceType,
                'description'   => $this->description,
                'entity'        => $this->entity,
                'entity_id'     => $this->entityId,
                'api_format'    => $this->apiFormat,
                'data_format'   => $this->dataFormat,
                'endpoint'      => $this->endpoint,
                'frequency'     => $this->frequency,
                'properties'    => $this->properties,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
    }
  }
}