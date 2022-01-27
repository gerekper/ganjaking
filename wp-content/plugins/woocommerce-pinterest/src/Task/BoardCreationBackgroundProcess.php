<?php namespace Premmerce\WooCommercePinterest\Task;

use Premmerce\SDK\V2\Notifications\AdminNotifier;
use Premmerce\WooCommercePinterest\Logger\Logger;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Model\PinterestModelException;
use Premmerce\WooCommercePinterest\Pinterest\Api\Api;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\Pinterest\PinDataGenerator;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WC_Background_Process;
use wpdb;

/**
 * Class BackgroundProcess
 * Responsible for background processing create, update and delete tasks
 *
 * @package Premmerce\WooCommercePinterest\Task
 *
 */
class BoardCreationBackgroundProcess extends WC_Background_Process
{
  /**
   * Action
   *
   * @var string
   */
  protected $action = 'woocommerce_pinterest_board_creation';

  /**
   * Api instance
   *
   * @var Api
   */
  private $api;

  /**
   * PinModel instance
   *
   * @var PinModel
   */
  private $model;

  /**
   * PinDataGenerator instance
   *
   * @var PinDataGenerator
   */
  private $generator;

  /**
   * Logger instance
   *
   * @var Logger
   */
  private $logger;

  /**
   * ServiceContainer instance
   *
   * @var ServiceContainer
   */
  private $container;

  /**
   * BackgroundProcess constructor.
   *
   * @param Api $api
   * @param PinModel $model
   * @param PinDataGenerator $generator
   * @param Logger $logger
   */
  public function __construct( Api $api, PinModel $model, PinDataGenerator $generator, Logger $logger )
  {
    $this->api = $api;
    $this->model = $model;
    $this->generator = $generator;
    $this->logger = $logger;

    $this->container = ServiceContainer::getInstance();

    parent::__construct();
  }

  /**
   * Run task
   *
   * @param array $board
   */
  public function task( $board )
  {
    return $this->runCreateBoard( $board );
  }

  /**
   * Create board using api
   *
   * @param array $board
   */
  public function runCreateBoard( $board )
  {
    try {
      $this->api->createBoard( $board );
    } catch ( PinterestApiException $e ) {
      $this->handleTaskException( $e );
    }
  }

  /**
   * Set failed status and error data
   *
   * @param PinterestApiException $e
   */
  protected function handleTaskException( PinterestApiException $e )
  {
    $this->logger->logPinterestException( $e );

    if ( $e->getCode() === Api::CODE_AUTH_FAILED ) {
      $this->api->getState()->setApiAuthFailed();
    }

    if ( $e->getCode() === Api::CODE_TOO_MANY_REQUESTS ) {
      //Exceeded rate limit, wait for 1 hour
      $this->api->getState()->wait();
    }

    if ( $e->getCode() >= 500 ) {
      //Api error, wait for 10 minutes
      $this->api->getState()->wait( 600 );
    }
  }

  /**
   * Complete
   */
  protected function complete()
  {
    if ( ! wp_next_scheduled( 'woocommerce_pinterest_set_timeout_to_get_boards' ) ) {
      wp_schedule_single_event( time() + 60, 'woocommerce_pinterest_set_timeout_to_get_boards' );
    }

    parent::complete();
  }

  /**
   * Filter Pinterest boards list to remove hidden board _products.
   *
   * @param array $boards
   *
   * @return array
   */
  public static function filterBoards( array $boards )
  {
    foreach ( $boards as $key => $board ) {
      $boardUrlParts = array_filter( explode( '/', $board['url'] ) );
      if ( '_products' === end( $boardUrlParts ) ) {
        unset( $boards[$key] );
        break;
      }
    }
    return array_values( $boards );
  }

  public static function updateBoardsMapping( array $boards )
  {
    global $wpdb;
    $table = $wpdb->prefix . 'woocommerce_pinterest_boards_mapping';
    $pinterestUserId = ( new ApiState() )->getUserId();
    foreach ( $boards as $board ) {
      $category = get_term_by( 'name', $board['name'], 'product_cat' );
      if( $category !== false ) {
        $fieldsString = "(`pin_user_id`, `entity_id`, `entity_type`, `board_id`)";
        $valuesString = [
          'pin_user_id' => $pinterestUserId,
          'entity_id'   => $category->term_id,
          'entity_type' => 'category',
          'board_id'    => $board['id'],
        ];
        $valuesString = "('" . implode( "', '", $valuesString ) . "')";
        $sql = "INSERT INTO {$table} {$fieldsString} VALUES {$valuesString}";
        $wpdb->query( $sql );
      }
    }
  }

  public static function getBoardsAfterTimeout()
  {
    $container = ServiceContainer::getInstance();

    $notifier = $container->getNotifier();

    try {
      $boardsRequest = $container->getApi()->getBoards();
      $boards = $boardsRequest->getData();

      while ( $boardsRequest->getBookmark() ) {
        $boardsRequest = $container->getApi()->getBoards( $boardsRequest->getBookmark() );
        $boards = array_merge( $boardsRequest->getData(), $boards );
      }

      $boards = self::filterBoards( $boards );

      $integration = $container->getPinterestIntegration();
      $integration->update_option( 'boards', $boards );
      self::updateBoardsMapping( $boards );
      $notifier->flash( __( 'Boards list updated', 'woocommerce-pinterest' ) );

    } catch ( PinterestApiException $e ) {
      $logger = $container->getLogger();
      $logger->logPinterestException( $e );
      $notifier->flash( $e->getMessage(), AdminNotifier::ERROR );
    }
  }
}