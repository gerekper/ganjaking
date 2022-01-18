<?php
namespace MailPoetVendor\Symfony\Component\Validator\Constraints;
if (!defined('ABSPATH')) exit;
use MailPoetVendor\Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use MailPoetVendor\Symfony\Component\Validator\Constraint;
use MailPoetVendor\Symfony\Component\Validator\ConstraintValidator;
use MailPoetVendor\Symfony\Component\Validator\Exception\LogicException;
use MailPoetVendor\Symfony\Component\Validator\Exception\UnexpectedTypeException;
class ExpressionValidator extends ConstraintValidator
{
 private $expressionLanguage;
 public function __construct($expressionLanguage = null)
 {
 if (\func_num_args() > 1) {
 @\trigger_error(\sprintf('The "%s" instance should be passed as "%s" first argument instead of second argument since 4.4.', ExpressionLanguage::class, __METHOD__), \E_USER_DEPRECATED);
 $expressionLanguage = \func_get_arg(1);
 if (null !== $expressionLanguage && !$expressionLanguage instanceof ExpressionLanguage) {
 throw new \TypeError(\sprintf('Argument 2 passed to "%s()" must be an instance of "%s" or null, "%s" given. Since 4.4, passing it as the second argument is deprecated and will trigger a deprecation. Pass it as the first argument instead.', __METHOD__, ExpressionLanguage::class, \is_object($expressionLanguage) ? \get_class($expressionLanguage) : \gettype($expressionLanguage)));
 }
 } elseif (null !== $expressionLanguage && !$expressionLanguage instanceof ExpressionLanguage) {
 @\trigger_error(\sprintf('The "%s" first argument must be an instance of "%s" or null since 4.4. "%s" given', __METHOD__, ExpressionLanguage::class, \is_object($expressionLanguage) ? \get_class($expressionLanguage) : \gettype($expressionLanguage)), \E_USER_DEPRECATED);
 $expressionLanguage = null;
 }
 $this->expressionLanguage = $expressionLanguage;
 }
 public function validate($value, Constraint $constraint)
 {
 if (!$constraint instanceof Expression) {
 throw new UnexpectedTypeException($constraint, Expression::class);
 }
 $variables = $constraint->values;
 $variables['value'] = $value;
 $variables['this'] = $this->context->getObject();
 if (!$this->getExpressionLanguage()->evaluate($constraint->expression, $variables)) {
 $this->context->buildViolation($constraint->message)->setParameter('{{ value }}', $this->formatValue($value, self::OBJECT_TO_STRING))->setCode(Expression::EXPRESSION_FAILED_ERROR)->addViolation();
 }
 }
 private function getExpressionLanguage() : ExpressionLanguage
 {
 if (null === $this->expressionLanguage) {
 if (!\class_exists(ExpressionLanguage::class)) {
 throw new LogicException('Unable to use expressions as the Symfony ExpressionLanguage component is not installed.');
 }
 $this->expressionLanguage = new ExpressionLanguage();
 }
 return $this->expressionLanguage;
 }
}
