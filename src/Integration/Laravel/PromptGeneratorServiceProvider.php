<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\PromptGenerator\PromptGeneratorPipeline;
use LLM\Agents\PromptGenerator\PromptGeneratorPipelineInterface;

final class PromptGeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PromptGeneratorPipeline::class, PromptGeneratorPipeline::class);
        $this->app->singleton(PromptGeneratorPipelineInterface::class, PromptGeneratorPipeline::class);
        $this->app->singleton(AgentPromptGeneratorInterface::class, PromptGeneratorPipeline::class);
    }
}