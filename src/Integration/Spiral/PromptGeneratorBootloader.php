<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Integration\Spiral;

use LLM\Agents\LLM\AgentPromptGeneratorInterface;
use LLM\Agents\PromptGenerator\PromptGeneratorPipeline;
use LLM\Agents\PromptGenerator\PromptGeneratorPipelineInterface;
use Spiral\Boot\Bootloader\Bootloader;

final class PromptGeneratorBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            PromptGeneratorPipeline::class => PromptGeneratorPipeline::class,
            PromptGeneratorPipelineInterface::class => PromptGeneratorPipeline::class,
            AgentPromptGeneratorInterface::class => PromptGeneratorPipeline::class,
        ];
    }
}
