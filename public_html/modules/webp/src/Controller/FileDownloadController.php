<?php

namespace Drupal\webp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Image\ImageFactory;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\webp\Webp;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class FileDownloadController extends ControllerBase {

  /**
   * The stream wrapper manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * WebP driver.
   *
   * @var \Drupal\webp\Webp
   */
  protected $webp;

  /**
   * The image factory.
   *
   * @var \Drupal\Core\Image\ImageFactory
   */
  protected $imageFactory;

  /**
   * Constructs a FileDownloadController object.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface|null $streamWrapperManager
   *   The stream wrapper manager.
   * @param \Drupal\webp\Webp $webp
   *   WebP driver.
   * @param \Drupal\Core\Image\ImageFactory $image_factory
   *   The image factory.
   */
  public function __construct(StreamWrapperManagerInterface $streamWrapperManager = NULL, Webp $webp, ImageFactory $image_factory) {
    if (!$streamWrapperManager) {
      @trigger_error('Calling FileDownloadController::__construct() without the $streamWrapperManager argument is deprecated in drupal:8.8.0. The $streamWrapperManager argument will be required in drupal:9.0.0. See https://www.drupal.org/node/3035273', E_USER_DEPRECATED);
      $streamWrapperManager = \Drupal::service('stream_wrapper_manager');
    }
    $this->streamWrapperManager = $streamWrapperManager;
    $this->webp = $webp;
    $this->imageFactory = $image_factory;
    $this->logger = $this->getLogger('image');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('stream_wrapper_manager'),
      $container->get('webp.webp'),
      $container->get('image.factory')
    );
  }

  /**
   * Handles private file transfers.
   *
   * Call modules that implement hook_file_download() to find out if a file is
   * accessible and what headers it should be transferred with. If one or more
   * modules returned headers the download will start with the returned headers.
   * If a module returns -1 an AccessDeniedHttpException will be thrown. If the
   * file exists but no modules responded an AccessDeniedHttpException will be
   * thrown. If the file does not exist a NotFoundHttpException will be thrown.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   * @param string $scheme
   *   The file scheme, defaults to 'private'.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The transferred file as response.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   *   Thrown when the requested file does not exist.
   * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
   *   Thrown when the user does not have access to the file.
   * @see hook_file_download()
   *
   */
  public function download(Request $request, $scheme = 'private') {
    $target = $request->query->get('file');
    // Merge remaining path arguments into relative file path.
    $uri = $scheme . '://' . $target;

    // Don't try to generate file if source is missing.
    if (preg_match('/\.webp$/', $uri)) {
      $path_info = pathinfo($uri);
      $possible_image_uris = [
        // If the image style converted the extension, it has been added to the
        // original file, resulting in filenames like image.png.jpeg. So to find
        // the actual source image, we remove the extension and check if that
        // image exists.
        $path_info['dirname'] . DIRECTORY_SEPARATOR . $path_info['filename'],
      ];
      // Try out the different possible sources for a webp image.
      $extensions = [
        '.jpg',
        '.jpeg',
        '.png',
      ];
      foreach ($extensions as $extension) {
        $possible_image_uris[] = str_replace('.webp', mb_strtoupper($extension), $uri);
        $possible_image_uris[] = str_replace('.webp', $extension, $uri);
      }
      $source_image_found = FALSE;
      foreach ($possible_image_uris as $possible_image_uri) {
        if (file_exists($possible_image_uri)) {
          $uri = $possible_image_uri;
          $source_image_found = TRUE;
          break;
        }
      }
      if (!$source_image_found) {
        $this->logger->notice('Source image at %source_image_path not found while trying to generate derivative image.', ['%source_image_path' => $uri]);
        return new Response($this->t('Error generating image, missing source file.'), 404);
      }
    }

    if ($this->streamWrapperManager->isValidScheme($scheme) && is_file($uri)) {
      // Let other modules provide headers and controls access to the file.
      $headers = $this->moduleHandler()->invokeAll('file_download', [$uri]);

      foreach ($headers as $result) {
        if ($result == -1) {
          throw new AccessDeniedHttpException();
        }
      }

      if (count($headers)) {
        $image = $this->imageFactory->get($uri);
        if (($webp = $this->webp->createWebpCopy($image->getSource())) && in_array('image/webp', $request->getAcceptableContentTypes())) {
          return $this->webpResponse($webp, $headers, $scheme);
        }
        else {
          return $this->response($uri, $headers, $scheme);
        }
      }

      throw new AccessDeniedHttpException();
    }

    throw new NotFoundHttpException();
  }

  /**
   * Returns a WebP image as response.
   *
   * @param string $file
   *   Path to image file.
   * @param array $headers
   *   Response headers.
   * @param string $scheme
   *   The file scheme, defaults to 'private'.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The transferred file as response.
   */
  protected function webpResponse($file, array $headers, $scheme) {
    $headers = array_merge($headers, [
      'Content-Type' => 'image/webp',
      'Content-Length' => filesize($file),
    ]);
    // \Drupal\Core\EventSubscriber\FinishResponseSubscriber::onRespond()
    // sets response as not cacheable if the Cache-Control header is not
    // already modified. We pass in FALSE for non-private schemes for the
    // $public parameter to make sure we don't change the headers.
    return new BinaryFileResponse($file, 200, $headers, $scheme !== 'private');
  }

  /**
   * Returns an image as response.
   *
   * @param \Drupal\Core\Image\Image $image
   *   The image.
   * @param array $headers
   *   Response headers.
   * @param string $scheme
   *   The file scheme, defaults to 'private'.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   The transferred file as response.
   */
  protected function response($uri, array $headers, $scheme) {
    // \Drupal\Core\EventSubscriber\FinishResponseSubscriber::onRespond()
    // sets response as not cacheable if the Cache-Control header is not
    // already modified. We pass in FALSE for non-private schemes for the
    // $public parameter to make sure we don't change the headers.
    return new BinaryFileResponse($uri, 200, $headers, $scheme !== 'private');
  }


}
