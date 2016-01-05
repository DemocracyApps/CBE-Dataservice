<?php namespace CBEDataService\Domain\Fetch;

class FetchTask
{
    const DATASET_OUTPUT_VERSION = "1.0.0";
    public static $tablename = "fetch_tasks";

    public $id = -1;
    public $frequency = "ondemand";
    public $dataSource = null;
    public $endpoint = null;
    public $fetcher = 'SimpleJSON';
    public $dataFormat = 'simple-budget';
    public $count = 1;
    public $next = null;
    public $properties = "";

    public function __construct() 
    {
        $this->dataSource   = null;
        $this->endpoint     = null;
        $this->frequency    = 'day';
        $this->fetcher      = 'SimpleJSON';
        $this->dataFormat   = 'simple-budget';
        $this->count        = 1;
        $this->next         = null;
    }

    public function save ()
    {
        if ($this->id < 0) {
            $this->id = app('db')->table(self::$tablename)->insertGetId([
                'datasource_id'   => $this->dataSource,
                'endpoint'      => $this->endpoint,
                'frequency'     => $this->frequency,
                'fetcher'       => $this->fetcher,
                'data_format'   => $this->dataFormat,
                'count'         => $this->count,
                'next'          => $this->next,
                'properties'    => $this->properties,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
              ]);
        }
        else {
            app('db')->table(self::$tablename)->where(['id' => $this->id])->update([
                'datasource_id'   => $this->dataSource,
                'endpoint'      => $this->endpoint,
                'frequency'     => $this->frequency,
                'fetcher'       => $this->fetcher,
                'data_format'   => $this->dataFormat,
                'count'         => $this->count,
                'next'          => $this->next,
                'properties'    => $this->properties,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function initializeFromObject($obj) 
    {
        $this->id           = $obj->id;
        $this->dataSource   = $obj->datasource_id;
        $this->endpoint     = $obj->endpoint;
        $this->frequency    = $obj->frequency;
        $this->fetcher      = $obj->fetcher;
        $this->dataFormat   = $obj->data_format;
        $this->count        = $obj->count;
        $this->next         = $obj->next;
        $this->properties   = $obj->properties;
    }

    public static function find ($id) {
        $s = "select id,datasource_id,endpoint,frequency,fetcher,data_format,count,next,properties,created_at,updated_at from " . self::$tablename . " WHERE id = " . $id;
        $result = app('db')->select($s);
        $ft = null;
        if ($result != null) {
            $ft = new FetchTask();
            $ft->initializeFromObject($result[0]);
        }
        return $ft;
    }
    public static function getNextTasks($now)
    {
//      $tasks = FetchTask::where('next', '<', date('Y-m-d H:i:s', $now))->get();
        $s = "select id,datasource_id,endpoint,frequency,fetcher,data_format,count,next,properties,created_at,updated_at from " . self::$tablename;
        $s .= " WHERE next < '" . date('Y-m-d H:i:s', $now) . "'";
        $results = app('db')->select($s);
        $tasks = array();
        if ($results != null) {
            foreach ($results as $result) {
                $ft = new FetchTask();
                $ft->initializeFromObject($result);
                $tasks[] = $ft;
            }
        }
        return $tasks;
    }

    public function fetch()
    {
        \Log::info("Fetching " . $this->id);
        $fetcherClassName = '\CBEDataService\Domain\Fetch\Fetchers\\' . $this->fetcher . "Fetcher";
        $reflectionMethod = new \ReflectionMethod($fetcherClassName, 'fetch');
        if ($reflectionMethod == null) throw new \Exception("No such method!");
        \Log::info("Calling fetcher");
        $result = $reflectionMethod->invokeArgs(null, array($this->endpoint));

        \Log::info('Back from fetcher with error = ' . json_encode($result->error));


        $this->scheduleNextFetch();
        $this->save();
        return $result;
    }

    public function scheduleNextFetch ()
    {
        if ($this->frequency == 'ondemand') {
            $this->next = null;
        }
        else if ($this->next == null) {
            $this->next = date('Y-m-d H:i:s', time()); // Immediately
        }
        else {
            $delta = 0;
            \Log::info("Incoming time = " . $this->next);
            \Log::info("Frequency = " . $this->frequency);
            $current = strtotime($this->next);
            $count = $this->count > 0?$this->count:1;

            if ($this->frequency == 'hour') {
                $delta = $count * 60 * 60;
                $current += $delta;
            }
            else if ($this->frequency == 'day') {
                $delta = $count * 60 * 60 * 24;
                $current += $delta;
            }
            else if ($this->frequency == 'week') {
                $delta = $count * 60 * 60 * 24 * 7;
                $current += $delta;
            }
            else if ($this->frequency == 'month') { // We ignore count - assume 1
                $current->modify('next month');
            }
            else { // Again, we ignore count - assume 1
                $current->modify('next year');
            }
            if (time() > $current) $current += $delta;
            $this->next = date('Y-m-d H:i:s', $current);
            \Log::info("Outgoing time = " . $this->next);

        }
    }
}
