<?php

namespace App\Traits\Weather;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

trait UiTraits
{
    protected function promptForMissingArgumentsUsing()
    {
        return [
            'provider' => fn () => select(
                label: 'Which provider do you want to use?',
                options: [
                    'open-weather' => 'Open Weather Map',
                    'accu-weather' => 'Accu Weather',
                ]
            ),
            'city' => fn () => text(
                label: 'Which city would you like to know about?',
                placeholder: 'E.g. Tashkent',
                required: true
            ),
            'channel' => fn () => select(
                label: 'Select a channel',
                options: [
                    'mail' => 'Mail',
                    'telegram' => 'Telegram',
                    'console' => 'Console',
                ]
            ),
        ];
    }

    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output)
    {
        $channel = $input->getArgument('channel');

        if ($channel === 'mail') {
            $input->setArgument('to', text(
                label: 'Enter your email address',
                placeholder: 'E.g. azamatumarjonov@gmail.com',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 5 => 'The field must be at least 5 characters.',
                    $this->emailRule($value) => 'The field must be a valid email address.',
                    default => null,
                }
            ));
        } elseif ($channel === 'telegram') {
            $input->setArgument('to', text(
                label: 'Enter Telegram chat ID',
                placeholder: 'E.g. 1001938683863',
                required: true,
                validate: fn (string $value) => match (true) {
                    strlen($value) < 13 => 'The field must be at least 13 characters.',
                    strlen($value) > 13 => 'The field must not exceed 13 characters.',
                    !is_numeric($value) => 'The field must be a valid Telegram Chat ID.',
                    default => null,
                }
            ));
        }
    }

    protected function emailRule($value) {
        return !filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
