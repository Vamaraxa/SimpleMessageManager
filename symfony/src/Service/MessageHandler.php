<?php

namespace App\Service;

use App\Model\Message;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class MessageHandler
{
    public function __construct(
        private $targetDirectory,
        private Filesystem $fileSystem
    ) {
    }

    public function list($sortBy = '', $orderBy = 'asc'): array
    {
        $files = new Finder();
        $files->files()->in($this->targetDirectory);

        $data = [];
        foreach ($files as $file) {
            $parts = explode('_', $file->getFilename());
            $data[] = (new Message())
                ->setUuid($parts[0])
                ->setText($file->getContents())
                ->setDateOfCreated(
                    (new DateTime())->setTimestamp($parts[1])
                )
                ->toArray();
        }

        if ($sortBy && $orderBy) {
            usort($data, (function($a, $b) use ($sortBy) {
                return $a[$sortBy] > $b[$sortBy];
            }));

            if ($orderBy == 'desc') {
                $data = array_reverse($data);
            }
        }

        return $data;
    }

    public function show(Message $message): array
    {
        $finder = new Finder();
        $finder->files()->in($this->targetDirectory)->name($message->getUuid(). "*");
        $data = [];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $parts = explode('_', $file->getFilename());
                $data = $message
                    ->setText($file->getContents())
                    ->setDateOfCreated(
                        (new DateTime())->setTimestamp($parts[1])
                    )
                    ->toArray();
                break;
            }
        }

        return $data;
    }

    public function isFile(Message $message): bool
    {
        $finder = new Finder();
        $finder->files()->in($this->targetDirectory)->name($message->getUuid() . "*");
        return $finder->hasResults();
    }

    public function create(Message $message)
    {
        $uuid = Uuid::v4();
        $message->setUuid($uuid);

        try {
            $this->fileSystem->dumpFile(
                $this->targetDirectory . '/' . $uuid . '_' . (new DateTime())->getTimestamp(),
                $message->getText()
            );
        } catch (\Throwable $th) {
            throw $th;
        }
        return true;
    }

    public function update(Message $message)
    {
        $finder = new Finder();
        $finder->files()->in($this->targetDirectory)->name($message->getUuid() . "_*");
        if ($finder->hasResults() || $finder->hasResults() == 0) {
            foreach ($finder as $file) {
                try {
                    $this->fileSystem->dumpFile(
                        $this->targetDirectory . '/' . $file->getFilename(),
                        $message->getText()
                    );
                } catch (\Throwable $th) {
                    throw $th;
                }
                break;
            }
        }
        return true;
    }

    public function delete(Message $message)
    {
        try {
            $finder = new Finder();
            $finder->files()->in($this->targetDirectory)->name($message->getUuid(). "*");
            if ($finder->hasResults()) {
                foreach ($finder as $file) {
                    $this->fileSystem->remove(
                        $this->targetDirectory . '/' . $file->getFilename(),
                    );
                    break;
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
