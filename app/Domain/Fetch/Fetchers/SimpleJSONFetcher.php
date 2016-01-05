<?php namespace CBEDataService\Domain\Fetch\Fetchers;

class SimpleJSONFetcher extends Fetcher
{
    /*
     * Get a simple CSV file
     */
    static public function fetch($url)
    {
        $result = new \stdClass();
        $result->type = 'json';
        $result->error = false;
        $result->message = null;
        $result->data = null;

        ini_set("auto_detect_line_endings", true); // Deal with Mac line endings

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            $result->error = true;
            $result->message = $info;
            die('error occured during curl exec. Additional info: ' . var_export($info));
        }
        else {
            curl_close($curl);
            $result->data = json_decode($curl_response);
        }
        return $result;
    }
}