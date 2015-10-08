<?php namespace CBEDataService\Console\Commands;

use CBEDataService\Domain\Data\Dataset;
use Illuminate\Console\Command;

class Tester extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cds:test {name}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Test something';


  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $name = $this->argument('name');
    $ds = new Dataset(3, 2, "Expense", 2010, $name);
    $ds->values = "This is the data";
    $ds->save();
  }
}