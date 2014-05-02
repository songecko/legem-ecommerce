<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * Default assortment products to play with Sylius.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadProductsData extends DataFixture
{
    /**
     * Total variants created.
     *
     * @var integer
     */
    private $totalVariants;

    /**
     * SKU collection.
     *
     * @var array
     */
    private $skus;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->skus = array();
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->productAttributeClass = $this->container->getParameter('sylius.model.product_attribute.class');
        
        // T-Shirts...
        for ($i = 1; $i <= 10; $i++) {
            switch (rand(0, 3)) {
                case 0:
                    $manager->persist($this->createRing($i, 'Solitaire'));
                break;

                case 1:
                    $manager->persist($this->createRing($i, 'Side Stones'));
                break;

                case 2:
                    $manager->persist($this->createRing($i, 'Unique Design'));
                break;

                case 3:
                    $manager->persist($this->createRing($i, 'Halo'));
                break;
            }

            if (0 === $i % 20) {
                $manager->flush();
            }
        }

        $manager->flush();

        $this->defineTotalVariants();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 6;
    }

    /**
     * Creates t-shirt product.
     *
     * @param integer $i
     */
    protected function createRing($i, $taxon)
    {
        $product = $this->createProduct();

        $product->setTaxCategory($this->getTaxCategory('Taxable goods'));
        $product->setName(sprintf($taxon.' "%s"', $this->faker->word));
        $product->setDescription($this->faker->paragraph);
        $product->setShortDescription($this->faker->sentence);
        $product->setVariantSelectionMethod(Product::VARIANT_SELECTION_MATCH);

        $this->addMasterVariant($product);

        $this->setTaxons($product, array($taxon));

        // T-Shirt brand.
        /*$randomBrand = $this->faker->randomElement(array('Nike', 'Adidas', 'Puma', 'Potato'));
        $this->addAttribute($product, 'T-Shirt brand', $randomBrand);

        // T-Shirt collection.
        $randomCollection = sprintf('Symfony2 %s %s', $this->faker->randomElement(array('Summer', 'Winter', 'Spring', 'Autumn')), rand(1995, 2012));
        $this->addAttribute($product, 'T-Shirt collection', $randomCollection);

        // T-Shirt material.
        $randomMaterial = $this->faker->randomElement(array('Polyester', 'Wool', 'Polyester 10% / Wool 90%', 'Potato 100%'));
        $this->addAttribute($product, 'T-Shirt material', $randomMaterial);*/

        //$product->addOption($this->getReference('Sylius.Option.shape'));
        $product->addOption($this->getReference('Sylius.Option.metal'));
        $product->addOption($this->getReference('Sylius.Option.size'));
        $product->addOption($this->getReference('Sylius.Option.carat'));
      	$product->addOption($this->getReference('Sylius.Option.color'));
        $product->addOption($this->getReference('Sylius.Option.cut'));
        $product->addOption($this->getReference('Sylius.Option.clarity'));

        $this->generateVariants($product);

        $this->setReference('Sylius.Product-'.$i, $product);

        return $product;
    }

    /**
     * Generates all possible variants with random prices.
     *
     * @param ProductInterface $product
     */
    protected function generateVariants(ProductInterface $product)
    {
        $this
            ->getVariantGenerator()
            ->generate($product)
        ;

        foreach ($product->getVariants() as $variant) {
            $variant->setAvailableOn($this->faker->dateTimeThisYear);
            $variant->setPrice($this->faker->randomNumber(4));
            $variant->setSku($this->getUniqueSku());
            $variant->setOnHand($this->faker->randomNumber(1));

            $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);
            $this->totalVariants++;
        }
    }

    /**
     * Adds master variant to product.
     *
     * @param ProductInterface $product
     * @param string           $sku
     */
    protected function addMasterVariant(ProductInterface $product, $sku = null)
    {
        if (null === $sku) {
            $sku = $this->getUniqueSku();
        }

        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($this->faker->randomNumber(4));
        $variant->setSku($sku);
        $variant->setAvailableOn($this->faker->dateTimeThisYear);
        $variant->setOnHand($this->faker->randomNumber(1));

        $productName = explode(' "', $product->getName());
        $image = clone $this->getReference(
            'Sylius.Image.'.strtolower($productName[0])
        );
        $variant->addImage($image);

        $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);
        $this->totalVariants++;

        $product->setMasterVariant($variant);
    }

    /**
     * Adds attribute to product with given value.
     *
     * @param ProductInterface $product
     * @param string           $name
     * @param string           $value
     */
    private function addAttribute(ProductInterface $product, $name, $value)
    {
        $attribute = $this->getProductAttributeValueRepository()->createNew();
        $attribute->setAttribute($this->getReference('Sylius.Attribute.'.$name));
        $attribute->setProduct($product);
        $attribute->setValue($value);

        $product->addAttribute($attribute);
    }

    /**
     * Add product to given taxons.
     *
     * @param ProductInterface $product
     * @param array            $taxonNames
     */
    protected function setTaxons(ProductInterface $product, array $taxonNames)
    {
        $taxons = new ArrayCollection();

        foreach ($taxonNames as $taxonName) {
            $taxons->add($this->getReference('Sylius.Taxon.'.$taxonName));
        }

        $product->setTaxons($taxons);
    }

    /**
     * Get tax category by name.
     *
     * @param string $name
     *
     * @return TaxCategoryInterface
     */
    protected function getTaxCategory($name)
    {
        return $this->getReference('Sylius.TaxCategory.'.ucfirst($name));
    }

    /**
     * Get unique SKU.
     *
     * @param integer $length
     *
     * @return string
     */
    protected function getUniqueSku($length = 5)
    {
        do {
            $sku = $this->faker->randomNumber($length);
        } while (in_array($sku, $this->skus));

        $this->skus[] = $sku;

        return $sku;
    }

    /**
     * Get unique ISBN number.
     *
     * @return string
     */
    protected function getUniqueISBN()
    {
        return $this->getUniqueSku(13);
    }

    /**
     * Create new product instance.
     *
     * @return ProductInterface
     */
    protected function createProduct()
    {
        return $this
            ->getProductRepository()
            ->createNew()
        ;
    }

    /**
     * Define constant with number of total variants created.
     */
    protected function defineTotalVariants()
    {
        define('SYLIUS_FIXTURES_TOTAL_VARIANTS', $this->totalVariants);
    }
}
