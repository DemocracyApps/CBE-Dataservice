<?php namespace CBEDataService\Domain\Fetch;

class FetchTask
{
    const DATASET_OUTPUT_VERSION = "1.0.0";
    protected static $tablename = "fetch_tasks";

    public $id = -1;
    public $frequency = "ondemand";
    public $datasource = null;
    public $datasourceId = -1;
    public $entity = null;
    public $entityId = -1;
    public $endpoint = null;

    public function __construct($frequency, $params) 
    {
        $this->frequency    = $frequency;
        $this->entity       = $params['entity'];
        $this->entityId     = $params['entityId'];
        $this->datasource   = $params['datasource'];
        $this->datasourceId = $params['datasourceId'];
        $this->endpoint     = $params['endpoint'];
        $this->next         = null;
    }

    public function initializeFromSpec ($spec) {

        if (array_key_exists('datasource', $spec)) {
            $this->entity = $spec['datasource'];
        }
        if (array_key_exists('datasourceId', $spec)) {
            $this->entity = $spec['datasourceId'];
        }
        else {
            return array('status'=>'error', 'Message'=>'Datasource ID is required');
        }

        if (array_key_exists('frequency', $spec)) {
            $this->frequency = $spec['frequency'];
        }
        else {
            $this->frequency = 'OnDemand';
        }

        if (array_key_exists('endpoint', $spec)) {
            $this->endpoint = $spec['endpoint'];
        }
        else {
            return array('status'=>'error','message'=>'Endpoint is required');
        }

        if (array_key_exists('fetcher', $spec)) {
            $this->fetcher = $spec['fetcher'];
        }
        else {
            return array('status'=>'error', 'message'=>"Fetcher is required");
        }

        if (array_key_exists('entity', $spec)) {
            $this->entity = $spec['entity'];
        }
        if (array_key_exists('entityId', $spec)) {
            $this->entity = $spec['entityId'];
        }

        if (array_key_exists('properties', $spec)) {
            $this->properties = $spec['properties'];
        }

        $this->scheduleNextFetch();

        return array('status'=>'ok', 'message'=>'OK');
    }

    public function fetch()
    {
        echo "Fetching " . $this->id . PHP_EOL;
        $fetcherClassName = '\CBEDataService\Domain\Fetch\Fetchers\\' . $this->fetcher . "Fetcher";
        $reflectionMethod = new \ReflectionMethod($fetcherClassName, 'fetch');
        if ($reflectionMethod == null) throw new \Exception("No such method!");
        echo 'Calling fetcher' . PHP_EOL;
        $result = $reflectionMethod->invokeArgs(null, array($this->url));

        echo ('Back from fetcher with result error = ' . $result->error);
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
        }
    }
}
