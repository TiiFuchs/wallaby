<?php

namespace App\Console\Commands\Pass;

use App\Models\Pass;
use Illuminate\Console\Command;

class GeneratePass extends Command
{
    protected $signature = 'pass:generate {pass : ID for the pass to generate}
                            {--o|out= : Specify output filename}';

    protected $description = 'Generates the pkpass file.';

    public function handle(): void
    {
        $pass = Pass::find($this->argument('pass'));

        $filename = $this->option('out') ?? 'pass.pkpass';

        file_put_contents($filename, $pass->generate());
    }
}
