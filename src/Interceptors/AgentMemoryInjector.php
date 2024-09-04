<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\PromptContextInterface;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;
use LLM\Agents\Solution\SolutionMetadata;

final class AgentMemoryInjector implements PromptInterceptorInterface
{
    public function generate(
        AgentInterface $agent,
        \Stringable|string $userPrompt,
        PromptInterface $prompt,
        PromptContextInterface $context,
        AgentPromptGeneratorInterface $next,
    ): PromptInterface {
        \assert($prompt instanceof Prompt);

        return $next->generate(
            agent: $agent,
            userPrompt: $userPrompt,
            context: $context,
            prompt: $prompt
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
                                $agent->getMemory(),
                            ),
                        ),
                        'dynamic_memory' => '',
                    ],
                ),
        );
    }
}
