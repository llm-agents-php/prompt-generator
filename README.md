# LLM Agents Prompt Generator

[![PHP](https://img.shields.io/packagist/php-v/llm-agents-php/agent-site-status-checker.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/llm-agents/prompt-generator.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/llm-agents/prompt-generator.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)

This package provides a flexible and extensible system for generating chat prompts with all required system and user
messages for LLM agents. It uses an interceptor-based approach to expand generator abilities.

### Installation

You can install the package via Composer:

```bash
composer require llm-agents/prompt-generator
```

### Setup in Spiral Framework

To get the Site Status Checker Agent up and running in your Spiral Framework project, you need to register its
bootloader.

**Here's how:**

1. Open up your `app/src/Application/Kernel.php` file.
2. Add the bootloader like this:
   ```php
   public function defineBootloaders(): array
   {
       return [
           // ... other bootloaders ...
           \LLM\Agents\PromptGenerator\Integration\Spiral\PromptGeneratorBootloader::class,
       ];
   }
   ```
3. Create a bootloader for the prompt generator. Create a file named `PromptGeneratorBootloader.php`
   in your `app/src/Application/Bootloader` directory:

```php
namespace App\Application\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;

use LLM\Agents\PromptGenerator\Interceptors\AgentMemoryInjector;
use LLM\Agents\PromptGenerator\Interceptors\InstructionGenerator;
use LLM\Agents\PromptGenerator\Interceptors\LinkedAgentsInjector;
use LLM\Agents\PromptGenerator\Interceptors\UserPromptInjector;
use LLM\Agents\PromptGenerator\PromptGeneratorPipeline;

class PromptGeneratorBootloader extends Bootloader
{
    public function defineSingletons(): array
    {
        return [
            PromptGeneratorPipeline::class => static function (
                LinkedAgentsInjector $linkedAgentsInjector,
            ): PromptGeneratorPipeline {
                $pipeline = new PromptGeneratorPipeline();

                return $pipeline->withInterceptor(
                    new InstructionGenerator(),
                    new AgentMemoryInjector(),
                    $linkedAgentsInjector,
                    new UserPromptInjector(),
                    // ...
                );
            },
        ];
    }
}
```

And that's it! Your Spiral app is now ready to use the agent.

## Usage

Here's an example of how to initialize the prompt generator and generate a prompt:

```php
use App\Domain\Chat\PromptGenerator\SessionContextInjector;
use LLM\Agents\PromptGenerator\Interceptors\AgentMemoryInjector;
use LLM\Agents\PromptGenerator\Interceptors\InstructionGenerator;
use LLM\Agents\PromptGenerator\Interceptors\LinkedAgentsInjector;
use LLM\Agents\PromptGenerator\Interceptors\UserPromptInjector;
use LLM\Agents\PromptGenerator\PromptGeneratorPipeline;

$generator = new PromptGeneratorPipeline();

$generator = $generator->withInterceptor(
    new InstructionGenerator(),
    new AgentMemoryInjector(),
    new LinkedAgentsInjector($agents, $schemaMapper),
    new SessionContextInjector(),
    new UserPromptInjector()
);

$prompt = $generator->generate($agent, $userPrompt, $context, $initialPrompt);
```

## Interceptors

The package comes with several built-in interceptors:

### InstructionGenerator

This interceptor adds the agent's instruction to the prompt. It includes important rules like responding in markdown
format and thinking before responding to the user.

### AgentMemoryInjector

This interceptor adds the agent's memory to the prompt. It includes both static memory (defined when creating the agent)
and dynamic memory (which can be updated during the conversation).

### LinkedAgentsInjector

This interceptor adds information about linked agents to the prompt. It provides details about other agents that the
current agent can call for help, including their keys, descriptions, and output schemas.

### UserPromptInjector

This interceptor adds the user's input to the prompt as a user message.

## Creating Custom Interceptors

You can create custom interceptors by implementing the `LLM\Agents\PromptGenerator\PromptInterceptorInterface`:

Let's create a `ContextAwarePromptInjector` that adds relevant context to the prompt based on the current time of day
and
user preferences. This example will demonstrate how to create a more sophisticated interceptor that interacts with
external services and modifies the prompt accordingly.

```php
namespace App\PromptGenerator\Interceptors;

use LLM\Agents\LLM\Prompt\Chat\MessagePrompt;
use LLM\Agents\LLM\Prompt\Chat\Prompt;
use LLM\Agents\LLM\Prompt\Chat\PromptInterface;
use LLM\Agents\PromptGenerator\InterceptorHandler;
use LLM\Agents\PromptGenerator\PromptGeneratorInput;
use LLM\Agents\PromptGenerator\PromptInterceptorInterface;
use App\Services\UserPreferenceService;
use App\Services\WeatherService;

class ContextAwarePromptInjector implements PromptInterceptorInterface
{
    public function __construct(
        private UserPreferenceService $userPreferenceService,
        private WeatherService $weatherService,
    ) {}

    public function generate(PromptGeneratorInput $input, InterceptorHandler $next): PromptInterface
    {
        $userId = $input->context->getUserId(); // Assuming we have this method in our context
        $userPreferences = $this->userPreferenceService->getPreferences($userId);
        $currentTime = new \DateTime();
        $currentWeather = $this->weatherService->getCurrentWeather($userPreferences->getLocation());

        $contextMessage = $this->generateContextMessage($currentTime, $userPreferences, $currentWeather);

        $modifiedPrompt = $input->prompt;
        if ($modifiedPrompt instanceof Prompt) {
            $modifiedPrompt = $modifiedPrompt->withAddedMessage(
                MessagePrompt::system($contextMessage),
            );
        }

        return $next($input->withPrompt($modifiedPrompt));
    }

    private function generateContextMessage(\DateTime $currentTime, $userPreferences, $currentWeather): string
    {
        $timeOfDay = $this->getTimeOfDay($currentTime);
        $greeting = $this->getGreeting($timeOfDay);

        return <<<PROMPT
{$greeting} Here's some context for this conversation:
- It's currently {$timeOfDay}.
- The weather is {$currentWeather->getDescription()} with a temperature of {$currentWeather->getTemperature()}°C.
- The user prefers {$userPreferences->getCommunicationStyle()} communication.
- The user's interests include: {$this->formatInterests($userPreferences->getInterests())}.

Please take this context into account when generating responses.
PROMPT;
    }

    private function getTimeOfDay(\DateTime $time): string
    {
        $hour = (int) $time->format('G');
        return match (true) {
            $hour >= 5 && $hour < 12 => 'morning',
            $hour >= 12 && $hour < 18 => 'afternoon',
            $hour >= 18 && $hour < 22 => 'evening',
            default => 'night',
        };
    }

    private function getGreeting(string $timeOfDay): string
    {
        return match ($timeOfDay) {
            'morning' => 'Good morning!',
            'afternoon' => 'Good afternoon!',
            'evening' => 'Good evening!',
            'night' => 'Hello!',
        };
    }

    private function formatInterests(array $interests): string
    {
        return \implode(', ', \array_map(fn($interest) => \strtolower($interest), $interests));
    }
}
```

Then, add your custom interceptor to the pipeline:

```php
$generator = $generator->withInterceptor(new ContextAwarePromptInjector(...));
```

## Implementing PromptContextInterface

The `PromptGeneratorInput` includes a `context` property of type `PromptContextInterface`. This interface allows you to
pass custom context data to your interceptors. To use it effectively, you need to create your own implementation of this
interface.

Here's an example of how you might implement the `PromptContextInterface`:

```php
use LLM\Agents\LLM\PromptContextInterface;

class ChatContext implements PromptContextInterface
{
    public function __construct(
        private string $userId,
        private array $sessionData = [],
    ) {}

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSessionData(): array
    {
        return $this->sessionData;
    }

    // Add any other methods you need for your specific use case
}
```

Then, when generating a prompt, you would pass an instance of your custom context:

```php
$context = new ChatContext($userId, $sessionData);
$prompt = $generator->generate($agent, $userPrompt, $context);
```

In your custom interceptors, you can then access this context data:

```php
class ContextAwarePromptInjector implements PromptInterceptorInterface
{
    public function generate(PromptGeneratorInput $input, InterceptorHandler $next): PromptInterface
    {
        $userId = $input->context->getUserId();
        $sessionData = $input->context->getSessionData();

        // Use this data to customize your prompt
        // ...

        return $next($input);
    }
}
```

By implementing your own `PromptContextInterface`, you can pass any necessary data from your application to your
interceptors, allowing for highly customized and context-aware prompt generation.

## Want to help out? 🤝

We love contributions! If you've got ideas to make this agent even cooler, here's how you can chip in:

1. Fork the repo
2. Make your changes
3. Create a new Pull Request

Just make sure your code is clean, well-commented, and follows PSR-12 coding standards.

## License 📄

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

That's all, folks! If you've got any questions or run into any trouble, don't hesitate to open an issue.