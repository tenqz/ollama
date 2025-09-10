<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Application\DTO\Request;

/**
 * Data Transfer Object for text generation request.
 */
class GenerationRequest
{
    /**
     * @var string Model name
     */
    private $model;

    /**
     * @var string|null Prompt to generate a response for
     */
    private $prompt;

    /**
     * @var string|null The text to append after the model response
     */
    private $suffix;

    /**
     * @var string|null System prompt to set role/rules for generation
     */
    private $system;

    /**
     * @var string|null Output template for formatting the model response
     */
    private $template;

    /**
     * @var string|null Output format (e.g. 'json')
     */
    private $format;

    /**
     * @var bool Whether to stream the response
     */
    private $stream = false;

    /**
     * @var bool|null Whether the model should "think" before responding
     */
    private $think;

    /**
     * @var string[]|null Base64-encoded images for multimodal models
     */
    private $images;

    /**
     * @var GenerationOptions|null Additional model parameters
     */
    private $options;

    /**
     * @param string $model Model name to use for generation
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string|null $prompt
     * @return self
     */
    public function setPrompt(?string $prompt): self
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrompt(): ?string
    {
        return $this->prompt;
    }

    /**
     * @param string|null $suffix
     * @return self
     */
    public function setSuffix(?string $suffix): self
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    /**
     * @param string|null $system
     * @return self
     */
    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSystem(): ?string
    {
        return $this->system;
    }

    /**
     * @param string|null $template
     * @return self
     */
    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string|null $format
     * @return self
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param bool $stream
     * @return self
     */
    public function setStream(bool $stream): self
    {
        $this->stream = $stream;

        return $this;
    }

    /**
     * @return bool
     */
    public function getStream(): bool
    {
        return $this->stream;
    }

    /**
     * @param bool|null $think Enable or disable thinking mode for supported models
     * @return self
     */
    public function setThink(?bool $think): self
    {
        $this->think = $think;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getThink(): ?bool
    {
        return $this->think;
    }

    /**
     * @param string[]|null $images
     * @return self
     */
    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @param GenerationOptions|null $options
     * @return self
     */
    public function setOptions(?GenerationOptions $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return GenerationOptions|null
     */
    public function getOptions(): ?GenerationOptions
    {
        return $this->options;
    }

    /**
     * Convert request to array for API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [
            'model'  => $this->model,
            'stream' => $this->stream,
        ];

        // Add prompt only if it's set
        if ($this->prompt !== null) {
            $result['prompt'] = $this->prompt;
        }

        // Add suffix only if it's set
        if ($this->suffix !== null) {
            $result['suffix'] = $this->suffix;
        }

        // Add system only if it's set
        if ($this->system !== null) {
            $result['system'] = $this->system;
        }

        // Add template only if it's set
        if ($this->template !== null) {
            $result['template'] = $this->template;
        }

        // Add format only if it's set
        if ($this->format !== null) {
            $result['format'] = $this->format;
        }

        // Add think only if it's set (null means omit)
        if ($this->think !== null) {
            $result['think'] = $this->think;
        }

        // Add images only if set and non-empty array
        if (is_array($this->images) && $this->images !== []) {
            $result['images'] = $this->images;
        }

        // Add options only if it's set and not empty
        if ($this->options instanceof GenerationOptions) {
            $optionsArray = $this->options->toArray();
            if ($optionsArray !== []) {
                $result['options'] = $optionsArray;
            }
        }

        return $result;
    }
}
