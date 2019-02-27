<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/8/2017
 * Time: 10:25 AM
 */

namespace Drupal\revslider\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\revslider\Helper\RevSliderInit;

class RevSliderControllerBase extends ControllerBase
{
    public function __construct()
    {
        RevSliderInit::start();
    }

}