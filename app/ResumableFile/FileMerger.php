<?php

namespace App\ResumableFile;

use App\ResumableFile\Entity\ChunkFileParameters;


/**
 * @author Matej Ondrus
 */
class FileMerger
{
    /** @var FileLocator */
    private $fileLocator;


    public function __construct(FileLocator $fileLocator)
    {
        $this->fileLocator = $fileLocator;
    }


    /**
     * @param string $destinationFilePath
     * @param ChunkFileParameters $parameters
     * @throws \Exception
     */
    public function merge($destinationFilePath, ChunkFileParameters $parameters)
    {
        if (!$destinationFile = fopen($destinationFilePath, 'w')) {
            throw new \Exception("Opening file '$destinationFilePath' for writing failed.");
        }

        for ($i = 1; $i <= $parameters->totalCount; $i++) {
            $chunkFilePath = $this->fileLocator->getChunkFilePath($parameters->identifier, $i);

            if (!file_exists($chunkFilePath)) {
                throw new \Exception("File '$chunkFilePath' not found.");
            }

            fwrite($destinationFile, file_get_contents($chunkFilePath));
            unlink($chunkFilePath);
        }

        fclose($destinationFile);

        if (filesize($destinationFilePath) != $parameters->totalSize) {
            throw new \Exception("Size of '$destinationFile' differs from '$parameters->totalSize'.");
        }
    }
}
