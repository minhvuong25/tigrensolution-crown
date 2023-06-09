<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Filesystem\Driver\File as FileReader;

class CssFileContentProvider
{
    const MODULE_NAME = 'Aitoc_FollowUpEmails';
    const CSS_FILE_NAME = 'style.css';

    /**
     * @var ModuleDirReader
     */
    private $moduleDirReader;

    /**
     * @var FileReader
     */
    private $fileReader;

    /**
     * CssFileContent constructor.
     *
     * @param ModuleDirReader $moduleDirReader
     * @param FileReader $fileReader
     */
    public function __construct(
        ModuleDirReader $moduleDirReader,
        FileReader $fileReader
    ) {
        $this->moduleDirReader = $moduleDirReader;
        $this->fileReader = $fileReader;
    }

    /**
     * Get css content
     *
     * @return string
     * @throws FileSystemException
     */
    public function getCssContent()
    {
        $cssFilename = $this->getCssFilename();

        return $this->getFileConetent($cssFilename);
    }

    /**
     * Get file content
     *
     * @param string $filename
     * @return string
     * @throws FileSystemException
     */
    private function getFileConetent($filename)
    {
        return $this->fileReader->fileGetContents($filename);
    }

    /**
     * Get css filename
     *
     * @return string
     */
    private function getCssFilename()
    {
        $viewFolderPath = $this->getViewModuleDir();

        return $viewFolderPath . self::CSS_FILE_NAME;
    }

    /**
     * Get view module dir
     *
     * @return string
     */
    private function getViewModuleDir()
    {
        $moduleDir = $this->moduleDirReader->getModuleDir('view', self::MODULE_NAME);
        $viewFolderPath = $moduleDir . '/frontend/email/';
        return $viewFolderPath;
    }
}
