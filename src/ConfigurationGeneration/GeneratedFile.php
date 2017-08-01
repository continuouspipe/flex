<?php

namespace ContinuousPipe\Flex\ConfigurationGeneration;

final class GeneratedFile
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string|null
     */
    private $contents;

    /**
     * @var string|null
     */
    private $failureReason;

    /**
     * @param string      $path
     * @param null|string $contents
     * @param null|string $failureReason
     */
    public function __construct(string $path, string $contents = null, string $failureReason = null)
    {
        $this->path = $path;
        $this->contents = $contents;
        $this->failureReason = $failureReason;
    }

    public static function failed(string $path, string $reason)
    {
        return new self($path, null, $reason);
    }

    public static function generated(string $path, string $contents)
    {
        return new self($path, $contents);
    }

    public function hasFailed() : bool
    {
        return null !== $this->failureReason;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return null|string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return null|string
     */
    public function getFailureReason()
    {
        return $this->failureReason;
    }
}
