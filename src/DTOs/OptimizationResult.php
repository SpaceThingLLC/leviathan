<?php

declare(strict_types=1);

namespace Leviathan\DTOs;

/**
 * Data Transfer Object for optimization result.
 */
class OptimizationResult
{
    /**
     * Create a new OptimizationResult instance.
     *
     * @param  bool  $success  Whether the optimization was successful
     * @param  string  $fileName  Original file name
     * @param  int  $originalSize  Original file size in bytes
     * @param  int  $krakedSize  Optimized file size in bytes
     * @param  int|float  $savedBytes  Bytes saved
     * @param  string  $krakedUrl  URL to the optimized image
     * @param  int|null  $originalWidth  Original image width
     * @param  int|null  $originalHeight  Original image height
     * @param  int|null  $krakedWidth  Optimized image width
     * @param  int|null  $krakedHeight  Optimized image height
     * @param  string|null  $originalType  Original image MIME type
     * @param  string|null  $krakedType  Optimized image MIME type
     */
    public function __construct(
        public readonly bool $success,
        public readonly string $fileName,
        public readonly int $originalSize,
        public readonly int $krakedSize,
        public readonly int|float $savedBytes,
        public readonly string $krakedUrl,
        public readonly ?int $originalWidth = null,
        public readonly ?int $originalHeight = null,
        public readonly ?int $krakedWidth = null,
        public readonly ?int $krakedHeight = null,
        public readonly ?string $originalType = null,
        public readonly ?string $krakedType = null,
    ) {}

    /**
     * Get the percentage of bytes saved.
     */
    public function getSavedPercentage(): float
    {
        if ($this->originalSize === 0) {
            return 0.0;
        }

        return round(($this->savedBytes / $this->originalSize) * 100, 2);
    }

    /**
     * Check if the image was resized.
     */
    public function wasResized(): bool
    {
        return $this->originalWidth !== $this->krakedWidth
            || $this->originalHeight !== $this->krakedHeight;
    }

    /**
     * Check if the format was converted.
     */
    public function wasConverted(): bool
    {
        return $this->originalType !== null
            && $this->krakedType !== null
            && $this->originalType !== $this->krakedType;
    }

    /**
     * Create from API response.
     *
     * @param  array<string, mixed>  $response
     */
    public static function fromResponse(array $response): self
    {
        $success = is_bool($response['success'] ?? null) ? $response['success'] : false;
        $fileName = is_string($response['file_name'] ?? null) ? $response['file_name'] : '';
        $originalSize = is_int($response['original_size'] ?? null) ? $response['original_size'] : 0;
        $krakedSize = is_int($response['kraked_size'] ?? null) ? $response['kraked_size'] : 0;
        $savedBytes = is_int($response['saved_bytes'] ?? null) || is_float($response['saved_bytes'] ?? null)
            ? $response['saved_bytes']
            : 0;
        $krakedUrl = is_string($response['kraked_url'] ?? null) ? $response['kraked_url'] : '';
        $originalWidth = is_int($response['original_width'] ?? null) ? $response['original_width'] : null;
        $originalHeight = is_int($response['original_height'] ?? null) ? $response['original_height'] : null;
        $krakedWidth = is_int($response['kraked_width'] ?? null) ? $response['kraked_width'] : null;
        $krakedHeight = is_int($response['kraked_height'] ?? null) ? $response['kraked_height'] : null;
        $originalType = is_string($response['original_type'] ?? null) ? $response['original_type'] : null;
        $krakedType = is_string($response['kraked_type'] ?? null) ? $response['kraked_type'] : null;

        return new self(
            success: $success,
            fileName: $fileName,
            originalSize: $originalSize,
            krakedSize: $krakedSize,
            savedBytes: $savedBytes,
            krakedUrl: $krakedUrl,
            originalWidth: $originalWidth,
            originalHeight: $originalHeight,
            krakedWidth: $krakedWidth,
            krakedHeight: $krakedHeight,
            originalType: $originalType,
            krakedType: $krakedType,
        );
    }

    /**
     * Convert to array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'file_name' => $this->fileName,
            'original_size' => $this->originalSize,
            'kraked_size' => $this->krakedSize,
            'saved_bytes' => $this->savedBytes,
            'saved_percentage' => $this->getSavedPercentage(),
            'kraked_url' => $this->krakedUrl,
            'original_width' => $this->originalWidth,
            'original_height' => $this->originalHeight,
            'kraked_width' => $this->krakedWidth,
            'kraked_height' => $this->krakedHeight,
            'original_type' => $this->originalType,
            'kraked_type' => $this->krakedType,
            'was_resized' => $this->wasResized(),
            'was_converted' => $this->wasConverted(),
        ];
    }
}
