<?php namespace CBEDataService\Domain\Data;

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
    public $apiEndpoint = null;
    public $apiFormat = null;
    public $frequency = null;
    public $properties = null;

    public function __construct() 
    {
        $this->status       = "inactive";
        $this->frequency    = "ondemand";
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
        $ds = new DataSource($result);
        return $ds;
    }

    public static function listEntityDataSources ($entityId) {
        $s = "select id,name,status,source_type,description,entity,entity_id,api_format,data_format,endpoint,frequency,properties,created_at,updated_at from " . self::$tablename . " WHERE entity_id = " . $entityId;
        $s .= " order by id";
        $datasources = array();
        \Log::info("Here we go with entityId $entityId");
        $list = app('db')->select($s);
        \Log::info(json_encode($list));
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