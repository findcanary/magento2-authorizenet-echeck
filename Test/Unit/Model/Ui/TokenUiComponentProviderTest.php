<?php
/**
 *
 * @category  AuthorizeNet
 * @package   AuthorizeNet_ECheck
 */

namespace AuthorizeNet\ECheck\Test\Unit\Model\Ui;

use AuthorizeNet\ECheck\Gateway\Config\Config;
use AuthorizeNet\ECheck\Model\Ui\TokenUiComponentProvider;
use PHPUnit\Framework\TestCase;

class TokenUiComponentProviderTest extends TestCase
{
    /**
     * @var TokenUiComponentProvider
     */
    protected $model;
    /**
     * @var \Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $componentFactoryMock;
    /**
     * @var \Magento\Vault\Model\Ui\TokenUiComponentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $componentMock;
    /**
     * @var \Magento\Vault\Api\Data\PaymentTokenInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentTokenMock;

    public function testGetComponentForToken()
    {
        $publicHash = 'publicHash';
        $tokenDetails = '{"cardNumber":"4111111111111111","cardExpMonth":"01","cardExpYear":"2020","cardType":"VI"}';
        $vaultRequireCvv = true;
        $this->componentFactoryMock = $this->createMock(\Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory::class);
        $this->componentMock = $this->createMock(\Magento\Vault\Model\Ui\TokenUiComponentInterface::class);
        $this->paymentTokenMock = $this->createMock(\Magento\Vault\Api\Data\PaymentTokenInterface::class);

        $this->paymentTokenMock->expects($this->once())
            ->method('getTokenDetails')
            ->willReturn($tokenDetails);

        $this->paymentTokenMock->expects($this->once())
            ->method('getPublicHash')
            ->willReturn($publicHash);

        $this->componentFactoryMock->expects($this->once())
            ->method('create')
            ->with([
                'config' => [
                    'code' => Config::VAULT_CODE,
                    \Magento\Vault\Model\Ui\TokenUiComponentProviderInterface::COMPONENT_DETAILS => json_decode($tokenDetails ?: '{}', true),
                    \Magento\Vault\Model\Ui\TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $publicHash,
                ],
                'name' => 'AuthorizeNet_ECheck/js/view/payment/method-renderer/vault'
            ])
            ->willReturn($this->componentMock);

        $this->model = new TokenUiComponentProvider($this->componentFactoryMock);
        $this->assertEquals($this->componentMock, $this->model->getComponentForToken($this->paymentTokenMock));
    }
}
