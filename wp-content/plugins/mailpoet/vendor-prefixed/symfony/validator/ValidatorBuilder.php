<?php
namespace MailPoetVendor\Symfony\Component\Validator;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Doctrine\Common\Annotations\AnnotationReader;
use MailPoetVendor\Doctrine\Common\Annotations\CachedReader;
use MailPoetVendor\Doctrine\Common\Annotations\PsrCachedReader;
use MailPoetVendor\Doctrine\Common\Annotations\Reader;
use MailPoetVendor\Doctrine\Common\Cache\ArrayCache;
use MailPoetVendor\Doctrine\Common\Cache\Psr6\DoctrineProvider;
use MailPoetVendor\Psr\Cache\CacheItemPoolInterface;
use MailPoetVendor\Symfony\Component\Cache\Adapter\ArrayAdapter;
use MailPoetVendor\Symfony\Component\Cache\DoctrineProvider as SymfonyDoctrineProvider;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Component\Validator\Context\ExecutionContextFactory;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
use MailPoetVendor\Symfony\Component\Validator\Exception\ValidatorException;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderChain;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\XmlFileLoader;
use MailPoetVendor\Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;
use MailPoetVendor\Symfony\Component\Validator\Util\LegacyTranslatorProxy;
use MailPoetVendor\Symfony\Component\Validator\Validator\RecursiveValidator;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorTrait;
// Help opcache.preload discover always-needed symbols
\class_exists(TranslatorInterface::class);
\class_exists(LocaleAwareInterface::class);
\class_exists(TranslatorTrait::class);
class ValidatorBuilder implements ValidatorBuilderInterface
{
 private $initializers = [];
 private $loaders = [];
 private $xmlMappings = [];
 private $yamlMappings = [];
 private $methodMappings = [];
 private $annotationReader;
 private $metadataFactory;
 private $validatorFactory;
 private $mappingCache;
 private $translator;
 private $translationDomain;
 public function addObjectInitializer(ObjectInitializerInterface $initializer)
 {
 $this->initializers[] = $initializer;
 return $this;
 }
 public function addObjectInitializers(array $initializers)
 {
 $this->initializers = \array_merge($this->initializers, $initializers);
 return $this;
 }
 public function addXmlMapping($path)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->xmlMappings[] = $path;
 return $this;
 }
 public function addXmlMappings(array $paths)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->xmlMappings = \array_merge($this->xmlMappings, $paths);
 return $this;
 }
 public function addYamlMapping($path)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->yamlMappings[] = $path;
 return $this;
 }
 public function addYamlMappings(array $paths)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->yamlMappings = \array_merge($this->yamlMappings, $paths);
 return $this;
 }
 public function addMethodMapping($methodName)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->methodMappings[] = $methodName;
 return $this;
 }
 public function addMethodMappings(array $methodNames)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot add custom mappings after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->methodMappings = \array_merge($this->methodMappings, $methodNames);
 return $this;
 }
 public function enableAnnotationMapping(Reader $annotationReader = null)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot enable annotation mapping after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->annotationReader = $annotationReader ?? $this->createAnnotationReader();
 return $this;
 }
 public function disableAnnotationMapping()
 {
 $this->annotationReader = null;
 return $this;
 }
 public function setMetadataFactory(MetadataFactoryInterface $metadataFactory)
 {
 if (\count($this->xmlMappings) > 0 || \count($this->yamlMappings) > 0 || \count($this->methodMappings) > 0 || null !== $this->annotationReader) {
 throw new ValidatorException('You cannot set a custom metadata factory after adding custom mappings. You should do either of both.');
 }
 $this->metadataFactory = $metadataFactory;
 return $this;
 }
 public function setMetadataCache(CacheInterface $cache)
 {
 @\trigger_error(\sprintf('%s is deprecated since Symfony 4.4. Use setMappingCache() instead.', __METHOD__), \E_USER_DEPRECATED);
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot set a custom metadata cache after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->mappingCache = $cache;
 return $this;
 }
 public function setMappingCache(CacheItemPoolInterface $cache)
 {
 if (null !== $this->metadataFactory) {
 throw new ValidatorException('You cannot set a custom mapping cache after setting a custom metadata factory. Configure your metadata factory instead.');
 }
 $this->mappingCache = $cache;
 return $this;
 }
 public function setConstraintValidatorFactory(ConstraintValidatorFactoryInterface $validatorFactory)
 {
 $this->validatorFactory = $validatorFactory;
 return $this;
 }
 public function setTranslator(LegacyTranslatorInterface $translator)
 {
 $this->translator = $translator;
 while ($this->translator instanceof LegacyTranslatorProxy) {
 $this->translator = $this->translator->getTranslator();
 }
 return $this;
 }
 public function setTranslationDomain($translationDomain)
 {
 $this->translationDomain = $translationDomain;
 return $this;
 }
 public function addLoader(LoaderInterface $loader)
 {
 $this->loaders[] = $loader;
 return $this;
 }
 public function getLoaders()
 {
 $loaders = [];
 foreach ($this->xmlMappings as $xmlMapping) {
 $loaders[] = new XmlFileLoader($xmlMapping);
 }
 foreach ($this->yamlMappings as $yamlMappings) {
 $loaders[] = new YamlFileLoader($yamlMappings);
 }
 foreach ($this->methodMappings as $methodName) {
 $loaders[] = new StaticMethodLoader($methodName);
 }
 if ($this->annotationReader) {
 $loaders[] = new AnnotationLoader($this->annotationReader);
 }
 return \array_merge($loaders, $this->loaders);
 }
 public function getValidator()
 {
 $metadataFactory = $this->metadataFactory;
 if (!$metadataFactory) {
 $loaders = $this->getLoaders();
 $loader = null;
 if (\count($loaders) > 1) {
 $loader = new LoaderChain($loaders);
 } elseif (1 === \count($loaders)) {
 $loader = $loaders[0];
 }
 $metadataFactory = new LazyLoadingMetadataFactory($loader, $this->mappingCache);
 }
 $validatorFactory = $this->validatorFactory ?? new ConstraintValidatorFactory();
 $translator = $this->translator;
 if (null === $translator) {
 $translator = new class implements TranslatorInterface, LocaleAwareInterface
 {
 use TranslatorTrait;
 };
 // Force the locale to be 'en' when no translator is provided rather than relying on the Intl default locale
 // This avoids depending on Intl or the stub implementation being available. It also ensures that Symfony
 // validation messages are pluralized properly even when the default locale gets changed because they are in
 // English.
 $translator->setLocale('en');
 }
 $contextFactory = new ExecutionContextFactory($translator, $this->translationDomain);
 return new RecursiveValidator($contextFactory, $metadataFactory, $validatorFactory, $this->initializers);
 }
 private function createAnnotationReader() : Reader
 {
 if (!\class_exists(AnnotationReader::class)) {
 throw new LogicException('Enabling annotation based constraint mapping requires the packages doctrine/annotations and symfony/cache to be installed.');
 }
 // Doctrine Annotation >= 1.13, Symfony Cache
 if (\class_exists(PsrCachedReader::class) && \class_exists(ArrayAdapter::class)) {
 return new PsrCachedReader(new AnnotationReader(), new ArrayAdapter());
 }
 // Doctrine Annotations < 1.13, Doctrine Cache >= 1.11, Symfony Cache
 if (\class_exists(CachedReader::class) && \class_exists(DoctrineProvider::class) && \class_exists(ArrayAdapter::class)) {
 return new CachedReader(new AnnotationReader(), DoctrineProvider::wrap(new ArrayAdapter()));
 }
 // Doctrine Annotations < 1.13, Doctrine Cache < 1.11, Symfony Cache
 if (\class_exists(CachedReader::class) && !\class_exists(DoctrineProvider::class) && \class_exists(ArrayAdapter::class)) {
 return new CachedReader(new AnnotationReader(), new SymfonyDoctrineProvider(new ArrayAdapter()));
 }
 // Doctrine Annotations < 1.13, Doctrine Cache < 1.11
 if (\class_exists(CachedReader::class) && \class_exists(ArrayCache::class)) {
 return new CachedReader(new AnnotationReader(), new ArrayCache());
 }
 // Doctrine Annotation >= 1.13, Doctrine Cache >= 2, no Symfony Cache
 if (\class_exists(PsrCachedReader::class)) {
 throw new LogicException('Enabling annotation based constraint mapping requires the package symfony/cache to be installed.');
 }
 // Doctrine Annotation (<1.13 || >2), no Doctrine Cache, no Symfony Cache
 throw new LogicException('Enabling annotation based constraint mapping requires the packages doctrine/annotations (>=1.13) and symfony/cache to be installed.');
 }
}
