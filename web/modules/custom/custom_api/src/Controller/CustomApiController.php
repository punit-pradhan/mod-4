<?php 

namespace Drupal\custom_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomApiController extends ControllerBase {

  public function displayData(Request $request) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'news')
      ->condition('status', 1) // Published nodes if ,0 then not published notes will come
      ->accessCheck(FALSE); // Disable access check so we can access it

      // Specific Tags parameter by name
    $specific_tags = $request->query->get('specific_tags');
    if ($specific_tags) {
      $tag_ids = $this->getTagIdsByTagNames($specific_tags);
      if (!empty($tag_ids)) {
        $query->condition('field_tags', $tag_ids, 'IN');
      }
    }

    $nids = $query->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $data = [];
    foreach ($nodes as $node) {
      $published_date = \Drupal::service('date.formatter')->format($node->getCreatedTime(), 'custom', 'd-m-Y');
      // $nid = $node->id();
      // $view_count = \Drupal::service('statistics.storage.node')->fetchView($nid);
     
      // Load taxonomy term tags
      $tags = [];
      $tag_entities = $node->get('field_tags')->referencedEntities();
      foreach ($tag_entities as $tag_entity) {
        $tags[] = $tag_entity->getName();
      }

      $data[] = [
        'title' => $node->getTitle(),
        'body' => $node->get('body')->value,
        'published_date' => $published_date,
        'image' => $node->get('field_images')->entity->getFileUri(),
        // 'view_count' => $view_count,
        'tags' => $tags,
      ];
    }

    $response = new JsonResponse($data);
    return $response;
  }


  /**
   * Get taxonomy term IDs by tag names.
   */
  protected function getTagIdsByTagNames($tag_names) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('name', $tag_names, 'IN')
      ->condition('vid', 'tags') 
      ->accessCheck(FALSE);
    return $query->execute();
  }
}
