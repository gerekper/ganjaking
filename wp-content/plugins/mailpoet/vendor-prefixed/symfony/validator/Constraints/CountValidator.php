<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedValueException;
class CountValidator extends ConstraintValidator
{
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Count) {
 throw new UnexpectedTypeException($constraint, Count::class);
 }
 if (null === $value) {
 return;
 }
 if (!\is_array($value) && !$value instanceof \Countable) {
 throw new UnexpectedValueException($value, 'array|\\Countable');
 }
 $count = \count($value);
 if (null !== $constraint->max && $count > $constraint->max) {
 $this->context->buildViolation($constraint->min == $constraint->max ? $constraint->exactMessage : $constraint->maxMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->max)->setInvalidValue($value)->setPlural((int) $constraint->max)->setCode(Count::TOO_MANY_ERROR)->addViolation();
 return;
 }
 if (null !== $constraint->min && $count < $constraint->min) {
 $this->context->buildViolation($constraint->min == $constraint->max ? $constraint->exactMessage : $constraint->minMessage)->setParameter('{{ count }}', $count)->setParameter('{{ limit }}', $constraint->min)->setInvalidValue($value)->setPlural((int) $constraint->min)->setCode(Count::TOO_FEW_ERROR)->addViolation();
 }
 }
}
