<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\PromptContextInterface;

final readonly class PromptGeneratorInput
{
    public function __construct(
        public AgentInterface $agent,
        public string|\Stringable $userPrompt,
        public PromptInterface $prompt,
        public PromptContextInterface $context,
    ) {}

    public function withAgent(AgentInterface $agent): self
    {
        return new self(
            $agent,
            $this->userPrompt,
            $this->prompt,
            $this->context,
        );
    }

    public function withUserPrompt(string|\Stringable $userPrompt): self
    {
        return new self(
            $this->agent,
            $userPrompt,
            $this->prompt,
            $this->context,
        );
    }

    public function withContext(PromptContextInterface $context): self
    {
        return new self(
            $this->agent,
            $this->userPrompt,
            $this->prompt,
            $context,
        );
    }

    public function withPrompt(PromptInterface $prompt): self
    {
        return new self(
            $this->agent,
            $this->userPrompt,
            $prompt,
            $this->context,
        );
    }
}