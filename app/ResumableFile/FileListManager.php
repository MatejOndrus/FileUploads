<?php

namespace App\ResumableFile;

use App\ResumableFile\Entity\ChunkFileParameters;
use Nette\Http\Session;
use Nette\Http\SessionSection;


/**
 * @author Matej Ondrus
 */
class FileListManager
{
    const EXPIRE_SECONDS = 5;


    /** @var Session */
    private $session;

    /** @var SessionSection */
    private $sessionSection;


    function __construct(Session $session, $sectionName)
    {
        $this->session = $session;
        $this->sessionSection = $session->getSection($sectionName);
    }


    /**
     * @return string[]
     */
    public function getFiles()
    {
        $files = [];

        foreach ($this->sessionSection as $filename) {
            $files[] = $filename;
        }

        return $files;
    }


    public function markFileActivity(ChunkFileParameters $parameters)
    {
        $this->sessionSection->{$parameters->identifier} = $parameters->filename;
        $this->sessionSection->setExpiration(self::EXPIRE_SECONDS, $parameters->identifier);
    }


    public function markEndOfUploading(ChunkFileParameters $parameters)
    {
        unset($this->sessionSection->{$parameters->identifier});
    }
}
