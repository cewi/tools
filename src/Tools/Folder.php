<?php

namespace Tools;

use Cake\Error\FatalErrorException;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder as CakeFolder;

/**
 * Folder related commands.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class Folder
{
    /**
     * the Folder.
     *
     * @var Cake\Filesystem\Folder
     */
    protected $_Folder;

    /**
     * folder path.
     *
     * @var string
     */
    protected $_path;

    /**
     * Constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
        $this->_Folder = new CakeFolder($path);
    }

    /**
     * delete all Files in Folder.
     *
     * @return int
     *
     * @throws FatalErrorException
     */
    public function deleteAllFiles()
    {
        $files = $this->_Folder->find();
        $deleted = 0;
        foreach ($files as $filename) {
            $file = new File($this->_path.DS.$filename);
            if ($file->delete()) {
                ++$deleted;
            } else {
                throw new FatalErrorException(__('could not delete file {0}', [$file->name]));
            }
        }

        return $deleted;
    }

    /**
     * copy all files in an directory to anpther dierectroy.
     *
     * @param string $destination folder
     *
     * @return int number of copied files
     *
     * @throws \InvalidArgumentException
     * @throws FatalErrorException
     */
    public function copyAllFiles($destination)
    {
        if (!is_dir($destination)) {
            throw new \InvalidArgumentException(__('destination directory does not exist'));
        }
        $files = $this->_Folder->find();
        $copied = 0;
        foreach ($files as $filename) {
            if (copy($this->_Folder->path.DS.$filename, $destination.$filename)) {
                ++$copied;
            } else {
                throw new FatalErrorException(__('could not copy file {0}', [$file->name]));
            }
        }

        return $copied;
    }
}
