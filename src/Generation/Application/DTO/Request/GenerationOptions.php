<?php

declare(strict_types=1);

namespace Tenqz\Ollama\Generation\Application\DTO\Request;

/**
 * DTO for Ollama generate "options" payload.
 */
class GenerationOptions
{
    /**
     * Size of the context window used to generate the next token. Default: 4096
     *
     * @var int|null
     */
    private $numCtx;

    /**
     * How far back the model looks to prevent repetition. Default: 64 (0 = disabled, -1 = num_ctx)
     *
     * @var int|null
     */
    private $repeatLastN;

    /**
     * How strongly to penalize repetitions. Higher penalizes more (e.g., 1.5), lower is more lenient (e.g., 0.9). Default: 1.1
     *
     * @var float|null
     */
    private $repeatPenalty;

    /**
     * The temperature of the model. Increasing the temperature will make the model answer more creatively. Default: 0.8
     *
     * @var float|null
     */
    private $temperature;

    /**
     * Sets the random number seed to use for generation. Setting this to a specific number will make
     * the model generate the same text for the same prompt. Default: 0
     *
     * @var int|null
     */
    private $seed;

    /**
     * Stop sequences to use. When encountered the LLM will stop generating text and return.
     * Multiple sequences allowed.
     *
     * @var string[]|null
     */
    private $stop;

    /**
     * Maximum number of tokens to predict when generating text. Default: -1 (infinite generation)
     *
     * @var int|null
     */
    private $numPredict;

    /**
     * Reduces the probability of generating nonsense. Higher (e.g., 100) gives more diverse answers; lower (e.g., 10) is conservative. Default: 40
     *
     * @var int|null
     */
    private $topK;

    /**
     * Works with top_k. Higher (e.g., 0.95) leads to more diverse text, lower (e.g., 0.5) is more focused. Default: 0.9
     *
     * @var float|null
     */
    private $topP;

    /**
     * Alternative to top_p to balance quality and variety. p is the minimum probability relative to the most likely token. Default: 0.0
     *
     * @var float|null
     */
    private $minP;

    /**
     * @param int|null $numKeep
     * @return self
     */

    /**
     * @param int|null $seed Random seed (deterministic outputs when set)
     * @return self
     */
    public function setSeed(?int $seed): self
    {
        $this->seed = $seed;

        return $this;
    }

    /**
     * @param int|null $numPredict
     * @return self
     */
    public function setNumPredict(?int $numPredict): self
    {
        $this->numPredict = $numPredict;

        return $this;
    }

    /**
     * @param int|null $topK
     * @return self
     */
    public function setTopK(?int $topK): self
    {
        $this->topK = $topK;

        return $this;
    }

    /**
     * @param float|null $topP
     * @return self
     */
    public function setTopP(?float $topP): self
    {
        $this->topP = $topP;

        return $this;
    }

    /**
     * @param float|null $minP
     * @return self
     */
    public function setMinP(?float $minP): self
    {
        $this->minP = $minP;

        return $this;
    }

    /**
     * @param float|null $typicalP
     * @return self
     */

    /**
     * @param int|null $repeatLastN How far back to look to penalize repetition (0=disabled, -1=num_ctx)
     * @return self
     */
    public function setRepeatLastN(?int $repeatLastN): self
    {
        $this->repeatLastN = $repeatLastN;

        return $this;
    }

    /**
     * @param float|null $temperature
     * @return self
     */
    public function setTemperature(?float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @param float|null $repeatPenalty
     * @return self
     */
    public function setRepeatPenalty(?float $repeatPenalty): self
    {
        $this->repeatPenalty = $repeatPenalty;

        return $this;
    }

    /**
     * @param float|null $presencePenalty
     * @return self
     */

    /**
     * @param string[]|null $stop Stop sequences
     * @return self
     */
    public function setStop(?array $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    /**
        * @param int|null $numCtx
        * @return self
        */
    public function setNumCtx(?int $numCtx): self
    {
        $this->numCtx = $numCtx;

        return $this;
    }

    /** @return int|null */
    public function getSeed(): ?int
    {
        return $this->seed;
    }

    /** @return int|null */
    public function getNumPredict(): ?int
    {
        return $this->numPredict;
    }

    /** @return int|null */
    public function getTopK(): ?int
    {
        return $this->topK;
    }

    /** @return float|null */
    public function getTopP(): ?float
    {
        return $this->topP;
    }

    /** @return float|null */
    public function getMinP(): ?float
    {
        return $this->minP;
    }

    /** @return float|null */

    /** @return int|null */
    public function getRepeatLastN(): ?int
    {
        return $this->repeatLastN;
    }

    /** @return float|null */
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    /** @return float|null */
    public function getRepeatPenalty(): ?float
    {
        return $this->repeatPenalty;
    }

    /** @return float|null */

    /** @return string[]|null */
    public function getStop(): ?array
    {
        return $this->stop;
    }

    /** @return int|null */
    public function getNumCtx(): ?int
    {
        return $this->numCtx;
    }

    /**
     * Convert to array for API (snake_case keys as expected by Ollama).
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];

        if ($this->seed !== null) {
            $result['seed'] = $this->seed;
        }
        if ($this->numPredict !== null) {
            $result['num_predict'] = $this->numPredict;
        }
        if ($this->topK !== null) {
            $result['top_k'] = $this->topK;
        }
        if ($this->topP !== null) {
            $result['top_p'] = $this->topP;
        }
        if ($this->minP !== null) {
            $result['min_p'] = $this->minP;
        }
        if ($this->repeatLastN !== null) {
            $result['repeat_last_n'] = $this->repeatLastN;
        }
        if ($this->temperature !== null) {
            $result['temperature'] = $this->temperature;
        }
        if ($this->repeatPenalty !== null) {
            $result['repeat_penalty'] = $this->repeatPenalty;
        }
        if ($this->stop !== null && count($this->stop) > 0) {
            $result['stop'] = array_values($this->stop);
        }
        if ($this->numCtx !== null) {
            $result['num_ctx'] = $this->numCtx;
        }


        return $result;
    }
}
