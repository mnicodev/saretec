<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/28/2017
 * Time: 3:08 PM
 */

namespace Drupal\revslider\Helper;


class RevSliderAqResize
{
    /**
     * The singleton instance
     */
    static private $instance = null;

    /**
     * No initialization allowed
     */
    private function __construct() {}

    /**
     * No cloning allowed
     */
    private function __clone() {}

    /**
     * For your custom default usage you may want to initialize an Aq_Resize object by yourself and then have own defaults
     */
    static public function getInstance() {
        if(self::$instance == null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Run, forest.
     */
    public function process( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
        // Validate inputs.
        if ( ! $url || ( ! $width && ! $height ) ) return false;

        // Caipt'n, ready to hook.
//        if ( true === $upscale ) add_filter( 'image_resize_dimensions', array($this, 'aq_upscale'), 10, 6 );

        // Define upload path & dir.
        $upload_info = RevSliderFile::uploadDirInfo();
        $upload_dir = $upload_info['basedir'];
        $upload_url = $upload_info['baseurl'];

        $http_prefix = "http://";
        $https_prefix = "https://";

        /* if the $url scheme differs from $upload_url scheme, make them match
           if the schemes differe, images don't show up. */
        if(!strncmp($url,$https_prefix,strlen($https_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
            $upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
        }
        elseif(!strncmp($url,$http_prefix,strlen($http_prefix))){ //if url begins with http:// make $upload_url begin with http:// as well
            $upload_url = str_replace($https_prefix,$http_prefix,$upload_url);
        }


        // Check if $img_url is local.
        if ( false === strpos( $url, $upload_url ) ) return false;

        // Define path of image.
        $rel_path = str_replace( $upload_url, '', $url );
        $img_path = $upload_dir . $rel_path;

        // Check if img path exists, and is an image indeed.
        if ( ! file_exists( $img_path ) or ! @getimagesize( $img_path ) ) return false;

        // Get image info.
        $info = pathinfo( $img_path );
        $ext = $info['extension'];
        list( $orig_w, $orig_h ) = getimagesize( $img_path );

        // Get image size after cropping.
//        $dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
        $dims = $this->image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
        $dst_w = $dims[4];
        $dst_h = $dims[5];

        // Return the original image only if it exactly fits the needed measures.
        if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
            $img_url = $url;
            $dst_w = $orig_w;
            $dst_h = $orig_h;
        } else {
            // Use this to check if cropped image already exists, so we can return that instead.
            $suffix = "{$dst_w}x{$dst_h}";
            $dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
            $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

            if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
                // Can't resize, so return false saying that the action to do could not be processed as planned.
                return false;
            }
            // Else check if cache exists.
            elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
                $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
            }
            // Else, we resize the image and return the new resized image url.
            else {

//                $editor = wp_get_image_editor( $img_path );
//
//                if ( is_wp_error( $editor ) || is_wp_error( $editor->resize( $width, $height, $crop ) ) )
//                    return false;
//
//                $resized_file = $editor->save();

//                if ( ! is_wp_error( $resized_file ) ) {
//                    $resized_rel_path = str_replace( $upload_dir, '', $resized_file['path'] );
                    $img_url = $url;//$upload_url . $resized_rel_path;
//                } else {
//                    return false;
//                }

            }
        }

        // Okay, leave the ship.
//        if ( true === $upscale ) remove_filter( 'image_resize_dimensions', array( $this, 'aq_upscale' ) );

        // Return the output.
        if ( $single ) {
            // str return.
            $image = $img_url;
        } else {
            // array return.
            $image = array (
                0 => $img_url,
                1 => $dst_w,
                2 => $dst_h
            );
        }

        return $image;
    }
    function image_resize_dimensions($orig_w, $orig_h, $dest_w, $dest_h, $crop = false)
    {

        if ($orig_w <= 0 || $orig_h <= 0)
            return false;
        // at least one of dest_w or dest_h must be specific
        if ($dest_w <= 0 && $dest_h <= 0)
            return false;

        /**
         * @param null|mixed $null Whether to preempt output of the resize dimensions.
         * @param int $orig_w Original width in pixels.
         * @param int $orig_h Original height in pixels.
         * @param int $dest_w New width in pixels.
         * @param int $dest_h New height in pixels.
         * @param bool|array $crop Whether to crop image to specified width and height or resize.
         *                           An array can specify positioning of the crop area. Default false.
         */
        $output = $this->aq_upscale(null, $orig_w, $orig_h, $dest_w, $dest_h, $crop);
        if (null !== $output)
            return $output;

        if ($crop) {
            // crop the largest possible portion of the original image that we can size to $dest_w x $dest_h
            $aspect_ratio = $orig_w / $orig_h;
            $new_w = min($dest_w, $orig_w);
            $new_h = min($dest_h, $orig_h);

            if (!$new_w) {
                $new_w = (int)round($new_h * $aspect_ratio);
            }

            if (!$new_h) {
                $new_h = (int)round($new_w / $aspect_ratio);
            }

            $size_ratio = max($new_w / $orig_w, $new_h / $orig_h);

            $crop_w = round($new_w / $size_ratio);
            $crop_h = round($new_h / $size_ratio);

            if (!is_array($crop) || count($crop) !== 2) {
                $crop = array('center', 'center');
            }

            list($x, $y) = $crop;

            if ('left' === $x) {
                $s_x = 0;
            } elseif ('right' === $x) {
                $s_x = $orig_w - $crop_w;
            } else {
                $s_x = floor(($orig_w - $crop_w) / 2);
            }

            if ('top' === $y) {
                $s_y = 0;
            } elseif ('bottom' === $y) {
                $s_y = $orig_h - $crop_h;
            } else {
                $s_y = floor(($orig_h - $crop_h) / 2);
            }
        } else {
            // don't crop, just resize using $dest_w x $dest_h as a maximum bounding box
            $crop_w = $orig_w;
            $crop_h = $orig_h;

            $s_x = 0;
            $s_y = 0;

            list($new_w, $new_h) = $this->constrain_dimensions($orig_w, $orig_h, $dest_w, $dest_h);
        }

        // if the resulting image would be the same size or larger we don't want to resize it
        if ($new_w >= $orig_w && $new_h >= $orig_h && $dest_w != $orig_w && $dest_h != $orig_h) {
            return false;
        }

        // the return array matches the parameters to imagecopyresampled()
        // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
        return array(0, 0, (int)$s_x, (int)$s_y, (int)$new_w, (int)$new_h, (int)$crop_w, (int)$crop_h);

    }
    function constrain_dimensions($current_width, $current_height, $max_width = 0, $max_height = 0)
    {
        if (!$max_width && !$max_height)
            return array($current_width, $current_height);

        $width_ratio = $height_ratio = 1.0;
        $did_width = $did_height = false;

        if ($max_width > 0 && $current_width > 0 && $current_width > $max_width) {
            $width_ratio = $max_width / $current_width;
            $did_width = true;
        }

        if ($max_height > 0 && $current_height > 0 && $current_height > $max_height) {
            $height_ratio = $max_height / $current_height;
            $did_height = true;
        }

        // Calculate the larger/smaller ratios
        $smaller_ratio = min($width_ratio, $height_ratio);
        $larger_ratio = max($width_ratio, $height_ratio);

        if ((int)round($current_width * $larger_ratio) > $max_width || (int)round($current_height * $larger_ratio) > $max_height) {
            // The larger ratio is too big. It would result in an overflow.
            $ratio = $smaller_ratio;
        } else {
            // The larger ratio fits, and is likely to be a more "snug" fit.
            $ratio = $larger_ratio;
        }

        // Very small dimensions may result in 0, 1 should be the minimum.
        $w = max(1, (int)round($current_width * $ratio));
        $h = max(1, (int)round($current_height * $ratio));

        // Sometimes, due to rounding, we'll end up with a result like this: 465x700 in a 177x177 box is 117x176... a pixel short
        // We also have issues with recursive calls resulting in an ever-changing result. Constraining to the result of a constraint should yield the original result.
        // Thus we look for dimensions that are one pixel shy of the max value and bump them up

        // Note: $did_width means it is possible $smaller_ratio == $width_ratio.
        if ($did_width && $w == $max_width - 1) {
            $w = $max_width; // Round it up
        }

        // Note: $did_height means it is possible $smaller_ratio == $height_ratio.
        if ($did_height && $h == $max_height - 1) {
            $h = $max_height; // Round it up
        }

        /**
         * @param array $dimensions The image width and height.
         * @param int $current_width The current width of the image.
         * @param int $current_height The current height of the image.
         * @param int $max_width The maximum width permitted.
         * @param int $max_height The maximum height permitted.
         */
        return array(array($w, $h), $current_width, $current_height, $max_width, $max_height);
    }

    function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
        if ( ! $crop ) return null; // Let the wordpress default function handle this.

        // Here is the point we allow to use larger image size than the original one.
        $aspect_ratio = $orig_w / $orig_h;
        $new_w = $dest_w;
        $new_h = $dest_h;

        if ( ! $new_w ) {
            $new_w = intval( $new_h * $aspect_ratio );
        }

        if ( ! $new_h ) {
            $new_h = intval( $new_w / $aspect_ratio );
        }

        $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

        $crop_w = round( $new_w / $size_ratio );
        $crop_h = round( $new_h / $size_ratio );

        $s_x = floor( ( $orig_w - $crop_w ) / 2 );
        $s_y = floor( ( $orig_h - $crop_h ) / 2 );

        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }
}