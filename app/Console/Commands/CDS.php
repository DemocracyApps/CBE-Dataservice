<?php namespace CBEDataService\Console\Commands;

use CBEDataService\Domain\Data\Dataset;
use CBEDataService\Domain\Data\CSVProcessor;
use Illuminate\Console\Command;

class CDS extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cds:uploadfile {file}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Load a data file as if uploaded from CBE';


  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $filename = $this->argument('file');
    $datasets = CSVProcessor::ProcessSimpleBudget(json_decode(file_get_contents($filename), true));
    foreach ($datasets as $ds) {
      \Log::info("Did dataset " . $ds->name);
    }

//    $ds = new Dataset(3, 2, "Expense", 2010, $name);
//    $ds->values = "This is the data";
//    $ds->save();
  }
}