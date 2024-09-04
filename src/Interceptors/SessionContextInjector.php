<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\PromptContextInterface;
use LLM\Agents\PromptGenerator\Context;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;

final class SessionContextInjector implements PromptInterceptorInterface
{
    public function generate(
        AgentInterface $agent,
        \Stringable|string $userPrompt,
        PromptInterface $prompt,
        PromptContextInterface $context,
        AgentPromptGeneratorInterface $next,
    ): PromptInterface {
        \assert($prompt instanceof Prompt);
        \assert($context instanceof Context);

        if ($context->getAuthContext() === null) {
            return $next->generate(
                agent: $agent,
                userPrompt: $userPrompt,
                context: $context,
                prompt: $prompt,
            );
        }

        return $next->generate(
            agent: $agent,
            userPrompt: $userPrompt,
            context: $context,
            prompt: $prompt
                ->withAddedMessage(
                    MessagePrompt::system(
                        prompt: 'Session context: {active_context}',
                    ),
                )->withValues(
                    values: [
                        'active_context' => \json_encode($context->getAuthContext()),
                    ],
                ),
        );
    }
}
