<?php

namespace Drupal\my_theme\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 * Provides a 'Hero Block' with an image and text.
 *
 * @Block(
 *   id = "hero_block",
 *   admin_label = @Translation("Hero Full Block"),
 *   category = @Translation("Custom")
 * )
 */
class HeroBlock extends BlockBase {

  public function build() {
    $config = $this->getConfiguration();
    $hero_text = $config['hero_text'] ?? 'Welcome to My Website';
    $hero_media_id = $config['hero_media'] ?? NULL;
    $hero_image_url = '';

    if ($hero_media_id) {
      $media = Media::load($hero_media_id);
      if ($media && $media->hasField('field_media_image')) {
        $image_field = $media->get('field_media_image')->entity;
        if ($image_field instanceof File) {
          $hero_image_url = file_create_url($image_field->getFileUri());
        }
      }
    }

    return [
      '#theme' => 'hero_block',
      '#hero_text' => $hero_text,
      '#hero_image' => $hero_image_url,
      '#attached' => [
        'library' => [
          'my_theme/hero-styling',
        ],
      ],
    ];
  }

  public function blockForm($form, FormStateInterface $form_state) {
    $form['hero_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Hero Text'),
      '#default_value' => $this->getConfiguration()['hero_text'] ?? '',
    ];

    $form['hero_media'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Hero Image'),
      '#target_type' => 'media',
      '#default_value' => isset($this->getConfiguration()['hero_media']) ? Media::load($this->getConfiguration()['hero_media']) : NULL,
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
    ];

    return $form;
  }

  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('hero_text', $form_state->getValue('hero_text'));
    $this->setConfigurationValue('hero_media', $form_state->getValue('hero_media'));
  }
}
