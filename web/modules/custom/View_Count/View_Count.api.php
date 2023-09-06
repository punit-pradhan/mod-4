<?php

/**
 * @file
 * Hooks specific to the Hooks module.
 */

/**
 * Respond to node view count being incremented.
 *
 * This hooks allows modules to respond whenever the total number of times the
 * current user has viewed a specific node during their current session is
 * increased.
 *
 * @param int $current_count
 *   The number of times that the current user has viewed the node during this
 *   session.
 * @param \Drupal\node\NodeInterface $node
 *   The node being viewed.
 */

function hook_hooks_count_incremented($current_count, \Drupal\node\NodeInterface $node) {

  // If this is the first time the user has viewed this node we display a
  // message letting them know.
  if ($current_count === 1) {
    \Drupal::messenger()->addMessage(\Drupal::translation()->translate('This is the first time you have viewed the node %title.', ['%title' => $node->label()]));
  }
}
