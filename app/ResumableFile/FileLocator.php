<?php

namespace App\ResumableFile;


/**
 * @author Matej Ondrus
 */
class FileLocator
{
    /** @var string */
    private $tempDir;


    /**
     * @param string $tempDir
     */
    public function __construct($tempDir)
    {
        $this->tempDir = $tempDir;
    }


    /**
     * @param string $identifier
     * @param int $chunkNumber
     * @return string
     */
    public function getChunkFilePath($identifier, $chunkNumber)
    {
        return "$this->tempDir/resumable/$identifier.part$chunkNumber";
    }


    /**
     * @param string $filename
     * @return string
     */
    public function getMergedFilePath($filename)
    {
        return "$this->tempDir/resumable/$filename";
    }
}
