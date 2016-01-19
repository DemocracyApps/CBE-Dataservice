<?php namespace CBEDataService\Domain\Data\Processors;

use CBEDataService\Domain\Data\Dataset;

class SimpleProjectProcessor
{
    static private function getValue ($index, $data) {
        $result = ($index['summable']?0.0:null);
        if ($index['location']) {
            if (array_key_exists($index['location'][0], $data)) {
                $value = $data[$index['location'][0]];
                if (sizeof($index['location']) > 1) {
                    for ($i=1; $i<sizeof($index['location']) && $value != null; ++$i) {
                        if (array_key_exists($index['location'][0], $data)) {
                            $value = $value[$index['location'][$i]];
                        }
                        else {
                            $value = null;
                        }
                    }
                }
                if ($value) {
                    $result = ($index['summable'])?Number($value):trim($value);
                }
            }
        }
        return $result;
    }

    static public function process($dataSource, $data)
    {
        if ($data['type'] != 'FeatureCollection') {
            return "Invalid input";
        }

                    //TODO: We need to do the following:
            // 1. Define the standard fields for a project. Quasi-done below except for budget/actual numbers. Need to determine that.
            // 2. Define a mapping of AVL fields to project fields. Later that will be a file, but for now hard-code in call
            // 3. Add ability to remap names of any field to new names (e.g., project title, account name, division name, etc.)
            // 4. Decide how to support higher granularity (month, period, etc.)
            // 5. write the bit below that converts incoming data to numbers, adds and saves to the data array.
            //    probably use $ds->addValue() (see CSVProcessor), but will need to be augmented. Key that
            //    incoming API may have multiple years ... we need to allow for that. That should be separate datasets
            //    but should the periods be the same??? I don't think that's a great idea, but maybe. 
            // 6. Save out the dataset(s)

        $projects = array(); // For project metadata

        // This defines the mapping of data fields to a standard project structure
        $dataMap = array(
            'project_id'        =>  array('location' => ['properties', 'project'],'summable' => false),
            'year'              =>  array('location' => ['properties', 'fiscal_year'],'summable' => false),
            'current_period'    =>  array('location' => ['properties', 'current_period'],'summable' => false),
            'title'             =>  array('location' => ['properties', 'title'],'summable' => false),
            'description'       =>  array('location' => ['properties', 'description'],'summable' => false),
            'organization'      =>  array('location' => ['properties', 'project_department'],'summable' => false),
            'completion_date'   =>  array('location' => ['properties', 'scheduled_completion_date'],'summable' => false),
            'categories'        =>  array(
                                        array('location' => ['properties', 'project'], 'summable' => false),
                                        array('location' => ['properties', 'fund_description'], 'summable' => false),
                                        array('location' => ['properties', 'department_description'], 'summable' => false),
                                        array('location' => ['properties', 'division_description'], 'summable' => false),
                                        array('location' => ['properties', 'cost_center_description'], 'summable' => false),
                                        array('location' => ['properties', 'object_description'], 'summable' => false)
                                     ), 
            // Just for testing - actuals TBD
            'life_budget_orig'  => array('location' => ['lifetime_original_budget'],'summable' => true),
            'life_transfers_in' => array('location' => ['lifetime_transfers_in'],'summable' => true),
            'life_transfers_out' => array('location' =>  null, 'summable' =>  true),
            'life_budget_rev'   => array('location' =>  null, 'summable' =>  true),
            'life_actual'       => array('location' => ['current_year_actual_memo'],'summable' => true),
            'current_actual'    => array('location' =>  null, 'summable' =>  true)
        );
        $categoryNames = array('Project', 'Fund', 'Department', 'Division', 'Cost Center', 'Account');
        $ds = new Dataset($dataSource->name, array(
                'year'            => self::getValue($dataMap['year'], $data['features'][0]),
                'type'            => 'project',
                'entity'          => $dataSource->entity,
                'entityId'        => $dataSource->entityId,
                'datasourceId'    => $dataSource->id
                ));
        $ds->initializeCategories($categoryNames);

        $maxIterations = -1;
        $iter = 0;
        // Loop through the entries of the incoming data
        foreach ($data['features'] as $feature) {
            ++$iter;
            if ($maxIterations > 0 && $iter > $maxIterations) break;
            // Create an entry for the project if it doesn't exist
            $pkey = self::getValue($dataMap['project_id'], $feature);
            if (! array_key_exists($pkey, $projects)) {
                $projects[] = array('project_id'      => $pkey, 
                                    'title'           => self::getValue($dataMap['title'], $feature),
                                    'description'     => self::getValue($dataMap['description'], $feature),
                                    'organization'    => self::getValue($dataMap['organization'], $feature),
                                    'completion_date' => self::getValue($dataMap['completion_date'], $feature)
                                    );
            }
            // Extract the categories
            $categories = array();
            for ($i = 0; $i < sizeof($dataMap['categories']); ++$i) {
                $categories[] = self::getValue($dataMap['categories'][$i], $feature);
            }
            $value = array();
            $value['life_budget_orig']   = self::getValue($dataMap['life_budget_orig'], $feature);
            $value['life_transfers_in']  = self::getValue($dataMap['life_transfers_in'], $feature);
            $value['life_transfers_out'] = self::getValue($dataMap['life_transfers_out'], $feature);
            $value['life_budget_rev']    = self::getValue($dataMap['life_budget_rev'], $feature);
            $value['life_actual']        = self::getValue($dataMap['life_actual'], $feature);
            $value['current_actual']     = self::getValue($dataMap['current_actual'], $feature);
            $ds->addValue($categories, $value);
        }
        // Now register metadata for the projects
        foreach ($projects as $project) {
            //\Log::info("Project meta for " . $project['project_id'] . " is " . json_encode($project));
            $ds->registerCategoryMetadata(0, $project['project_id'], $project);
        }
        // \Log::info("Dataset is: " . json_encode($ds));

        return $ds;
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
