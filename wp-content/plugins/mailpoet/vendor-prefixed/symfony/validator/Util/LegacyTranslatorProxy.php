<?php
namespace MailPoetVendor\Symfony\Component\Validator\Util;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use MailPoetVendor\Symfony\Contracts\Translation\LocaleAwareInterface;
use MailPoetVendor\Symfony\Contracts\Translation\TranslatorInterface;
class LegacyTranslatorProxy implements LegacyTranslatorInterface, TranslatorInterface
{
 private $translator;
 public function __construct($translator)
 {
 if ($translator instanceof LegacyTranslatorInterface) {
 // no-op
 } elseif (!$translator instanceof TranslatorInterface) {
 throw new \InvalidArgumentException(\sprintf('The translator passed to "%s()" must implement "%s" or "%s".', __METHOD__, TranslatorInterface::class, LegacyTranslatorInterface::class));
 } elseif (!$translator instanceof LocaleAwareInterface) {
 throw new \InvalidArgumentException(\sprintf('The translator passed to "%s()" must implement "%s".', __METHOD__, LocaleAwareInterface::class));
 }
 $this->translator = $translator;
 }
 public function getTranslator()
 {
 return $this->translator;
 }
 public function setLocale($locale)
 {
 $this->translator->setLocale($locale);
 }
 public function getLocale() : string
 {
 return $this->translator->getLocale();
 }
 public function trans($id, array $parameters = [], $domain = null, $locale = null) : string
 {
 return $this->translator->trans($id, $parameters, $domain, $locale);
 }
 public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) : string
 {
 return $this->translator->trans($id, ['%count%' => $number] + $parameters, $domain, $locale);
 }
}
