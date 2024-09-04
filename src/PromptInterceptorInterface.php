<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\LLM\Prompt\Chat\PromptInterface;

interface PromptInterceptorInterface
{
    public function generate(
        PromptGeneratorInput $input,
        InterceptorHandler $next,
    ): PromptInterface;
}
