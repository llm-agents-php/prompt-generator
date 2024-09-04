<?php

declare(strict_types=1);

namespace LLM\Agents\PromptGenerator;

use LLM\Agents\LLM\PromptContextInterface;

class Context implements PromptContextInterface
{
    private array|\JsonSerializable|null $authContext = null;

    final public static function new(): self
    {
        return new self();
    }

    public function setAuthContext(array|\JsonSerializable $authContext): self
    {
        $this->authContext = $authContext;

        return $this;
    }

    public function getAuthContext(): array|\JsonSerializable|null
    {
        return $this->authContext;
    }
}
