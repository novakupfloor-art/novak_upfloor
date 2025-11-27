<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SessionHelper;

class CleanExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired sessions from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting session cleanup (Hard Delete)...');
        
        $cleanedCount = SessionHelper::cleanExpiredSessions();
        
        $this->info("Hard deleted {$cleanedCount} expired sessions from database.");
        
        // Since we're using hard delete, no need to clean old inactive sessions
        // All sessions are deleted when they expire or are logged out
        
        return Command::SUCCESS;
    }
}
