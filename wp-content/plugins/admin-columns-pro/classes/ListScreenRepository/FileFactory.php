<?php

declare(strict_types=1);

namespace ACP\ListScreenRepository;

use AC\ListScreenRepository\Rules;
use AC\ListScreenRepository\Storage;
use ACP\ListScreenRepository;
use ACP\Search\SegmentRepository;
use ACP\Storage\AbstractDecoderFactory;
use ACP\Storage\Directory;
use ACP\Storage\EncoderFactory;
use ACP\Storage\Serializer;
use InvalidArgumentException;

final class FileFactory implements Storage\ListScreenRepositoryFactory
{

    private $encoder_factory;

    private $decoder_factory;

    private $serializer;

    private $i18n_serializer_factory;

    private $segment_file_factory;

    public function __construct(
        EncoderFactory $encoder_factory,
        AbstractDecoderFactory $decoder_factory,
        Serializer\PhpSerializer\File $serializer,
        Serializer\PhpSerializer\I18nFactory $i18n_serializer_factory,
        SegmentRepository\FileFactory $segment_file_factory
    ) {
        $this->encoder_factory = $encoder_factory;
        $this->decoder_factory = $decoder_factory;
        $this->serializer = $serializer;
        $this->i18n_serializer_factory = $i18n_serializer_factory;
        $this->segment_file_factory = $segment_file_factory;
    }

    public function create(
        string $path,
        bool $writable,
        Rules $rules = null,
        string $i18n_text_domain = null
    ): Storage\ListScreenRepository {
        if ($path === '') {
            throw new InvalidArgumentException('Invalid path.');
        }

        $serializer = $this->serializer;

        if ($i18n_text_domain) {
            $serializer = $this->i18n_serializer_factory->create($serializer, $i18n_text_domain);
        }

        $file = new ListScreenRepository\CachedFile(
            new ListScreenRepository\File(
                new Directory($path),
                $this->decoder_factory,
                $this->encoder_factory,
                $serializer,
                $this->segment_file_factory
            )
        );

        return new Storage\ListScreenRepository($file, $writable, $rules);
    }

}