<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;

final class UserPromptInjector implements PromptInterceptorInterface
{
    public function generate(
        PromptGeneratorInput $input,
        InterceptorHandler $next,
    ): PromptInterface {
        \assert($input->prompt instanceof Prompt);

        return $next(
            input: $input->withPrompt(
                $input->prompt->withAddedMessage(
                    MessagePrompt::user(
                        prompt: '{user_prompt}',
                        with: ['user_prompt'],
                    )->withValues([
                        'user_prompt' => (string) $input->userPrompt,
                    ]),
                ),
            ),
        );
    }
}
