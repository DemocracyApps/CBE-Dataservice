<?php namespace CBEDataService\Domain\Fetch\Fetchers;

class SimpleCSVFileFetcher extends Fetcher
{
    /*
     * Get a simple CSV file
     */
    static public function fetch($url)
    {
        $result = new \stdClass();
        $result->type = 'tableData';
        $result->error = false;
        $result->message = null;
        $result->headers = null;
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

            $headers = null;
            $values = [];
            $lines_arr = preg_split('/\n|\r/', $curl_response);
            foreach ($lines_arr as $line) {
                if ($headers == null) {
                    $headers = str_getcsv($line);
                } else {
                    $values[] = str_getcsv($line);
                }
            }
            $result->headers = $headers;
            $result->data = $values;
        }
        return $result;
    }
}