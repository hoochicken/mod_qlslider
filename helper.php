<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2017 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

class modQlsliderHelper
{
    private $arrIptcDatafields=array
    (
        '2#005'=>'title',
        '2#120'=>'description',
        '2#122'=>'caption',
        '2#105'=>'headline',
        '2#040'=>'instructions',
        '2#085'=>'author',
        '2#116'=>'copyright',
    );
    private $arrIptc;
    private $params;
    private $module;
    private $document;
    private $app;
    private $canDefer;

    /**
     * method to load module params as class objects
     * @param $params
     * @param $module
     */
    function __construct($params,$module)
    {
        $this->params=$params;
        $this->module=$module;
        $this->document=JFactory::getDocument();
        $this->app=JFactory::getApplication();
        $version=new JVersion;
        $this->jquery=version_compare($version->getShortVersion(), '3.0.0', '>=');
        $this->defer=preg_match('/(?i)msie [6-9]/',$_SERVER['HTTP_USER_AGENT']) ? false : true;
    }

    /**
     * method to get images from folder
     * @param $folder path to folder
     * @return array|null array with pic info like path, title and description
     */
    public function getImagesFromFolder($folder)
    {
    	if(!is_numeric($max=$this->params->get('max_images'))) $max=20;
        if(!$dir=@opendir($folder)) return null;
        while (false!==($file=readdir($dir)))
        {
            if (preg_match('/.+\.(jpg|jpeg|gif|png)$/i', $file))
            {
            	// check with getimagesize() which attempts to return the image mime-type
            	if(false!==getimagesize(JPATH_ROOT.'/'.$folder.'/'.$file)) $files[]=$file;
			}
        }
        closedir($dir);
        if($this->params->get('sort_by')) natcasesort($files);
		else shuffle($files);

		$images=array_slice($files, 0, $max);
		
		$target=modqlsliderHelper::getSlideTarget($this->params->get('link'));
		$i=0;
        $slides=array();
        foreach($images as $image)
        {
			$title=$description='';
            if(1==$this->params->get('iptc',0))
            {
                $this->generateIptc($image,$folder);
                $title=$this->getIptc($image,'title');
                $description=$this->getIptc($image,'description');
            }
            if(is_numeric($this->params->get('limit_desc')) AND 0!=$this->params->get('limit_desc'))$description=substr($description,0,$this->params->get('limit_desc'));
            if(''==$title)$title=$this->getTitle($image);
            //$slides[]=(object) array('title'=>$title, 'description'=>$description, 'image'=>$folder.'/'.$image, 'link'=>$this->params->get('link'), 'alt'=>$image, 'target'=>$target);
            if(!isset($slides[$i]))$slides[$i]=new stdClass();
            $slides[$i]->title=$title;
            $slides[$i]->description=$description;
            $slides[$i]->image=$folder.'/'.$image;
            $slides[$i]->link=$this->params->get('link');
            $slides[$i]->alt=$image;
            $slides[$i]->target=$target;
            $slides[$i]->caption=false;
            if
            (
            (1==$this->params->get('show_title') AND !empty($title)) OR
            (1==$this->params->get('show_desc')AND !empty($description))
            ) $slides[$i]->caption=true;
            if(1!=$this->params->get('resize',0)) $slides[$i]->thumb=$slides[$i]->resized=$slides[$i]->image;
            else
            {
                $thumbFolder=$folder.'/thumb';
                $resizedFolder=$folder.'/resized';

                if(false==$this->checkFolder($thumbFolder))$this->createFolder($thumbFolder);
                if(false==$this->checkFolder($resizedFolder))$this->createFolder($resizedFolder);

                $thumb=$thumbFolder.'/'.$image;
                $resized=$resizedFolder.'/'.$image;

                if (!file_exists($thumb) OR 1==$this->params->get('override_thumb'))
                {
                    $arrRel=$this->generateImage($slides[$i]->image,JPATH_ROOT.'/'.$thumb);
                    $slides[$i]->thumb=$thumb;
                }
                else $slides[$i]->thumb=$thumb;
                if (!file_exists($resized) OR 1==$this->params->get('override_resized'))
                {
                    $arrRel=$this->generateImage($slides[$i]->image,JPATH_ROOT.'/'.$resized);
                    $slides[$i]->resized=$resized;
                }
                else $slides[$i]->resized=$resized;
                if(is_array($arrRel))array_merge($slides[$i],$arrRel);
                unset($arrRel);
            }
            $i++;
		}
        return $slides;
    }

    /**
     * method to check, if folder exists and is a directory
     * @param $path
     * @return bool
     */
    private function checkFolder($path)
    {
        if(!is_dir($path))return false;
        return true;
    }

    /**
     * method to generate folder
     * @param $path
     */
    private function createFolder($path)
    {
        mkdir($path);
    }

    /**
     * method to get title of image
     * @param $image
     * @return string title of image, i. e. alterated file name
     */
    private function getTitle($image)
    {
        $arr=explode('.',$image);
        unset($arr[count($arr)-1]);
        return ucwords(str_replace('-',' ',(implode(' ',$arr))));
    }

    /**
     * method to get iptc info from image
     * @param $image
     * @param $path
     * @return string|void
     */
    public function generateIptc($image,$path)
    {
        $pathImage=$path.'/'.$image;
        if(!file_exists($pathImage))return '';
        $size=getimagesize($pathImage,$info);
        if(!is_array($info) OR !isset($info['APP13']))
        {
            $this->arrIptc[$image]=array();
            return;
        }
        $iptc2=iptcparse($info['APP13']);
        $iptc=array();
        while(list($k,$v)=each($iptc2))if(isset($v[0]))$iptc[$k]=$v[0];
        foreach ($this->arrIptcDatafields as $k=>$v)
        {
            if(!isset($this->arrIptc[$image]))$this->arrIptc[$image]=array();
            if(isset($iptc[$k]))$this->arrIptc[$image][$v]=$iptc[$k];
        }
    }

    /**
     * @param $image
     * @param $key
     * @param string $default
     * @return string
     */
    public function getIptc($image,$key,$default='')
    {
        if(!isset($this->arrIptc[$image]) OR !isset($this->arrIptc[$image][$key])) return $default;
        return $this->arrIptc[$image][$key];
    }

    /**
     * method to get target of slide
     * @param $link
     * @return string
     */
	public function getSlideTarget($link)
    {
        $target='_self';
        if(preg_match("/^http/",$link) && !preg_match("/^".str_replace(array('/','.','-'), array('\/','\.','\-'),JURI::base())."/",$link)) $target='_blank';
        return $target;
	}

    /**
     * method to load jQuery framework
     */
    public function addJquery()
    {
        JHTML::_('jquery.framework');
        $this->document->addScript(JURI::root(true).'/modules/mod_qlslider/js/qlslider.js', 'text/javascript', $this->defer);
        return;
    }

    /**
     * method to load javascript
     */
    public function addScripts()
    {
        if($this->params->get('link_image',1) <= 1) return;
        $this->document->addScript(JURI::root(true).'/media/mod_qlslider/js/magnific.js', 'text/javascript', $this->defer);
        $this->document->addStyleSheet(JURI::root(true).'/media/mod_qlslider/css/magnific.css');
        $this->document->addScript(JURI::root(true).'/media/mod_qlslider/js/magnific-init.js', 'text/javascript', $this->defer);
        return;
    }

    /**
     * method to load css files
     */
    public function addStyles()
    {
        $this->document->addStyleSheet(JURI::root(true).'/media/mod_qlslider/css/qlslider.css');
        $styles=array();
        $styles[]='#qlslider'.$this->module->id.'.qlslider .item {width:'.$this->params->get('image_width',240).'px;margin-right:'.$this->params->get('image_margin',10).'px;}';
        $width=$this->params->get('visible_images', 3) * $this->params->get('image_width', 240) + (($this->params->get('visible_images', 3) - 1) * $this->params->get('image_margin',10));
        if (!is_numeric($width) OR 1 == $this->params->get('full_width', 0)) $width = '100%'; else $width .= 'px';
        $styles[] = '@media (max-width:'.$width.'){#qlslider'.$this->module->id.'.qlslider .qlsliderContent {width:100%!important;}}';
        $styles[] = '#qlslider'.$this->module->id.'.qlslider .qlsliderContent {max-width:' . $width . ';width:100%;height:'.$this->params->get('image_height',180).'px;}';
        $backgroundCaption=$this->hex2rgb($this->params->get('caption_bg','#000'),1);
        $backgroundCaption='rgba('.$backgroundCaption.','.$this->params->get('caption_bgopacity','0.7').')';
        $styles[] = '#qlslider'.$this->module->id.'.qlslider .caption {color:'.$this->params->get('caption_color','#fff').';background:'.$backgroundCaption.';}';
        if(2==$this->params->get('fit_to',0))$styles[]='#qlslider'.$this->module->id.'.qlslider .content img {height:'.$this->params->get('image_height',180).'px;}';
        $styles[] = '#qlslider'.$this->module->id.'.qlslider .qlsliderNavigation a span{color:'.$this->params->get('navigationColor','#000000').';background:'.$this->params->get('navigationBackground','#ffffff').';}';
        //echo implode("\n",$styles);
        $this->document->addStyleDeclaration(implode("\n",$styles));
        return;
    }

    /**
     * method to get slides according to params
     * @return array|bool|mixed|null
     */
    public function getSlides()
    {
        $slides=$this->getImagesFromFolder($this->params->get('image_folder'));
        if(null==$slides)
        {
            $this->app->enqueueMessage(JText::_('MOD_QLSLIDER_MSG_NO_CATALOG_OR_FILES'),'notice');
            return false;
        }
        return $slides;
    }

    /**
     * method to generate (resized/thumbed) image according to settings
     * @param $image
     * @param $destination
     * @return $arrRel array of image relation
     */
    function generateImage($image,$destination)
    {
        //die($image.'<br />'.$destination);
        $imageSrc=$this->imageCreateFromAny($image);
        $newWidth=$this->params->get('width',800);
        if(!is_numeric($newWidth))$newWidth=800;
        $newHeight=$this->params->get('height',600);
        if(!is_numeric($newHeight))$newHeight=600;
        list($width,$height) = getimagesize($image);
        if(1==$this->params->get('resize_force',0))$this->imagescale($imageSrc,$newWidth,$newHeight);
        //if($width>$newWidth) die('yes');else die('no');
        $relation=$width/$height;
        if($width>$newWidth) $imageSrc=$this->imagescale($imageSrc,$newWidth,$newWidth/$relation);
        if($height>$newHeight)
        {
            $imageSrc=$this->imagescale($imageSrc,$relation*$newHeight,$newHeight);
        }
        $arrRel=array();
        $arrRel['width']=$arrRel['widthOriginal']=$width;
        $arrRel['height']=$arrRel['heightOriginal']=$height;
        if($width>$newWidth)$arrRel['width']=$newWidth;
        if($height>$newHeight)$arrRel['height']=$newHeight;
        //$this->imagescale($imageSrc,$relation*$newHeight,$newHeight);
        imagejpeg($imageSrc,$destination);
        return $arrRel;
    }

    /**
     * method to create image source from any
     * @param $filepath
     * @return bool|resource
     */
    function imageCreateFromAny($filepath)
    {
        $type=$this->getFileType($filepath);
        if(false==$this->checkFiletypeAllowed($type))return false;
        $image=false;
        switch ($type)
        {
            case 'gif' :
                $image=imageCreateFromGif($filepath);
                break;
            case 'jpeg' :
                $image=imageCreateFromJpeg($filepath);
                break;
            case 'png' :
                $image=imageCreateFromPng($filepath);
                break;
            case 'bmp' :
                $image=imageCreateFromBmp($filepath);
                break;
        }
        return $image;
    }

    /**
     * method to get file type of image
     * @param $filepath
     * @return bool|mixed
     */
    function getFileType($filepath)
    {
        if(function_exists('exif_imagetype'))
        {
            $type=exif_imagetype($filepath);
            $types=array
            (
                1=>'gif',
                2=>'jpeg',
                3=>'png',
                6=>'bmp',
            );
            if (!array_key_exists($type, $types))return $types[$type];
        }
        $type=getImageSize($filepath);
        if (!isset($type['mime']))return false;
        $arr=explode('/',$type['mime']);
        return array_pop($arr);
    }

    /**
     * method to check if file type is allowed
     * @param $type
     * @return bool
     */
    function checkFiletypeAllowed($type)
    {
        $allowed=array('jpeg', 'bmp', 'png', 'gif');
        if(in_array($type,$allowed))return true;
    }

    /**
     * method to scale image according to settings;
     * method works like "imagescale()";
     * if this very function does not exists (when php version too old), a self-knitted routine takes over
     * @param $imageSrc
     * @param $width
     * @param int $height
     * @param bool $crop
     * @return resource|void
     */
    function imagescale($imageSrc,$width,$height=-1,$crop=false)
    {
        if(function_exists('imagescale'))
        {
            $imageSrc=imagescale($imageSrc,$width,$height);
            return $imageSrc;
        }
        //echo('modQlsliderHelper::imagescale()');
        //return;
        $r = $width / $height;
        if ($crop)
        {
            if ($width > $height)
            {
                $width = ceil($width-($width*abs($r-$width/$height)));
            }
            else
            {
                $height = ceil($height-($height*abs($r-$width/$height)));
            }
            $newwidth = $width;
            $newheight = $height;
        }
        else
        {
            if ($width/$height > $r)
            {
                $newwidth = $height*$r;
                $newheight = $height;
            }
            else
            {
                $newheight = $width/$r;
                $newwidth = $width;
            }
        }
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $imageSrc, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        return $dst;
    }
    function hex2rgb($hex,$implode=0,$default=array(255,255,255))
    {
        $hex = str_replace('#','',$hex);
        if(3==strlen($hex))
        {
            $r=hexdec(substr($hex,0,1).substr($hex,0,1));
            $g=hexdec(substr($hex,1,1).substr($hex,1,1));
            $b=hexdec(substr($hex,2,1).substr($hex,2,1));
        }
        elseif(6==strlen($hex))
        {
            $r=hexdec(substr($hex,0,2));
            $g=hexdec(substr($hex,2,2));
            $b=hexdec(substr($hex,4,2));
        }
        else
        {
            $r=$default[0];
            $g=$default[1];
            $b=$default[2];
        }
        $rgb=array($r,$g,$b);
        if(1==$implode) return implode(',', $rgb);
        return $rgb;
    }
}