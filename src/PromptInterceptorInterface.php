<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\PromptContextInterface;

interface PromptInterceptorInterface
{
    public function generate(
        AgentInterface $agent,
        string|\Stringable $userPrompt,
        PromptInterface $prompt,
        PromptContextInterface $context,
        AgentPromptGeneratorInterface $next,
    ): PromptInterface;
}
