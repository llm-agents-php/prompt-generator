<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;

final readonly class InstructionGenerator implements PromptInterceptorInterface
{
    public function __construct(
        private string $outputFormat = 'markdown',
    ) {}

    public function generate(
        PromptGeneratorInput $input,
        InterceptorHandler $next,
    ): PromptInterface {
        \assert($input->prompt instanceof Prompt);

        return $next(
            input: $input->withPrompt(
                $input->prompt
                    ->withAddedMessage(
                        MessagePrompt::system(
                            prompt: '{instruction}',
                            with: ['instruction'],
                        ),
                    )
                    ->withAddedMessage(
                        MessagePrompt::system(
                            prompt: 'Output format instruction: {output_format_instruction}',
                            with: ['output_format_instruction'],
                        ),
                    )
                    ->withValues(
                        values: [
                            'instruction' => $input->agent->getInstruction(),
                            'output_format_instruction' => \sprintf(
                                'always response in `%s` format',
                                $this->outputFormat,
                            ),
                        ],
                    ),
            ),
        );
    }
}
