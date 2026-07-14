<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\RecommendationService;

class GenerateRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:generate {--user= : The ID of a specific user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate book recommendations for users based on highly rated genres';

    /**
     * Execute the console command.
     */
    public function handle(RecommendationService $service)
    {
        $userId = $this->option('user');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User not found with ID {$userId}");
                return 1;
            }
            
            $this->info("Generating recommendations for {$user->name}...");
            $service->generateForUser($user);
            $this->info("Done.");
            return 0;
        }
        
        $this->info("Generating recommendations for all users...");
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();
        
        foreach ($users as $user) {
            $service->generateForUser($user);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("All recommendations generated successfully.");
        return 0;
    }
}
