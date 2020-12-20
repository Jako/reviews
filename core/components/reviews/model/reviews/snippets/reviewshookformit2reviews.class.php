<?php

/**
 * Formit2Reviews
 *
 * Copyright 2020 by Thomas Jakobi <office@treehillstudio.com>
 */

require_once dirname(__DIR__) . '/reviewssnippets.class.php';

class ReviewsHookFormit2Reviews extends ReviewsSnippets
{
    /**
     * @access public.
     * @var Array.
     */
    public $properties = [
        'reviews'               => '',

        'limit'                 => 0,
        'where'                 => '{"active": "1"}',
        'sortby'                => '{"createdon": "DESC"}',

        'tpl'                   => '@FILE elements/chunks/item.chunk.tpl',
        'tplWrapper'            => '@FILE elements/chunks/wrapper.chunk.tpl',
        'tplWrapperEmpty'       => '',

        'usePdoTools'           => false,
        'usePdoElementsPath'    => false
    ];

    /**
     * @access public.
     * @param Array $properties .
     * @param fiHooks $hook .
     * @return String.
     */
    public function run(array $properties, fiHooks $hook)
    {
        $fields = $hook->getValues();
        $this->setProperties($properties);

        $name = $this->getOption('name', $fields, '');
        $email = $this->getOption('email', $fields, '');
        if ($name && $email) {
            /** @var ReviewsReview $review */
            if (!$review = $this->modx->getObject('ReviewsReview', [
                'resource_id' => $this->modx->resource->id,
                'name' => $name,
                'email' => $email,
            ])) {
                $review = $this->modx->newObject('ReviewsReview');
                $review->fromArray([
                    'resource_id' => $this->modx->resource->id,
                    'name' => $name,
                    'email' => $email,
                    'city' => $this->getOption('city', $fields, ''),
                    'content' => $this->getOption('content', $fields, ''),
                    'active' => (bool)$this->getProperty('reviewsAutoPublish'),
                    'createdon' => time(),
                ]);
                $review->save();
            } elseif ((bool)$this->getProperty('allowOverwrite')) {
                $review->fromArray([
                    'city' => $this->getOption('city', $fields, ''),
                    'content' => $this->getOption('content', $fields, ''),
                    'active' => (bool)$this->getProperty('reviewsAutoPublish'),
                    'editedon' => time(),
                ]);
                $review->save();
            } else {
                $hook->addError('name', $this->modx->lexicon('reviews.hook_error_overwrite'));
                $hook->addError('email', $this->modx->lexicon('reviews.hook_error_overwrite'));
                return false;
            }

            /** @var ReviewsRating[] $ratings */
            $ratings = $this->modx->getCollection('ReviewsRating', [
                'active' => true
            ]);
            foreach ($ratings as $rating) {
                if (isset($fields['rating_' . $rating->get('name')])) {
                    if (!$reviewRating = $this->modx->getObject('ReviewsReviewRating', [
                        'review_id' => $review->get('id'),
                        'rating_id' => $rating->get('id')
                    ])) {
                        $reviewRating = $this->modx->newObject('ReviewsReviewRating');
                        $reviewRating->fromArray([
                            'review_id' => $review->get('id'),
                            'rating_id' => $rating->get('id'),
                        ]);
                    }
                    $reviewRating->fromArray([
                        'value' => $fields['rating_' . $rating->get('name')]
                    ]);
                    $reviewRating->save();
                }
            }
        }

        return true;
    }
}
