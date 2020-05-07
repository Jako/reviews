<?php

/**
 * Reviews
 *
 * Copyright 2020 by Oene Tjeerd de Bruin <oenetjeerd@sterc.nl>
 */

class ReviewsReviewRemoveProcessor extends modObjectRemoveProcessor
{
    /**
     * @access public.
     * @var String.
     */
    public $classKey = 'ReviewsReview';

    /**
     * @access public.
     * @var Array.
     */
    public $languageTopics = ['reviews:default'];

    /**
     * @access public.
     * @var String.
     */
    public $objectType = 'reviews.review';

    /**
     * @access public.
     * @return Mixed.
     */
    public function initialize()
    {
        $this->modx->getService('reviews', 'Reviews', $this->modx->getOption('reviews.core_path', null, $this->modx->getOption('core_path') . 'components/reviews/') . 'model/reviews/');

        return parent::initialize();
    }
}

return 'ReviewsReviewRemoveProcessor';
