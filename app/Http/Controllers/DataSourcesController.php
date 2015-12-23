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
