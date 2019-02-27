<?php

/**
 * Created by FsFlex.
 * User: VH
 * Date: 8/16/2017
 * Time: 4:13 PM
 */
namespace Drupal\revslider\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\revslider\Helper\RevSliderDB;
use Drupal\revslider\Helper\RevSliderInit;

class RevSliderBlock extends DeriverBase {
    public function __construct()
    {
        RevSliderInit::start();
    }
    /**
     * {@inheritdoc}
     */
    public function getDerivativeDefinitions($base_plugin_definition) {
        $sliders = RevSliderDB::instance(array(
            'table'=>RevSliderDB::TABLE_SLIDERS,
            'select'=>array('alias','title')
        ))->get();
        foreach ($sliders as $index => $slider) {
            $name = $slider['alias'];
            $this->derivatives[$name] = $base_plugin_definition;
            $this->derivatives[$name]['admin_label'] = $slider['title'];
        }
        return $this->derivatives;
    }
}