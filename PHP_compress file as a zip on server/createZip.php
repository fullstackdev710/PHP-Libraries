<?php

// Path of the directory to be zipped
// $dirPath = '/home/projects/domains/golive-portal.projects.ceninf.com/';
$dirPath = getcwd();
// Path of output zip file
// $zipPath = '/home/projects/domains/golive-portal.projects.ceninf.com/public_html/archive-' . time() . '.zip';
$zipPath = getcwd() . '/archive-' . time() . '.zip';

// echo (getcwd());
class ZipArchiver
{

   /**
    * Zip a folder (including itself).
    * 
    * Usage:
    * Folder path that should be zipped.
    * 
    * @param $sourcePath string 
    * Relative path of directory to be zipped.
    * 
    * @param $outZipPath string 
    * Path of output zip file. 
    *
    */
   public static function zipDir($sourcePath, $outZipPath)
   {
      $pathInfo = pathinfo($sourcePath);
      $parentPath = $pathInfo['dirname'];
      $dirName = $pathInfo['basename'];

      $z = new ZipArchive();
      $z->open($outZipPath, ZipArchive::CREATE);
      $z->addEmptyDir($dirName);
      if ($sourcePath == $dirName) {
         self::dirToZip($sourcePath, $z, 0);
      } else {
         self::dirToZip($sourcePath, $z, strlen("$parentPath/"));
      }
      $z->close();

      return true;
   }

   /**
    * Add files and sub-directories in a folder to zip file.
    * 
    * @param $folder string
    * Folder path that should be zipped.
    * 
    * @param $zipFile ZipArchive
    * Zip file where files end up.
    * 
    * @param $exclusiveLength int 
    * Number of text to be excluded from the file path. 
    *
    */
   private static function dirToZip($folder, &$zipFile, $exclusiveLength)
   {
      $handle = opendir($folder);
      while (FALSE !== $f = readdir($handle)) {
         // Check for local/parent path or zipping file itself and skip
         if ($f != '.' && $f != '..' && $f != basename(__FILE__)) {
            $filePath = "$folder/$f";
            // Remove prefix from file path before add to zip
            $localPath = substr($filePath, $exclusiveLength);
            if (is_file($filePath)) {
               $zipFile->addFile($filePath, $localPath);
            } elseif (is_dir($filePath)) {
               // Add sub-directory
               $zipFile->addEmptyDir($localPath);
               self::dirToZip($filePath, $zipFile, $exclusiveLength);
            }
         }
      }
      closedir($handle);
   }
}
// Include and initialize ZipArchive class
// require_once 'ZipArchiver.class.php';
$zipper = new ZipArchiver;
// Create zip archive
$zip = $zipper->zipDir($dirPath, $zipPath);

if ($zip) {
   echo 'ZIP archive created successfully.';
} else {
   echo 'Failed to create ZIP.';
}
