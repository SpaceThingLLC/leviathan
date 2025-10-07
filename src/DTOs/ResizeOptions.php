<?php

declare(strict_types=1);

namespace Leviathan\DTOs;

/**
 * Data Transfer Object for image resize options.
 */
class ResizeOptions
{
    /**
     * Create a new ResizeOptions instance.
     *
     * @param  int  $width  Target width in pixels
     * @param  int  $height  Target height in pixels
     * @param  string  $strategy  Resize strategy (exact, portrait, landscape, auto, fit, crop, square, fill)
     * @param  string|null  $background  Background color for fill strategy (hex)
     * @param  bool  $enhance  Whether to enable smart image enhancement
     */
    public function __construct(
        public readonly int $width,
        public readonly int $height,
        public readonly string $strategy = 'auto',
        public readonly ?string $background = null,
        public readonly bool $enhance = false,
    ) {
        if ($this->width < 1 || $this->height < 1) {
            throw new \InvalidArgumentException('Width and height must be positive integers');
        }

        $validStrategies = ['exact', 'portrait', 'landscape', 'auto', 'fit', 'crop', 'square', 'fill'];
        if (! in_array($this->strategy, $validStrategies)) {
            throw new \InvalidArgumentException("Invalid resize strategy: {$this->strategy}");
        }

        if ($this->background !== null && ! preg_match('/^#?[0-9a-fA-F]{6}$/', $this->background)) {
            throw new \InvalidArgumentException('Background must be a valid hex color');
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
            'width' => $this->width,
            'height' => $this->height,
            'strategy' => $this->strategy,
        ];

        if ($this->background !== null) {
            $data['background'] = ltrim($this->background, '#');
        }

        if ($this->enhance) {
            $data['enhance'] = true;
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
        $width = is_int($options['width'] ?? null)
            ? $options['width']
            : throw new \InvalidArgumentException('Width is required');
        $height = is_int($options['height'] ?? null)
            ? $options['height']
            : throw new \InvalidArgumentException('Height is required');
        $strategy = is_string($options['strategy'] ?? null) ? $options['strategy'] : 'auto';
        $background = is_string($options['background'] ?? null) ? $options['background'] : null;
        $enhance = is_bool($options['enhance'] ?? null) ? $options['enhance'] : false;

        return new self(
            width: $width,
            height: $height,
            strategy: $strategy,
            background: $background,
            enhance: $enhance,
        );
    }
}
