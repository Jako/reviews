<?php

/**
 * Review Group
 *
 * Copyright 2020 by Oene Tjeerd de Bruin <oenetjeerd@sterc.nl>
 */

require_once dirname(__DIR__) . '/reviewssnippets.class.php';

class ReviewsSnippetReviewGroup extends ReviewsSnippets
{
    /**
     * @access public.
     * @var Array.
     */
    public $properties = [
        'type' => 'default',
        'value' => '',
        'required' => false,
        'error' => '',
        'tpl' => '@FILE elements/chunks/formselect.chunk.tpl',
        'tplOption' => '@FILE elements/chunks/formoption.chunk.tpl',
    ];

    /**
     * @access public.
     * @param Array $properties .
     * @return String.
     */
    public function run(array $properties = [])
    {
        $this->setProperties($properties);

        $output = [];

        $type = $this->getProperty('type');
        $value = $this->getProperty('value');
        $error = $this->getProperty('error');
        $required = (bool)$this->getProperty('required');

        /** @var ReviewsRating $ratingType */
        if ($ratingType = $this->modx->getObject('ReviewsRating', [
            'name' => $type
        ])) {
            $range = explode('||', $this->getOption('ratings'));
            if (count($range) == 2 && filter_var($range[0], FILTER_VALIDATE_INT) && filter_var($range[2], FILTER_VALIDATE_INT)) {
                $range = range(intval($range[0]), intval($range[1]));
            } else {
                $range = range(1, 5);
            }
            $options = [];
            foreach ($range as $rating) {
                $options[] = $this->getChunk($this->getProperty('tplOption'), [
                    'rating' => $rating,
                    'value' => $value
                ]);
            }
            return $this->getChunk($this->getProperty('tpl'), [
                'type' => $type,
                'value' => $value,
                'error' => $error,
                'required' => ($required) ? 'required' : '',
                'options' => implode("\n", $options)
            ]);
        }

        return '';
    }
}