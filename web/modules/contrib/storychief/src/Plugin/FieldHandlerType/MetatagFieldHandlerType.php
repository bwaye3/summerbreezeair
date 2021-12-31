<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

/**
 * Base class to handle Meta Tags from the Metatag project (https://www.drupal.org/project/metatag).
 *
 * @package Drupal\storychief\Plugin\StorychiefFieldHandler
 */
class MetatagFieldHandlerType extends BaseFieldHandlerType {

    /**
     * {@inheritdoc}
     */
    public function getValue() {
        $payload = $this->getPayload();

        $keywords = [];
        if(isset($payload['seo_keywords']['data']) && is_array($payload['seo_keywords']['data'])){
            $keywords = array_column($payload['seo_keywords']['data'], 'name');
        }

        $meta_tags = [
            'title' => $payload['seo_title'],
            'description' => $payload['seo_description'],
            'keywords' => implode(',', $keywords),
            'canonical_url' => $payload['canonical'],
            'content_language' => $payload['language']
        ];


        return serialize(array_filter($meta_tags));
    }
}

