<?php

namespace YOOtheme\Theme\Joomla;

/**
 * @phpstan-type Stat array{
 *  dev: int,
 *  ino: int,
 *  mode: int,
 *  nlink: int,
 *  uid: int,
 *  gid: int,
 *  rdev: int,
 *  size: int,
 *  atime: int,
 *  mtime: int,
 *  ctime: int,
 *  blksize: numeric-string,
 *  blocks: float
 * }
 */
class StreamWrapper
{
    /**
     * @var ?resource
     * @link https://github.com/phpspec/phpspec/pull/1435
     */
    public $context;

    /**
     * @var Stat
     */
    protected $stat;

    protected int $length;

    protected int $position;

    protected string $output;

    /**
     * @var array<string, mixed>
     */
    protected static array $outputs = [];

    /**
     * @var array<string, callable>
     */
    protected static array $objects = [];

    /**
     * Retrieve information about a file.
     *
     * @return Stat|false
     */
    public function url_stat(string $path)
    {
        if (is_callable($object = static::getObject($path))) {
            static::setOutput($path, $object($path));
        }

        if (is_string($output = static::getOutput($path))) {
            return static::getStat($output);
        }

        return false;
    }

    /**
     * Function to open file or url
     */
    public function stream_open(string $path): bool
    {
        if (!is_string($output = static::getOutput($path))) {
            return false;
        }

        $this->stat = static::getStat($output);
        $this->length = strlen($output);
        $this->position = 0;
        $this->output = $output;

        return true;
    }

    /**
     * Read stream
     */
    public function stream_read(int $count): string
    {
        $result = substr($this->output, $this->position, $count);

        $this->position += strlen($result);

        return $result;
    }

    /**
     * Retrieve information about a file resource
     *
     * @return Stat
     */
    public function stream_stat()
    {
        return $this->stat;
    }

    /**
     * Function to get the current position of the stream
     */
    public function stream_tell(): int
    {
        return $this->position;
    }

    /**
     * Function to test for end of file pointer
     */
    public function stream_eof(): bool
    {
        return $this->position >= $this->length;
    }

    /**
     * The read write position updates in response to $offset and $whence
     */
    public function stream_seek(int $offset, int $whence): bool
    {
        switch ($whence) {
            case \SEEK_SET:
                if ($offset < $this->length && $offset >= 0) {
                    $this->position = $offset;
                    return true;
                }

                break;

            case \SEEK_CUR:
                if ($offset >= 0) {
                    $this->position += $offset;
                    return true;
                }

                break;

            case \SEEK_END:
                if ($this->length + $offset >= 0) {
                    $this->position = $this->length + $offset;
                    return true;
                }

                break;
        }

        return false;
    }

    /**
     * Change stream options
     */
    public function stream_set_option(): bool
    {
        return true;
    }

    /**
     * Sets a object
     */
    public static function setObject(callable $object): string
    {
        $key = spl_object_hash($object);

        static::$objects[$key] = $object;

        return $key;
    }

    /**
     * Gets an object
     */
    protected static function getObject(string $path): ?callable
    {
        $path = substr($path, strpos($path, '://') + 3);

        foreach (static::$objects as $key => $object) {
            if (str_starts_with($path, $key)) {
                return $object;
            }
        }

        return null;
    }

    /**
     * Sets an output
     *
     * @param mixed $output
     */
    protected static function setOutput(string $path, $output): void
    {
        if (is_string($output)) {
            $output = var_export($output, true);
            $output = "<?php echo $output;";
        }

        static::$outputs[$path] = $output;
    }

    /**
     * Gets an output
     *
     * @return mixed
     */
    protected static function getOutput(string $path)
    {
        return static::$outputs[$path] ?? null;
    }

    /**
     * Retrieve file information for a string
     *
     * @return Stat
     */
    protected static function getStat(string $string)
    {
        $time = time();
        $length = strlen($string);

        return [
            'dev' => 0,
            'ino' => 0,
            'mode' => 0,
            'nlink' => 1,
            'uid' => 0,
            'gid' => 0,
            'rdev' => 0,
            'size' => $length,
            'atime' => $time,
            'mtime' => $time,
            'ctime' => $time,
            'blksize' => '512',
            'blocks' => ceil($length / 512),
        ];
    }
}
