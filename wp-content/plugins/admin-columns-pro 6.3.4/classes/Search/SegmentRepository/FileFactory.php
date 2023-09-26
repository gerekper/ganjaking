<?php

declare(strict_types=1);

namespace ACP\Search\SegmentRepository;

use ACP\Search\Storage;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;

final class FileFactory
{

    private $abstract_decoder_factory;

    private $encoder_factory;

    private $serializer;

    public function __construct(
        AbstractDecoderFactory $abstract_decoder_factory,
        EncoderFactory $encoder_factory,
        Serializer\PhpSerializer\File $serializer
    ) {
        $this->abstract_decoder_factory = $abstract_decoder_factory;
        $this->encoder_factory = $encoder_factory;
        $this->serializer = $serializer;
    }

    public function create(Directory $directory): File
    {
        return new File(
            $directory,
            $this->abstract_decoder_factory,
            $this->encoder_factory,
            $this->serializer
        );
    }

}