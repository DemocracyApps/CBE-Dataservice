<?php namespace CBEDataService\Console\Commands;

use CBEDataService\Domain\Fetch\FetchTask;
use Illuminate\Console\Command;

class Fetch extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'cds:fetch {fetchId?}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Run fetch jobs';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {

    \Log::info("I am in Fetch!");
    return;
    $id = $this->argument('fetchId');

    if ($id != null) {
      $task = FetchTask::find($id);
    }
    else {
      $now = time();
      $tasks = FetchTask::where('next', '<', date('Y-m-d H:i:s', $now))->get();
      foreach ($tasks as $task) {
        echo "Task ID = $task->id and next = $task->next" . PHP_EOL;
        $result = $task->fetch();
        echo ('Column headers = ' . json_encode($result->headers) . PHP_EOL);
      }
    }
  }
}
