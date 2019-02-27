<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/31/2017
 * Time: 11:26 AM
 */

namespace Drupal\revslider\Controller\RawTemplates;


class EditSlider
{
    public function getTemplate(array $args)
    {
        extract($args);

        ob_start();
        $is_edit = true;

        ?>
        <input type="hidden" id="sliderid" value="<?php echo $sliderID; ?>">
        <?php
        $template_seg = new SliderMainOptions();
        echo $template_seg->getTemplate(array(
            "arrFieldsParams" => $arrFieldsParams ,
            "uslider" => $uslider ,
            "is_edit" => $is_edit ,
            "sliderID" => $sliderID ,
            "slider" => $slider ,
            'linksEditSlides'=>$linksEditSlides,
        ));
        ?>

        <script type="text/javascript">
            var g_jsonTaxWithCats = <?php echo $jsonTaxWithCats?>;

            document.addEventListener("DOMContentLoaded", function() {
                RevSliderAdmin.initEditSliderView();
            });
        </script>
        <?php
        return ob_get_clean();
    }
}