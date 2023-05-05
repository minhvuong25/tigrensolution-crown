<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service\Email;

use Aitoc\FollowUpEmails\Api\Service\Email\UnsubscribeCodeInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class UnsubscribeCode implements UnsubscribeCodeInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * UnsubscribeCode constructor.
     *
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * Generate unsubscribe code
     *
     * @return string
     */
    public function generateUnsubscribeCode()
    {
        $string = $this->getUniqueString();

        return $this->getHash($string);
    }

    /**
     * Get unique string
     *
     * @return string
     */
    private function getUniqueString()
    {
        return uniqid('', true);
    }

    /**
     * Get hash
     *
     * @param string $string
     * @return string
     */
    private function getHash($string)
    {
        return $this->encryptor->getHash($string);
    }
}
