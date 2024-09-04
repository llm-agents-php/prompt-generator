<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\ChatMessage;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\Prompt\Chat\Role;
use LLM\Agents\LLM\PromptContextInterface;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;

final class UserPromptInjector implements PromptInterceptorInterface
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
            prompt: $prompt->withAddedMessage(
                new ChatMessage(
                    content: (string) $userPrompt,
                    role: Role::User,
                ),
            ),
        );
    }
}
