<?php

namespace Tools;

use Cake\Error\FatalErrorException;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder as CakeFolder;

/**
 * Description of Folder
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class Folder
{

    /**
     * the Folder
     *
     * @var Cake\Filesystem\Folder
     */
    protected $_Folder;

    /**
     *
     * @var string
     */
    protected $_path;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->_path = $path;
        $this->_Folder = new CakeFolder($path);
    }

    /**
     * delete all Files in Folder
     *
     * @return int
     * @throws FatalErrorException
     */
    public function deleteAllFiles()
    {
        $files = $this->_Folder->find();
        $deleted = 0;
        foreach ($files as $filename) {
            $file = new File($this->_path . DS . $filename);
            if ($file->delete()) {
                $deleted++;
            } else {
                throw new FatalErrorException(__('could not delete file {0}', [$file->name]));
            }
        }
        return $deleted;
    }

}
