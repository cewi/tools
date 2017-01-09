<?php

namespace Tools;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Cake\Filesystem\File as CakeFile;

/**
 * File related commands.
 *
 * @author cewi <c.wichmann@gmx.de>
 */
class File
{
    protected $_File;

    public function __construct($filename)
    {
        $this->_File = new CakeFile($filename);
    }

    /**
     * extract Text from a file.
     *
     * @param string $file
     *
     * @return array
     */
    public function extractText()
    {
        copy($this->_File->path, TMP.'tmp.pdf');

        // convert to txt - file
        $command = $this->_pdfToText();
        exec($command.TMP.'tmp.pdf');

        // extract text from file, split into pages
        $text = explode(chr(12), file_get_contents(TMP.'tmp.txt'));

        // there is always garbage after last page, don't know why..
        array_pop($text);

        // clean up
        unlink(TMP.'tmp.txt');
        unlink(TMP.'tmp.pdf');

        return $text;
    }

    /**
     * locate pdfToText binary and compose the command to convert pdfs to txt files.
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    protected function _pdfToText()
    {
        $pdfToText = '';
        if (stristr(PHP_OS, 'darwin')) {
            // Mac
            if (file_exists('/usr/local/bin/pdftotext')) {
                $pdfToText = '/usr/local/bin/pdftotext -enc UTF-8 -eol dos ';
            }
        } elseif (stristr(PHP_OS, 'WIN')) {
            // Windows
            if (file_exists(ROOT.DS.'bin'.DS.'pdftotext.exe')) {
                $pdfToText = ROOT.DS.'bin'.DS.'pdftotext.exe -enc UTF-8 -eol dos ';
            }
        } else {
            // Linux
            if (file_exists('/usr/bin/pdftotext')) {
                $pdfToText = '/usr/bin/pdftotext -enc UTF-8 -eol dos ';
            } elseif (file_exists('/usr/local/bin/pdftotext')) {
                $pdfToText = '/usr/local/bin/pdftotext -enc UTF-8 -eol dos ';
            }
        }
        if (empty($pdfToText)) {
            throw new FileNotFoundException(__('binary {0} not found', ['pdftotext']));
        }

        return $pdfToText;
    }
}
