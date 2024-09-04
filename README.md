# Prompt generator for LLM Agents

[![PHP](https://img.shields.io/packagist/php-v/llm-agents-php/agent-site-status-checker.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/llm-agents/prompt-generator.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)
[![Total Downloads](https://img.shields.io/packagist/dt/llm-agents/prompt-generator.svg?style=flat-square)](https://packagist.org/packages/llm-agents/prompt-generator)

### Installation

First things first, let's get this package installed:

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

And that's it! Your Spiral app is now ready to use the agent.

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