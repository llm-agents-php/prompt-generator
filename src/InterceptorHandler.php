<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;

final readonly class InterceptorHandler
{
    public function __construct(
        private AgentPromptGeneratorInterface $generator,
    ) {}

    public function __invoke(PromptGeneratorInput $input): PromptInterface
    {
        return $this->generator->generate(
            agent: $input->agent,
            userPrompt: $input->userPrompt,
            context: $input->context,
            prompt: $input->prompt,
        );
    }
}
