<?php
namespace CBEDataService\Http\Controllers;

use CBEDataService\Domain\Data\CSVProcessor;
use CBEDataService\Domain\Data\DataSource;
use CBEDataService\Domain\Data\Dataset;
use Illuminate\Http\Request;

class DataSourcesController extends ApiController
{

    public function register(Request $request) {
        \Log::info("The request is " . json_encode($request->all()));
        $ds = new DataSource();
        $ds->initializeFromMap($request->all());
        $ds->save();
        return $this->respondOK("Datasource saved with id " . $ds->id);
    }

    public function show($dsId) {
        \Log::info("Here with datasource ID = $dsId");
        $ds = DataSource::find($dsId);
        return $this->respondOK("DataSource $dsId", $ds);
    }

    public function execute (Request $request, $dsId) {
        \Log::info("I am being asked to execute datasource $dsId");
        $ds = DataSource::find($dsId);
        if ($ds == null) {
            return $this->respondNotFound("Unable to find datasource with id $dsId");
        }
        $fetcherClassName = '\CBEDataService\Domain\Fetch\Fetchers\\' . $ds->getFetcher() . "Fetcher";
        $reflectionMethod = new \ReflectionMethod($fetcherClassName, 'fetch');
        if ($reflectionMethod == null) throw new \Exception("No such method!");
        \Log::info("Calling fetcher ". $fetcherClassName);
        $result = $reflectionMethod->invokeArgs(null, array($ds->endpoint));
        if ($result->error) {
            return $this->respondInternalError($result->message);
        }
        else {
            $processorClassName = '\CBEDataService\Domain\Data\Processors\\' . $ds->getDataProcessor() . 'Processor';
            $reflectionMethod = new \ReflectionMethod($processorClassName, 'process');
            if ($reflectionMethod == null) throw new \Exception("No such method!");
            \Log::info("Calling processor ". $processorClassName);
            $dataset = $reflectionMethod->invokeArgs(null, array($ds, $result->data));
            //\Log::info("Back from processing with result = " . json_encode($dataset));
            $dataset->save();
            //\Log::info('Back from processing with: ' . json_encode($result));
            \Log::info("Done processing");
            return $this->respondOK("Successfully executed datasource fetch and processing");;
        }
    }

    public function update(Request $request, $dsId) {
        $ds = DataSource::find($dsId);
        if (!$ds) $this->respondNotFound("No datasource $dsId found.");
        $params = $request->all();
        $needSave = false;
        if (array_key_exists('status', $params)) {
            $value = $params['status'];
            if ($value == 'active') {
                if ($ds->status != 'active') { // Just make it idempotent
                    $ds->activate();
                }
            }
            else if ($value == 'inactive') {
                if ($ds->status != 'inactive') {
                    $ds->deactivate();
                }
            }
            else {
                return $this->respondFailedValidation("Invalid status value: $value");
            }
            unset($params['status']);
        }
        $ds->updateFromMap($params);
        $ds->save();

        return $this->respondOK("Datasource $dsId updated successfully");
    }

    public function getEntityInfo(Request $request) {
        $entityId = $request->get('entity_id');
        \Log::info("I'm in getEntityInfo with id " . $entityId);
        $datasources = DataSource::listEntityDataSources($entityId);
        $datasets = Dataset::listEntityDatasets($entityId);
        \Log::info(json_encode($datasources));
        $data = array();
        $data['datasources'] = $datasources;
        $data['datasets']    = $datasets;
        return $this->respondOK("Listing of datasources and datasets for entity " . $entityId, $data);
    }
}
