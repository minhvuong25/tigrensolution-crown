<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Plugin\Customer\Model;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Migration\Model\PasswordVerifier;
use Migration\Model\PasswordHashResolver;
use Magento\Customer\Model\Authentication;

/**
 * Plugin for Authentication
 */
class AuthenticationPlugin
{
    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var CustomerResourceModel
     */
    private $customerResourceModel;

    /**
     * @var PasswordVerifier
     */
    private $passwordVerifier;

    /**
     * @var PasswordHashResolver
     */
    private $passwordHashResolver;

    /**
     * @var Encryptor
     */
    private $encryptor;

    /**
     * @param CustomerRegistry $customerRegistry
     * @param CustomerResourceModel $customerResourceModel
     * @param PasswordVerifier $passwordVerifier
     * @param Encryptor $encryptor
     */
    public function __construct(
        CustomerRegistry $customerRegistry,
        CustomerResourceModel $customerResourceModel,
        PasswordVerifier $passwordVerifier,
        PasswordHashResolver $passwordHashResolver,
        Encryptor $encryptor
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->customerResourceModel = $customerResourceModel;
        $this->passwordVerifier = $passwordVerifier;
        $this->passwordHashResolver = $passwordHashResolver;
        $this->encryptor = $encryptor;
    }

    /**
     * Replace customer password hash in case it is Bcrypt or sha-512 algorithms
     *
     * @param Authentication $subject
     * @param int $customerId
     * @param string $password
     * @return void
     */
    public function beforeAuthenticate(
        Authentication $subject,
        $customerId,
        $password
    ) {
        $customer = $this->customerRegistry->retrieve($customerId);
        $hash = $this->passwordHashResolver->resolve($customer);
        if ($this->passwordVerifier->verify($password, $hash)) {
            $this->customerRegistry->remove($customerId);
            $hash = $this->encryptor->getHash($password, true);
            $this->customerResourceModel->getConnection()->update(
                $this->customerResourceModel->getTable('customer_entity'),
                [
                    'password_hash' => $hash
                ],
                $this->customerResourceModel->getConnection()->quoteInto('entity_id = ?', $customerId)
            );
        }
    }
}
