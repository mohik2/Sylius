<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CheckoutCompleteContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CompletePageInterface
     */
    private $completePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CompletePageInterface $completePage
     */
    public function __construct(SharedStorageInterface $sharedStorage, CompletePageInterface $completePage)
    {
        $this->sharedStorage = $sharedStorage;
        $this->completePage = $completePage;
    }

    /**
     * @When I try to open checkout complete page
     * @When I want to complete checkout
     */
    public function iTryToOpenCheckoutCompletePage()
    {
        $this->completePage->tryToOpen();
    }

    /**
     * @When I decide to change the payment method
     */
    public function iGoToThePaymentStep()
    {
        $this->completePage->changePaymentMethod();
    }

    /**
     * @When /^I provide additional note like "([^"]+)"$/
     */
    public function iProvideAdditionalNotesLike($notes)
    {
        $this->sharedStorage->set('additional_note', $notes);
        $this->completePage->addNotes($notes);
    }

    /**
     * @When I return to the checkout summary step
     */
    public function iReturnToTheCheckoutSummaryStep()
    {
        $this->completePage->open();
    }

    /**
     * @When I confirm my order
     */
    public function iConfirmMyOrder()
    {
        $this->completePage->confirmOrder();
    }

    /**
     * @Then I should be on the checkout complete step
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutCompleteStep()
    {
        Assert::true($this->completePage->isOpen());
    }

    /**
     * @Then my order's shipping address should be to :fullName
     */
    public function iShouldSeeThisShippingAddressAsShippingAddress($fullName)
    {
        $address = $this->sharedStorage->get('shipping_address_'.StringInflector::nameToLowercaseCode($fullName));

        Assert::true($this->completePage->hasShippingAddress($address));
    }

    /**
     * @Then my order's billing address should be to :fullName
     */
    public function iShouldSeeThisBillingAddressAsBillingAddress($fullName)
    {
        $address = $this->sharedStorage->get('billing_address_'.StringInflector::nameToLowercaseCode($fullName));

        Assert::true($this->completePage->hasBillingAddress($address));
    }

    /**
     * @Then address to :fullName should be used for both shipping and billing of my order
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress($fullName)
    {
        $this->iShouldSeeThisShippingAddressAsShippingAddress($fullName);
        $this->iShouldSeeThisBillingAddressAsBillingAddress($fullName);
    }

    /**
     * @Then I should have :quantity :productName products in the cart
     */
    public function iShouldHaveProductsInTheCart($quantity, $productName)
    {
        Assert::true($this->completePage->hasItemWithProductAndQuantity($productName, $quantity));
    }

    /**
     * @Then my order shipping should be :price
     */
    public function myOrderShippingShouldBe($price)
    {
        Assert::true($this->completePage->hasShippingTotal($price));
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit price discounted by ("\$\d+")$/
     */
    public function theShouldHaveUnitPriceDiscountedFor(ProductInterface $product, $amount)
    {
        Assert::true($this->completePage->hasProductDiscountedUnitPriceBy($product, $amount));
    }

    /**
     * @Then /^my order total should be ("(?:\£|\$)\d+(?:\.\d+)?")$/
     */
    public function myOrderTotalShouldBe($total)
    {
        Assert::true($this->completePage->hasOrderTotal($total));
    }

    /**
     * @Then my order promotion total should be :promotionTotal
     */
    public function myOrderPromotionTotalShouldBe($promotionTotal)
    {
        Assert::true($this->completePage->hasPromotionTotal($promotionTotal));
    }

    /**
     * @Then :promotionName should be applied to my order
     */
    public function shouldBeAppliedToMyOrder($promotionName)
    {
        Assert::true($this->completePage->hasPromotion($promotionName));
    }

    /**
     * @Then :promotionName should be applied to my order shipping
     */
    public function shouldBeAppliedToMyOrderShipping($promotionName)
    {
        Assert::true($this->completePage->hasShippingPromotion($promotionName));
    }

    /**
     * @Given my tax total should be :taxTotal
     */
    public function myTaxTotalShouldBe($taxTotal)
    {
        Assert::true($this->completePage->hasTaxTotal($taxTotal));
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrdersShippingMethodShouldBe(ShippingMethodInterface $shippingMethod)
    {
        Assert::true($this->completePage->hasShippingMethod($shippingMethod));
    }

    /**
     * @Then my order's payment method should be :paymentMethod
     */
    public function myOrdersPaymentMethodShouldBe(PaymentMethodInterface $paymentMethod)
    {
        Assert::same($this->completePage->getPaymentMethodName(), $paymentMethod->getName());
    }

    /**
     * @Then the :product product should have unit price :price
     */
    public function theProductShouldHaveUnitPrice(ProductInterface $product, $price)
    {
        Assert::true($this->completePage->hasProductUnitPrice($product, $price));
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::true($this->completePage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product)
    {
        Assert::false($this->completePage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then my order's locale should be :localeName
     */
    public function myOrderLocaleShouldBe($localeName)
    {
        Assert::true($this->completePage->hasLocale($localeName));
    }

    /**
     * @Then I should see :provinceName in the shipping address
     */
    public function iShouldSeeInTheShippingAddress($provinceName)
    {
        Assert::true($this->completePage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName in the billing address
     */
    public function iShouldSeeInTheBillingAddress($provinceName)
    {
        Assert::true($this->completePage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then I should not see any information about payment method
     */
    public function iShouldNotSeeAnyInformationAboutPaymentMethod()
    {
        Assert::false($this->completePage->hasPaymentMethod());
    }

    /**
     * @Then /^(this promotion) should give "([^"]+)" discount$/
     */
    public function thisPromotionShouldGiveDiscount(PromotionInterface $promotion, $discount)
    {
        Assert::same($discount, $this->completePage->getShippingPromotionDiscount($promotion->getName()));
    }
}