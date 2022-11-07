<?php

namespace Spatie\Snapshots;

class Filesystem
{
    /** @var string */
    private $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public static function inDirectory(string $path): self
    {
        return new self($path);
    }

    public function path(string $filename): string
    {
        return $this->basePath.DIRECTORY_SEPARATOR.$filename;
    }

    public function has(string $filename): bool
    {
        return file_exists($this->path($filename));
    }

    /*
     * Get all file names in this directory that have the same name
     * as $fileName, but have a different file extension.
     */
    public function getNamesWithDifferentExtension(string $fileName): array
    {
        if (! file_exists($this->basePath)) {
            return [];
        }

        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $baseName = substr($fileName, 0, strlen($fileName) - strlen($extension) - 1);

        $allNames = scandir($this->basePath);

        $namesWithDifferentExtension = array_filter($allNames, function ($existingName) use ($baseName, $extension) {
            $existingExtension = pathinfo($existingName, PATHINFO_EXTENSION);

            $existingBaseName = substr($existingName, 0, strlen($existingName) - strlen($existingExtension) - 1);

            return $existingBaseName === $baseName && $existingExtension !== $extension;
        });

        return array_values($namesWithDifferentExtension);
    }

    public function read(string $filename): string
    {
        return file_get_contents($this->path($filename));
    }

    public function put(string $filename, string $contents): void
    {
        if (! file_exists($this->basePath)) {
            mkdir($this->basePath, 0777, true);
        }

        file_put_contents($this->path($filename), $contents);
    }

    public function delete(string $fileName): bool
    {
        return unlink($this->path($fileName));
    }

    public function copy(string $filePath, string $fileName): void
    {
        if (! file_exists($this->basePath)) {
            mkdir($this->basePath, 0777, true);
        }

        copy($filePath, $this->path($fileName));
    }

    public function fileEquals(string $filePath, string $fileName): bool
    {
		if (in_array(pathinfo($fileName, PATHINFO_EXTENSION), ['xlsx', 'zip', 'docx'])) {
			return $this->fileEqualsZipped($filePath, $fileName);
		}

        return sha1_file($filePath) === sha1_file($this->path($fileName));
    }

	public function fileEqualsZipped(string $filePath, string $fileName): bool
	{
		// open both archives
		$zipArchiveSource = new \ZipArchive();
		$zipArchiveSource->open($filePath);
		$zipArchiveTarget = new \ZipArchive();
		$zipArchiveTarget->open($this->path($fileName));

		// compare the file-number
		if ($zipArchiveSource->numFiles !== $zipArchiveTarget->numFiles) {
			return false;
		}

		// compare the content of the archived files, trie to do it fast
		for( $i = 0; $i < $zipArchiveSource->numFiles; $i++ ){
			$stat = $zipArchiveSource->statIndex( $i );
			$streamSource = $zipArchiveSource->getStream($stat['name']);
			$streamTarget = $zipArchiveTarget->getStream($stat['name']);
			while (!feof($streamSource)) {
				// if we reached feof of target before feof of source, the files are different
				if (feof($streamTarget)) {
					return false;
				}

				$contentsSourcePartial = fread($streamSource, 512);
				$contentsTargetPartial = fread($streamTarget, 512);

				// are there differences in the part, than the files are differnet
				if ($contentsSourcePartial !== $contentsTargetPartial) return false;
			}

			// if we reached feof of source before feof of target, the files are different
			if(!feof($streamTarget)) return false;
		}

		return true;
	}
}
