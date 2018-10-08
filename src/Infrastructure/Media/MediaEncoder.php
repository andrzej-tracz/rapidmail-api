<?php

namespace App\Infrastructure\Media;

use App\Infrastructure\Utils\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\RouterInterface;
use Intervention\Image\ImageManagerStatic as Image;

class MediaEncoder
{
    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var RouterInterface
     */
    protected $generator;

    /**
     * @var Filesystem
     */
    protected $fs;

    protected $options = [
        'max_width' => 1600,
        'max_height' => 1600,
    ];

    public function __construct(string $path, RouterInterface $generator, Filesystem $filesystem)
    {
        $this->storagePath = $path;
        $this->generator = $generator;
        $this->fs = $filesystem;
    }

    public function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    public function encodeFromBase(string $base64)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            $data = substr($base64, strpos($base64, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \InvalidArgumentException('Invalid image type');
            }

            $data = base64_decode($data);
            if ($data) {
                return $this->store($type, $data);
            }
        }

        throw new \InvalidArgumentException('Invalid raw input');
    }

    /**
     * @param $type
     * @param $data
     *
     * @return bool|int
     */
    public function store($type, $data)
    {
        $rand = Str::random(30);
        $time = time();
        $name = "{$time}-{$rand}.{$type}";
        $fullName = "{$this->storagePath}/{$name}";

        $this->fs->appendToFile($fullName, $data);

        $this->processImage($fullName);

        $base = $this->generator->getContext()->getScheme()
            .'://'.$this->generator->getContext()->getHost()
            .$this->generator->getContext()->getBaseUrl();

        return  "{$base}/media/{$name}";
    }

    protected function processImage($path)
    {
        Image::configure(['driver' => 'gd']);
        $image = Image::make($path);

        $image->widen($this->options['max_width'], function ($constraint) {
            $constraint->upsize();
        });

        $image->heighten($this->options['max_height'], function ($constraint) {
            $constraint->upsize();
        });

        $image->save($path);
    }
}
