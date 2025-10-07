<?php

declare(strict_types=1);

namespace Leviathan\DTOs;

/**
 * Data Transfer Object for image optimization options.
 */
class OptimizationOptions
{
    /**
     * Create a new OptimizationOptions instance.
     *
     * @param  bool  $wait  Whether to process synchronously
     * @param  bool  $lossy  Whether to use lossy compression
     * @param  int|null  $quality  Quality setting for lossy compression (1-100)
     * @param  string|null  $format  Output format (jpg, png, webp, avif, gif)
     * @param  bool  $preserveMeta  Whether to preserve image metadata
     * @param  ResizeOptions|null  $resize  Resize options
     * @param  array<string, mixed>  $storage  External storage configuration
     * @param  string|null  $callbackUrl  Webhook URL for async processing
     */
    public function __construct(
        public readonly bool $wait = true,
        public readonly bool $lossy = false,
        public readonly ?int $quality = null,
        public readonly ?string $format = null,
        public readonly bool $preserveMeta = false,
        public readonly ?ResizeOptions $resize = null,
        public readonly array $storage = [],
        public readonly ?string $callbackUrl = null,
    ) {
        if ($this->quality !== null && ($this->quality < 1 || $this->quality > 100)) {
            throw new \InvalidArgumentException('Quality must be between 1 and 100');
        }

        if ($this->format !== null && ! in_array($this->format, ['jpg', 'png', 'webp', 'avif', 'gif'])) {
            throw new \InvalidArgumentException("Invalid format: {$this->format}");
        }
    }

    /**
     * Convert to array suitable for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'wait' => $this->wait,
            'lossy' => $this->lossy,
        ];

        if ($this->quality !== null) {
            $data['quality'] = $this->quality;
        }

        if ($this->format !== null) {
            $data['webp'] = $this->format === 'webp';
            $data['avif'] = $this->format === 'avif';
        }

        if ($this->preserveMeta) {
            $data['preserve_meta'] = ['date', 'copyright', 'geotag', 'orientation'];
        }

        if ($this->resize !== null) {
            $data['resize'] = $this->resize->toArray();
        }

        if (! empty($this->storage)) {
            $data['storage'] = $this->storage;
        }

        if ($this->callbackUrl !== null) {
            $data['callback_url'] = $this->callbackUrl;
        }

        return $data;
    }

    /**
     * Create from array of options.
     *
     * @param  array<string, mixed>  $options
     */
    public static function fromArray(array $options): self
    {
        $resize = null;
        if (isset($options['resize'])) {
            $resize = $options['resize'] instanceof ResizeOptions
                ? $options['resize']
                : ResizeOptions::fromArray(is_array($options['resize']) ? $options['resize'] : []);
        }

        $wait = is_bool($options['wait'] ?? null) ? $options['wait'] : true;
        $lossy = is_bool($options['lossy'] ?? null) ? $options['lossy'] : false;
        $quality = is_int($options['quality'] ?? null) ? $options['quality'] : null;
        $format = is_string($options['format'] ?? null) ? $options['format'] : null;
        $preserveMeta = is_bool($options['preserve_meta'] ?? null) ? $options['preserve_meta'] : false;
        $storage = is_array($options['storage'] ?? null) ? $options['storage'] : [];
        $callbackUrl = is_string($options['callback_url'] ?? null) ? $options['callback_url'] : null;

        return new self(
            wait: $wait,
            lossy: $lossy,
            quality: $quality,
            format: $format,
            preserveMeta: $preserveMeta,
            resize: $resize,
            storage: $storage,
            callbackUrl: $callbackUrl,
        );
    }
}
