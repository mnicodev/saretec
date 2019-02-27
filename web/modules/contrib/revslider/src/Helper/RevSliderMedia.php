<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 8/8/2017
 * Time: 4:39 PM
 */

namespace Drupal\revslider\Helper;


use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RevSliderMedia
{
    public static function getMedia()
    {
        $page = intval(RevSliderFunctions::getRequestVariable('page',0));
        $search = $raw_seach = trim( RevSliderFunctions::getRequestVariable('search','*'));
        if($search == '*')
            $search = '';
        else
        {
            $search = strtolower($search);
            $search = preg_replace('/[^a-zA-Z0-9-_]+/','%',$search);
            $search = '%'.$search.'%';
            $search = preg_replace('/[%]+/','%',$search);
        }
        $type = RevSliderFunctions::getRequestVariable('type','images');
        $max_per_page = 50;
        $query = array(
            'table'=>RevSliderDB::DRUPAL_TABLE_FILE_MANAGER,
            'where'=>array(),
            'select'=>array('fid','filename','uri','filemime','filesize'),
            'order_by'=>array('fid','desc'),
            'limit'=>array($page*$max_per_page,$max_per_page+1)
        );
        switch ($type)
        {
            case 'videos':
                $file_type  = array(
                    'condition' => 'OR',
                    array('filemime','LIKE','video/%'),
                    array('filemime','LIKE','audio/%'),
                );
                break;
            case 'images':
            default:
                $file_type =  array('filemime','LIKE','image/%');
                break;
        }
        if(empty($search))
            $query['where'] = $file_type;
        else
            $query['where'] = [
                'condition' => 'AND',
                array('filename','LIKE',$search),
                $file_type
            ];
        $image_files = RevSliderDB::instance($query)->get();
        $result=array(
            'data'=>[],
            'query'=>[
                'page'=>$page.'',
                'search'=>$raw_seach,
                'type'=>$type
            ],
            'has_more'=>'no',
        );
        if(count($image_files)>$max_per_page)
            $result['has_more'] = 'yes';
        foreach ($image_files as $file)
        {
            $info = array();
            $info['id']=$file['fid'];
            $info['src']=RevSliderFile::wrapToUrl($file['uri']);
            $info['name']=$file['filename'];
            $info['size']=RevSliderFunctions::size_format($file['filesize']);
            $info['created']=date('F d,Y',$file['timestamp']);
            $result['data'][$info['id']] = $info;
        }
        return $result;
    }
    public static function uploadMedia(array $args = array())
    {
        $media = RevSliderFunctions::getFileUpload('media_upload');
        $origin_name = $media->getClientOriginalName();
        $mime = $media->getClientMimeType();
        $file_type = '';
        if(strpos($mime,'image') === 0)
            $file_type = 'image';
        if(strpos($mime,'video') === 0)
            $file_type = 'video';
        if(strpos($mime,'audio') === 0)
            $file_type = 'video';
        if(empty($file_type))
            exit('fail');
        $upload_dir = RevSliderFile::getMediaDir().DIRECTORY_SEPARATOR.$file_type;
        $temp_upload_dir = RevSliderFile::getMediaDir().DIRECTORY_SEPARATOR.'temp';
        $temp_file_upload = $media->move($temp_upload_dir,$origin_name);
        $result = RevSliderFile::moveFile($temp_file_upload->getPathName(),$upload_dir);
    }
    public static function removeFile($fid)
    {
        $file =File::load($fid);
        if($file)
            $file->delete();
        //return self::getMedia();
    }
}