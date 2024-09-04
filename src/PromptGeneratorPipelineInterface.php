<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\LLM\AgentPromptGeneratorInterface;

interface PromptGeneratorPipelineInterface extends AgentPromptGeneratorInterface
{
    public function withInterceptor(PromptInterceptorInterface ...$interceptor): self;
}
