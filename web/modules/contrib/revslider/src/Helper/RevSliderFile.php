<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/24/2017
 * Time: 9:24 AM
 */

namespace Drupal\revslider\Helper;


use Symfony\Component\HttpFoundation\File\File;
use Drupal\file\Entity\File as FileEntity;
class RevSliderFile
{
    protected static $data;


    protected static function init()
    {
        if (!empty(self::$data))
            return true;
        self::$data = array(
            'init'          => true,
            'base_dir'      => 'revslider',
            'public_prefix' => 'public://',
            'upload_dir'    => '/upload',
        );
        return true;
    }
    public static function getMediaDir()
    {
        self::init();
        $data = self::$data;
        return $data['public_prefix'] . $data['base_dir'];
    }
    public static function uploadDirInfo()
    {
        self::init();
        $data = self::$data;
        $base_dir = $data['public_prefix'] . $data['base_dir'] . $data['upload_dir'];
        return array(
            'basedir' => $base_dir,
            'baseurl' => self::wrapToUrl($base_dir),
        );
    }

    public static function downloadUrl($url,array $args = array())
    {
        self::init();
        extract(array_merge(array(
            'path'=>null,
            'name'=>null,
            'override'=>false,
            'file_type'=>'image',
            'return_type'=>'path'
        ),$args));
        $url = self::wrapToUrl($url);
        switch ($file_type)
        {
            case 'image':{
                if (!($image_size = getimagesize($url)))
                    return false;
                break;
            }
        }
        $raw_file = system_retrieve_file($url);
        if(empty($raw_file))
            return false;
        $new_path = self::wrapToDrupalUri($path);
        $result = self::moveFile($raw_file,$new_path, $name, $override);
        switch ($return_type)
        {
            case 'path':
            {
                return $new_path . DIRECTORY_SEPARATOR . $result->getFilename();
                break;
            }
            case 'id':
            {
                return $result->file_entity_id;
            }
            default :
            {
                return true;
            }
        }

    }

    public static function moveFile($drupal_path, $new_path, $new_name = null, $override = false)
    {
        $file_obj = new File($drupal_path);
        $filename = $file_obj->getFilename();
        $new_file_path = $new_path . DIRECTORY_SEPARATOR . $filename;
        if ($override || !self::fileExists($new_file_path)) {
            $new_target = $file_obj->move($new_path, $new_name);
        }
        else
        {
            $real_path = self::wrapToPath($new_file_path);
            $file_ext = pathinfo($real_path, PATHINFO_EXTENSION);
            $max_file_same_name = 50;
            $replace_file = null;
            for ($i = 0; $i < $max_file_same_name; $i++) {
                $replace_file = preg_replace('/^(.*)\.' . $file_ext . '$/', '$1_' . $i . '.' . $file_ext, $filename);
                if (!self::fileExists($new_path . DIRECTORY_SEPARATOR . $replace_file))
                    break;
            }
            $new_target = $file_obj->move($new_path, $replace_file);
        }
        $file_id = self::getFileIdFromUrl($drupal_path);
        if(empty($file_id))
        {
            $file_entity_obj = FileEntity::create(array(
                'filename'=>$new_target->getFilename(),
                'uri'=>$new_target->getPathname()
            ));
        }
        else{
            $file_entity_obj = FileEntity::load($file_id);
            $file_entity_obj->setFileUri($new_target->getPathname());
        }
        $file_entity_obj->setPermanent();
        $file_entity_obj->save();
        //send attr to other function
        $new_target->file_entity_id = $file_entity_obj->id();
        return $new_target;
    }

    public static function basePath($use_drupal_path = true)
    {
        self::init();
        $path =  self::$data['public_prefix'] . self::$data['base_dir'];
        if($use_drupal_path)
            return $path;
        else
            return self::wrapToPath($path);
    }

    public static function wrapToUrl($drupal_path)
    {
        $wrapper = \Drupal::service('stream_wrapper_manager')->getViaUri($drupal_path);
        if(is_object($wrapper))
            return $wrapper->getExternalUrl();
        return false;
    }

    public static function wrapToDrupalUri($path)
    {
        self::init();
        $public_prefix = self::$data['public_prefix'];
        if(strpos($path,$public_prefix) === 0)
            return $path;
        $public_url = self::wrapToUrl($public_prefix);
        if (($drupal_path = str_replace($public_url, $public_prefix, $path)) === $path)
            return false;
        return $drupal_path;
    }

    public static function getFileIdFromUrl($url,$file_type = 'any')
    {
        $drupal_path = self::wrapToDrupalUri($url);
        $db = RevSliderDB::table(RevSliderDB::DRUPAL_TABLE_FILE_MANAGER);
        $where =array('uri',$drupal_path);
//        $mime_col = 'filemime';
//        switch ($file_type)
//        {
//            case 'image':{
//                $where[] = array($mime_col,'LIKE','image/%');
//            }
//        }
        $result = $db->select('fid')->where($where)->first();
        if(!empty($result))
            return $result['fid'];
        return false;
    }
    public static function getFileUrlFromId($id,$filetype = 'any')
    {
        if(empty($id))
            return '';
        $file = FileEntity::load($id);
        if($file)
            return RevSliderFile::wrapToUrl($file->getFileUri());
        return '';
    }
    public static function getUrlLocalFile($local_path ='')
    {
        $module_path = '/'.drupal_get_path('module', 'revslider');
        if(empty($local_path) || !is_string($local_path))
            return $module_path;
        $local_path = trim($local_path,'/\\');
        return $module_path.'/'.$local_path;
    }
    public static function getPathLocalFile($local_path = '')
    {
        $module_path = '/'.drupal_get_path('module', 'revslider');
        $site_dir = \Drupal::service('file_system')->realpath('');
        if(empty($site_dir))
            return '';
        $module_dir = $site_dir.$module_path;
        $local_path = trim($local_path,'/\\');
        return $module_dir.'/'.$local_path;
    }
    public static function wrapToPath($drupal_path)
    {
        $public_prefix = self::$data['public_prefix'];
        if(isset(self::$data['public_path']))
            $public_path = self::$data['public_path'];
        else
            $public_path = self::$data['public_path'] = \Drupal::service('file_system')->realpath($public_prefix).DIRECTORY_SEPARATOR;
        $real_path = (strpos($drupal_path,$public_prefix) === 0) ? str_replace($public_prefix,$public_path,$drupal_path) : $drupal_path ;
        $real_path = str_replace('//','/',$real_path);
        return $real_path;
    }

    public static function fileExists($drupal_path)
    {
        $path = self::wrapToDrupalUri($drupal_path);
        return file_exists(self::wrapToPath($path));
    }
    public static function getContents($file_path)
    {
        if(!self::fileExists($file_path))
            return '';
        $use_path = self::wrapToPath($file_path);
        ob_start();
        $content = @readfile($use_path);
        $txt = ob_get_clean();
        return $txt;
    }
    public static function delete($path,$force_folder = false)
    {
        $base_path = self::basePath(false);
        $path_remove = self::wrapToPath($path);
        //do nothing
        if(strpos($path_remove,$base_path)!== 0)
            return false;
        if(is_dir($path_remove) && $force_folder)
        {
            $paths = scandir($path_remove);
            unset($paths[0]);
            unset($paths[1]);
            $keys = array_keys($paths);

            foreach ($keys as $key)
            {
                $file = $path_remove.DIRECTORY_SEPARATOR.$paths[$key];
                $file = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$file);
                if(is_file($file))
                {
                    unlink($file);
                    unset($paths[$key]);
                }
            }
            foreach ($paths as $dir)
            {
                self::delete($path_remove.DIRECTORY_SEPARATOR.$dir,true);
            }
            rmdir($path_remove);
        }
        if(is_file($path_remove))
            unlink($path_remove);
        return true;
    }
}