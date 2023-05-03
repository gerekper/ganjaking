<?php declare(strict_types = 1);

namespace MailPoet\Premium\Automation\Integrations\MailPoetPremium\Actions;

if (!defined('ABSPATH')) exit;


use MailPoet\Automation\Engine\Data\StepRunArgs;
use MailPoet\Automation\Engine\Data\StepValidationArgs;
use MailPoet\Automation\Engine\Exceptions\RuntimeException;
use MailPoet\Automation\Engine\Integration\Action;
use MailPoet\Entities\NewsletterEntity;
use MailPoet\Mailer\MailerFactory;
use MailPoet\Settings\SettingsController;
use MailPoet\Subscribers\SubscribersRepository;
use MailPoet\Validator\Builder;
use MailPoet\Validator\Schema\ObjectSchema;

/**
 * @phpstan-type NewsletterArray array{subject: string, body: array{html: string, text: string}}
 */

class NotificationEmailAction implements Action {


  const KEY = 'mailpoet:notification-email';

  /** @var MailerFactory */
  private $mailer;

  /** @var SettingsController */
  private $settings;

  /** @var SubscribersRepository */
  private $subscribersRepository;

  public function __construct(
    MailerFactory $mailer,
    SettingsController $settings,
    SubscribersRepository $subscribersRepository
  ) {
    $this->mailer = $mailer;
    $this->settings = $settings;
    $this->subscribersRepository = $subscribersRepository;
  }

  public function run(StepRunArgs $args): void {
    $emails = $args->getStep()->getArgs()['emails'] ?? [];


    $mailer = $this->mailer->buildMailer(null,
      $args->getStep()->getArgs()['sender_address'] ? [
        'name' => $args->getStep()->getArgs()['sender_name'] ?? '',
        'address' => $args->getStep()->getArgs()['sender_address'] ?? '',
      ] : null);
    foreach ($emails as $email) {
      $response = $mailer->send(
        $this->constructNewsletter($args),
        ['email' => $email],
        $this->constructParams($email)
      );

      if ($response['response']) {
        continue;
      }

      throw new RuntimeException(
        sprintf(
          // translators: %s is the actual error message
          __('Failed to send notification email: %s', 'mailpoet-premium'),
          $response['error']
        )
      );
    }
  }

  public function getKey(): string {
    return self::KEY;
  }

  public function getName(): string {
    return __('Send notification email', 'mailpoet-premium');
  }

  public function getArgsSchema(): ObjectSchema {

    $nameDefault = $this->settings->get('sender.name');
    $addressDefault = $this->settings->get('sender.address');
    $nonEmptyString = Builder::string()->required()->minLength(1);
    return Builder::object(
      [
        'emails' => Builder::array(
          Builder::string()->formatEmail()->required()
        )->maxItems(5)->required(),
        'subject' => $nonEmptyString->default(__('MailPoet Automation Notification', 'mailpoet-premium')),
        'sender_name' => $nonEmptyString->default($nameDefault),
        'sender_address' => $nonEmptyString->formatEmail()->default($addressDefault),
        'email_text' => $nonEmptyString,
      ]
    );
  }

  public function getSubjectKeys(): array {
    return [];
  }

  public function validate(StepValidationArgs $args): void {
  }

  /**
   * @param StepRunArgs $args
   * @return NewsletterArray
   */
  private function constructNewsletter(StepRunArgs $args): array {
    $args = $args->getStep()->getArgs();
    return [
      'subject' => $args['subject'],
      'body' => [
        'html' => $args['email_text'],
        'text' => $args['email_text'],
      ],
    ];
  }

  /**
   * @param string $email
   * @return array<string, array<string, string>>
   */
  private function constructParams(string $email): array {
    $subscriber = $this->subscribersRepository->findOneBy(['email' => $email]);
    return [
      'meta' => [
        'email_type' => NewsletterEntity::TYPE_AUTOMATION_NOTIFICATION,
        'subscriber_status' => $subscriber ? $subscriber->getStatus() : 'unknown',
        'subscriber_source' => $subscriber ? $subscriber->getSource() : 'unknown',
      ],
    ];
  }
}
