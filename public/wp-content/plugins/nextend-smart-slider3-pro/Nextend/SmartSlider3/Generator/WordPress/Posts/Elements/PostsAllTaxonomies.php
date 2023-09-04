<?php

namespace Nextend\SmartSlider3\Generator\WordPress\Posts\Elements;

use Nextend\Framework\Form\Element\Select;


class PostsAllTaxonomies extends Select {

    protected $isMultiple = true;

    protected $size = 10;

    protected $postSeparator = '_x_';

    protected $skip = false;

    public function __construct($insertAt, $name = '', $label = '', $default = '', array $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $this->options['0'] = n2_('All');

        $taxonomyNames = get_taxonomies();

        if ($this->skip) {
            $skip = array(
                'category',
                'post_tag'
            );
        } else {
            $skip = array();
        }

        foreach ($taxonomyNames as $taxonomyName) {
            if (!in_array($taxonomyName, $skip)) {
                $terms = get_terms(array(
                    'taxonomy' => $taxonomyName
                ));
                if (count($terms)) {
                    $taxonomy = get_taxonomy($taxonomyName);
                    $options  = array();
                    foreach ($terms AS $term) {
                        $options[$taxonomy->name . $this->postSeparator . $term->term_id] = '- ' . $term->name;
                    }
                    $this->optgroup[$taxonomy->label] = $options;
                }
            }
        }
    }

    public function setPostSeparator($postSeparator) {
        $this->postSeparator = $postSeparator;
    }

    public function setSkip($skip) {
        $this->skip = $skip;
    }

}

