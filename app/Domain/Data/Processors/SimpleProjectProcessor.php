<?php namespace CBEDataService\Domain\Data\Processors;

use CBEDataService\Domain\Data\Dataset;

class SimpleProjectProcessor
{
    static public function process($dataSource, $data)
    {

        $projectKey = 'project';

        $output = array();
        if ($data['type'] != 'FeatureCollection') {
            return "Invalid input";
        }
        foreach ($data['features'] as $feature) {
            $pkey = $feature['properties'][$projectKey];
            if (! array_key_exists($pkey, $output)) {
                $output[$pkey] = array( 'id' => $pkey, 
                                        'title' => $feature['properties']['title'],
                                        'description' => $feature['properties']['description'],
                                        'project_department' => $feature['properties']['project_department'],
                                        'completion_data' => $feature['properties']['scheduled_completion_date']
                                        );
            }
            //$output[$pkey][] = self::lineItem($feature);
        }
        //$output = sizeof($output);
        $year = date('Y', time());
        $ds = new Dataset($dataSource->name, array(
                'year'            => $year,
                'type'            => 'project',
                'entity'          => $dataSource->entity,
                'entityId'        => $dataSource->entityId,
                'datasourceId'    => $dataSource->id
                ));
        \Log::info("Dataset is: " . json_encode($ds));
//      $ds->initializeCategories(array_slice($header, 0, $nCategories));

        return $output;
    }

    private static function lineItem($feature) 
    {

    }
// type": "FeatureCollection",
//         "features": [{
//             "type": "Feature",
//             "id": "projects_general_ledger.fid-34112b2a_15231796dcf_1c94",
//             "geometry": null,
//             "properties": {
//                 "fund": "4100",
//                 "object": "521001",
//                 "project": "I0801",
//                 "org": "41000050",


}
