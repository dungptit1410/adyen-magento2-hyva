<?php

declare(strict_types=1);

namespace Adyen\Hyva\Magewire\Payment\Method;

use Adyen\Hyva\Model\Component\Payment\Context;
use Adyen\Hyva\Model\CreditCard\BrandsManager;
use Adyen\Hyva\Model\CreditCard\InstallmentsManager;
use Hyva\Checkout\Model\Magewire\Component\Evaluation\EvaluationResult;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Magento\Checkout\Model\Session as SessionCheckout;
class CreditCard extends AdyenPaymentComponent
{
    const METHOD_CC = 'adyen_cc';
    public ?string $cardBrands = null;

    public function __construct(
        private readonly Context $context,
        private readonly BrandsManager $brandsManager,
        private readonly InstallmentsManager $installmentsManager,
        private readonly SessionCheckout $sessionCheckout

    ) {
        parent::__construct($this->context);
    }

    /**
     * {@inheritDoc}
     */
    public function getMethodCode(): string
    {
        return self::METHOD_CC;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResult
    {
        $payment = $this->sessionCheckout->getQuote()->getPayment();
        return $payment->getMethod() === 'adyen_cc' && !$payment->getAdditionalData()
            ? $resultFactory->createBlocking()
            : $resultFactory->createSuccess();
    }

    public function refreshProperties(): void
    {
        $this->cardBrands = $this->brandsManager->getBrands();
        parent::refreshProperties();
    }

    /**
     * @return string
     */
    public function getFormattedInstallments(): string
    {
        return $this->installmentsManager->getFormattedInstallments();
    }
}
