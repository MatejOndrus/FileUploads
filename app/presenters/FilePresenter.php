<?php

namespace App\Presenters;

use App\Forms\PlaceFormFactory;
use App\ResumableFile\FileLocator;
use App\ResumableFile\Entity\ChunkFileParameters;
use App\ResumableFile\FileListManager;
use App\ResumableFile\FileMerger;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Http\Response;


/**
 * @author Matej Ondrus
 */
class FilePresenter extends BasePresenter
{
    /** @var FileLocator @inject */
    public $fileLocator;

    /** @var FileListManager @inject */
    public $fileListManager;

    /** @var FileMerger @inject */
    public $fileMerger;

    /** @var PlaceFormFactory @inject */
    public $placeFormFactory;


    public function renderDefault()
    {
        $this->template->uploadingFiles = $this->fileListManager->getFiles();
    }


    public function actionProceedChunk()
    {
        if ($this->request->isMethod('GET')) {
            $chunkParams = new ChunkFileParameters($this->request->getParameters());
            $chunkFilePath = $this->fileLocator->getChunkFilePath($chunkParams->identifier, $chunkParams->number);

            if (file_exists($chunkFilePath)) {
                $this->getHttpResponse()->setCode(Response::S200_OK);
            } else {
                $this->getHttpResponse()->setCode(Response::S404_NOT_FOUND);
            }

            $this->fileListManager->markFileActivity($chunkParams);

            $this->terminate();
        }

        /** @var FileUpload[] $files */
        $files = $this->request->getFiles();

        if ($files) {
            $chunkFile = $files[key($files)];

            if ($chunkFile->isOk()) {
                $chunkParams = new ChunkFileParameters($this->request->getPost());
                $chunkFilePath = $this->fileLocator->getChunkFilePath($chunkParams->identifier, $chunkParams->number);

                move_uploaded_file($chunkFile->temporaryFile, $chunkFilePath);

                if ($chunkParams->number == $chunkParams->totalCount) {
                    $destinationFilePath = $this->fileLocator->getMergedFilePath($chunkParams->filename);
                    $this->fileMerger->merge($destinationFilePath, $chunkParams);
                    $this->fileListManager->markEndOfUploading($chunkParams);
                } else {
                    $this->fileListManager->markFileActivity($chunkParams);
                }
            }
        }

        $this->terminate();
    }


    public function handleListUpdate()
    {
        $this->redrawControl('uploadingFiles');
    }


	/**
	 * @return Form
	 */
	protected function createComponentPlaceForm()
	{
		$form = $this->placeFormFactory->create();
		$form->onSuccess[] = function ($form) {
            $this->flashMessage('Data was saved.');
			$form->getPresenter()->redirect('default');
		};
		return $form;
	}
}
