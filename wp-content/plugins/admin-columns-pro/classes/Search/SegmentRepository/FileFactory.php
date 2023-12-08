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

    public function create(
        Directory $directory,
        AbstractDecoderFactory $abstract_decoder_factory,
        EncoderFactory $encoder_factory,
        Serializer $serializer
    ): File {
        return new File(
            $directory,
            $abstract_decoder_factory,
            $encoder_factory,
            $serializer
        );
    }

}