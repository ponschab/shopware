<?php declare(strict_types=1);

namespace Shopware\DbalIndexing\Loader;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\Uuid;
use Shopware\ProductListingPrice\Struct\ProductListingPriceBasicCollection;
use Shopware\ProductListingPrice\Struct\ProductListingPriceBasicStruct;

class ListingPriceLoader
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function load(array $productUuids): ProductListingPriceBasicCollection
    {
        $query = $this->connection->createQueryBuilder();
        $query->addSelect([
            'product.uuid',
            'product_detail_price.customer_group_uuid as customer_group_uuid',
            'MIN(product_detail_price.price) as price',
        ]);

        $query->from('product');
        $query->innerJoin('product', 'product_detail', 'product_detail', 'product_detail.product_uuid = product.uuid AND product_detail.active = 1');
        $query->innerJoin('product', 'product_detail_price', 'product_detail_price', 'product_detail_price.product_detail_uuid = product_detail.uuid');
        $query->andWhere('product.uuid IN (:uuids)');
        $query->setParameter(':uuids', $productUuids, Connection::PARAM_STR_ARRAY);
        $query->addGroupBy('product.uuid');
        $query->addGroupBy('product_detail_price.customer_group_uuid');

        $rows = $query->execute()->fetchAll();
        $collection = new ProductListingPriceBasicCollection();
        foreach ($rows as $row) {
            $struct = new ProductListingPriceBasicStruct();
            $struct->setUuid(Uuid::uuid4()->toString());
            $struct->setProductUuid($row['uuid']);
            $struct->setPrice((float) $row['price']);
            $struct->setCustomerGroupUuid($row['customer_group_uuid']);
            $collection->add($struct);
        }

        return $collection;
    }
}
