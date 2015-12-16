<?php namespace CBEDataService\Domain\Fetch\Fetchers;

abstract class Fetcher
{
    public $name = null;

    static public function fetch ($url)
    {
        return null;
    }
}
