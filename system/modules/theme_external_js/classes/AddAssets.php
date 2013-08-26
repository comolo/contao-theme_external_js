<?php 

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package   ThemeExternalJS 
 * @author    Hendrik Obermayer - Comolo 
 * @license   LGPL 
 * @copyright Hendrik Obermayer - Comolo 
 */


/**
 * Namespace
 */
namespace ThemeExternalJS;

/**
 * Class AddJavascript 
 *
 * @copyright  Hendrik Obermayer - Comolo 
 * @author     Hendrik Obermayer - Comolo 
 * @package    Devtools
 */
class AddAssets extends \Controller
{
	public function addJavascriptToPage(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular)
	{
		$arrJavascriptFiles = $this->combineArrayValues(
			(array)unserialize($layout->external_js), 
			(array)unserialize($page->external_js)
		);
		
		if(count($arrJavascriptFiles)>0){
			$this->import('FilesModel'); 
			$files = $this->FilesModel->findMultipleByIds($arrJavascriptFiles);
			
			if(is_object($files) && $files->count()){
 				$arrPaths = $files->fetchEach('path');
 				$combiner = new \Combiner();
 				foreach(array_unique($arrPaths) as $fileId => $filePath)
 				{
					if(substr($filePath, -7) == '.coffee')){
						$filePath = $this->compileCoffeescript($filePath);
					}
 					$combiner->add($filePath);
				}
 				$GLOBALS['TL_JQUERY'][] = '<script src="'.$combiner->getCombinedFile().'"></script>';
 			}
		}
	}
	
	protected function combineArrayValues($arr1, $arr2)
	{
		$array = array();
		foreach($arr1 as $key => $value) $array[] = $value;
		foreach($arr2 as $key => $value) $array[] = $value;
		
		return $array;
	}
	
	protected function compileCoffeescript($strCoffeescriptPath)
	{
		$debug = false;
		$strJSFile = 'assets/js/coffee-'.md5_file($strPathSCSS).'.js';
		
		if(!file_exists(TL_ROOT.'/'.$strJSFile) || $debug) {
			
			// require classes
			require_once __DIR__.'/../vendor/CoffeeScript/Init.php';
			CoffeeScript\Init::load();
		
			$strCoffee = file_get_contents($strCoffeescriptPath);
			$strJs = CoffeeScript\Compiler::compile($strCoffee, array('filename' => $strCoffeescriptPath));
			
			# write css file
			file_put_contents(TL_ROOT.'/'.$strJSFile, $strJs);
		}
		
		return $strJSFile;
	}
	
	
	public function addSCSSToPage(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular)
	{
		$arrSCSSFiles = (array)unserialize($layout->external_scss);
		
		if(count($arrSCSSFiles)>0){
			$this->import('FilesModel'); 
			$files = $this->FilesModel->findMultipleByIds($arrSCSSFiles);
			
			if(is_object($files) && $files->count()){
 				$arrPaths = $files->fetchEach('path');
 				$combiner = new \Combiner();
 				foreach(array_unique($arrPaths) as $fileId => $filePath)
 				{
					$combiner->add(
						$this->compileSCSS($filePath)
							)
								;
				}
 				$GLOBALS['TL_CSS'][] = $combiner->getCombinedFile();
 			}
		}
	}
	
	protected function compileSCSS($strPathSCSS)
	{
		$debug = false;
		$strCSSFile = 'assets/css/scss-'.md5_file($strPathSCSS).'.css';
		#$objCssFile = new \File($strCSSFile);
		
		#if($objCssFile->exists()){
		if(!file_exists(TL_ROOT.'/'.$strCSSFile) || $debug) {
			
			// require classes
			require_once __DIR__.'/../vendor/leafo/scssphp/scss.inc.php';
			require_once __DIR__.'/../vendor/leafo/scssphp-compass/compass.inc.php';
			
			# Add Sass
			$scss = new \scssc();
			$scss->setImportPaths(dirname($strPathSCSS).'/');
			$scss->setFormatter('scss_formatter_compressed');
			
			# Add custom function
			$scss->registerFunction("contao", function($args) use($scss) {
			  switch($args[0]){
				  case 'base': return \Environment::get('base');
				  case 'files': return '/'.$GLOBALS['TL_CONFIG']['uploadPath'].'/';
				  case 'debug': $scss->setFormatter('scss_formatter_nested'); return;
			  }
			});
			
			# Add Compass
			new \scss_compass($scss);
			
			#$objCssFile->write($strCSSFile, $scss->compile(file_get_contents(TL_ROOT.'/'.$strPathSCSS)));
			
			$strCssContent = $scss->compile(file_get_contents(TL_ROOT.'/'.$strPathSCSS));
			$strCssContent = $this->modifyCss($strCssContent);
			
			# write css file
			file_put_contents(TL_ROOT.'/'.$strCSSFile, $strCssContent);
		}
		
		return $strCSSFile;
	}
	
	protected function modifyCss($strCss)
	{
		// Remove css comments
		$strCss = preg_replace( '/\s*(?!<\")\/\*[^\*]+\*\/(?!\")\s*/' , '' , $strCss);
		
		return  $strCss;
	}
}