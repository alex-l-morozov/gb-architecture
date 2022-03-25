<?php

/**
 * class MyWordApp
 */
class MyWordApp
{
    /**
     * @param string $filename
     * @return File
     */
    public function openFile(string $filename): File
    {
        if (!file_exists($filename)){
            return $this->createFile($filename);
        }
        $content = file_get_contents($filename);

        if ($content === false) {
            throw new Error('Fail to read file.');
        }

        return new File($filename, $content);
    }

    /**
     * @param string $filename
     * @return File
     */
    public function createFile(string $filename): File
    {
        $file = file_put_contents($filename, '');
        if (!$file){
            throw new Error('Can`t create file : ' . $filename);
        }
        return new File( $filename, '',);
    }

    /**
     * @param File $file
     * @return void
     */
    public function saveFile(File $file)
    {
        $result = file_put_contents($file->filename, $file->content);
        if (!$result){
            throw new Error('Error');
        }
    }
}
