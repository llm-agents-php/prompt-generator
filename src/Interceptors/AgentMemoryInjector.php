<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;
use LLM\Agents\Solution\SolutionMetadata;

final class AgentMemoryInjector implements PromptInterceptorInterface
{
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
                            prompt: 'Instructions about your experiences, follow them: {memory}. And also {dynamic_memory}',
                        ),
                    )
                    ->withValues(
                        values: [
                            'memory' => \implode(
                                PHP_EOL,
                                \array_map(
                                    static fn(SolutionMetadata $metadata) => $metadata->content,
                                    $input->agent->getMemory(),
                                ),
                            ),
                            'dynamic_memory' => '',
                        ],
                    ),
            ),
        );
    }
}
