<?php

namespace Drupal\commerce_autosku\Plugin\Validation;

use Drupal\Core\Validation\Plugin\Validation\Constraint\NotNullConstraintValidator;
use Drupal\Core\Field\FieldItemList;
use Symfony\Component\Validator\Constraint;

/**
 * EntityLabelNotNull constraint validator.
 *
 * Custom override of NotNull constraint to allow empty entity labels to
 * validate before the automatic label is set.
 */
class CommerceSkuNotNullConstraintValidator extends NotNullConstraintValidator {
  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $typed_data = $this->getTypedData();
    if ($typed_data instanceof FieldItemList && $typed_data->isEmpty()) {
      $entity = $typed_data->getEntity();
      if (!$entity->hasField('sku')) {
        return;
      }
      $decorator = \Drupal::service('commerce_autosku.entity_decorator');
      /** @var \Drupal\commerce_autosku\CommerceAutoSkuManager $decorated_entity */
      $decorated_entity = $decorator->decorate($entity);

      if ($decorated_entity->hasSku() && $decorated_entity->autoSkuNeeded()) {
        return;
      }
    }
    parent::validate($value, $constraint);
  }
}
