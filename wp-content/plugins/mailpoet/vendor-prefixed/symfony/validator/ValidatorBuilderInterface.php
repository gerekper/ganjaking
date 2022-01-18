<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Validator\ValidatorInterface;
interface ValidatorBuilderInterface
{
 public function addObjectInitializer(ObjectInitializerInterface $initializer);
 public function addObjectInitializers(array $initializers);
 public function addXmlMapping($path);
 public function addXmlMappings(array $paths);
 public function addYamlMapping($path);
 public function addYamlMappings(array $paths);
 public function addMethodMapping($methodName);
 public function addMethodMappings(array $methodNames);
 public function enableAnnotationMapping(Reader $annotationReader = null);
 public function disableAnnotationMapping();
 public function setMetadataFactory(MetadataFactoryInterface $metadataFactory);
 public function setMetadataCache(CacheInterface $cache);
 public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $validatorFactory);
 public function setTranslator(TranslatorInterface $translator);
 public function setTranslationDomain($translationDomain);
 public function getValidator();
}
