<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/13/2017
 * Time: 6:59 PM
 */

namespace Drupal\revslider\Helper;


class RevSliderHtml
{
    public static function getHTMLSelect($arr,$default="",$htmlParams="",$assoc = false){

        $html = "<select $htmlParams>";
        foreach($arr as $key=>$item){
            $selected = "";

            if($assoc == false){
                if($item == $default) $selected = " selected ";
            }else{
                if(trim($key) == trim($default)) $selected = " selected ";
            }


            if($assoc == true)
                $html .= "<option $selected value='$key'>$item</option>";
            else
                $html .= "<option $selected value='$item'>$item</option>";
        }
        $html.= "</select>";
        return($html);
    }
}