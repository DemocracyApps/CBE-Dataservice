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
    // For now we just process these here, but later we'll probably throw them into a queue
    // for worker processes to handle individually.
    $id = $this->argument('fetchId');
    \Log::info("In fetch with id = " . $id);
    if ($id != null) {
      $task = FetchTask::find($id);
      $task->fetch();
    }
    else {
      \Log::info("Running fetch tasks");
      $now = time();
      $tasks = FetchTask::getNextTasks($now);
      foreach ($tasks as $task) {
        echo "Task ID = $task->id and next = $task->next" . PHP_EOL;
        $result = $task->fetch();
      }
    }
  }
}
