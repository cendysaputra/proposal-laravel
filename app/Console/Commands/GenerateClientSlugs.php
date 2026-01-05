<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateClientSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clients = Client::whereNull('slug')->orWhere('slug', '')->get();

        if ($clients->isEmpty()) {
            $this->info('No clients found without slugs.');
            return 0;
        }

        $this->info("Found {$clients->count()} clients without slugs.");

        $bar = $this->output->createProgressBar($clients->count());
        $bar->start();

        foreach ($clients as $client) {
            $slug = Str::slug($client->judul);
            $originalSlug = $slug;
            $counter = 1;

            while (Client::where('slug', $slug)->where('id', '!=', $client->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $client->slug = $slug;
            $client->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Slugs generated successfully!');

        return 0;
    }
}
