<?php

namespace App\Services\Uic918Parser;

use App\Data\Uic918\FlexibleContent\FlexibleContent;
use App\Exceptions\Uic918\InvalidDataException;
use App\Exceptions\Uic918\ParserException;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Process;

class FlexibleContentParser extends Parser
{
    /**
     * @throws ParserException
     * @throws InvalidDataException
     */
    public function parse(string $rawData): FlexibleContent
    {
        $pythonExec = base_path('uic-asn1-parser/.venv/bin/python');
        if (!is_file($pythonExec)) {
            $this->setupPythonEnvironment();
        }

        $process = Process::run([
            $pythonExec,
            base_path('uic-asn1-parser/decode.py'),
            $this->version,
            base64_encode($rawData),
        ]);

        if ($process->failed()) {
            Context::add('python_error', $process->errorOutput());
            throw new InvalidDataException('Couldn\'t parse flexible content');
        }

        $data = json_decode($process->output());

        //        ray($data)->green();

        return FlexibleContent::from($data);
    }

    protected function setupPythonEnvironment(): void
    {
        $commands = [
            [ // Setup virtual environment
                'python',
                '-m',
                'venv',
                base_path('uic-asn1-parser/.venv'),
            ],
            [ // Install dependencies
                base_path('uic-asn1-parser/.venv/bin/pip'),
                'install',
                '-r',
                base_path('uic-asn1-parser/requirements.txt'),
            ],
        ];

        foreach ($commands as $command) {
            $process = Process::run($command);

            if ($process->failed()) {
                throw new ParserException('Could not setup python environment automatically.');
            }
        }
    }
}
