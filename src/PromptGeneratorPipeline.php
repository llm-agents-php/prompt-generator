<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\Agent\AgentInterface;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\LLM\PromptContextInterface;

final class PromptGeneratorPipeline implements PromptGeneratorPipelineInterface
{
    /** @var PromptInterceptorInterface[] */
    private array $interceptors = [];
    private int $offset = 0;

    public function generate(
        AgentInterface $agent,
        string|\Stringable $userPrompt,
        PromptContextInterface $context,
        PromptInterface $prompt = new Prompt(),
    ): PromptInterface {
        if (!isset($this->interceptors[$this->offset])) {
            return $prompt;
        }

        return $this->interceptors[$this->offset]->generate(
            agent: $agent,
            userPrompt: $userPrompt,
            prompt: $prompt,
            context: $context,
            next: $this->next(),
        );
    }

    public function withInterceptor(PromptInterceptorInterface ...$interceptor): self
    {
        $pipeline = clone $this;
        $pipeline->interceptors = \array_merge($this->interceptors, $interceptor);

        return $pipeline;
    }

    private function next(): self
    {
        $pipeline = clone $this;
        $pipeline->offset++;
        return $pipeline;
    }
}
