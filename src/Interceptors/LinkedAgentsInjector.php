<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator\Interceptors;

use LLM\Agents\Agent\AgentRepositoryInterface;
use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;
use LLM\Agents\Solution\AgentLink;
use LLM\Agents\Tool\SchemaMapperInterface;

final readonly class LinkedAgentsInjector implements PromptInterceptorInterface
{
    public function __construct(
        private AgentRepositoryInterface $agents,
        private SchemaMapperInterface $schemaMapper,
    ) {}

    public function generate(
        PromptGeneratorInput $input,
        InterceptorHandler $next,
    ): PromptInterface {
        \assert($input->prompt instanceof Prompt);

        if (\count($input->agent->getAgents()) === 0) {
            return $next($input);
        }

        $associatedAgents = \array_map(
            fn(AgentLink $agent): array => [
                'agent' => $this->agents->get($agent->getName()),
                'output_schema' => \json_encode($this->schemaMapper->toJsonSchema($agent->outputSchema)),
            ],
            $input->agent->getAgents(),
        );

        return $next(
            input: $input->withPrompt(
                $input
                    ->prompt
                    ->withAddedMessage(
                        MessagePrompt::system(
                            prompt: <<<'PROMPT'
There are agents {linked_agents} associated with you. You can ask them for help if you need it.
Use the `ask_agent` tool and provide the agent key.
Always follow rules:
- Don't make up the agent key. Use only the ones from the provided list.
PROMPT,
                        ),
                    )
                    ->withValues(
                        values: [
                            'linked_agents' => \implode(
                                PHP_EOL,
                                \array_map(
                                    static fn(array $agent): string => \json_encode([
                                        'key' => $agent['agent']->getKey(),
                                        'description' => $agent['agent']->getDescription(),
                                        'output_schema' => $agent['output_schema'],
                                    ]),
                                    $associatedAgents,
                                ),
                            ),
                        ],
                    ),
            ),
        );
    }
}
