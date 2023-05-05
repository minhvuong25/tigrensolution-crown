<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Model\Source;

/**
 * Class Thumbnail
 * {@inheritDoc}
 */
class Thumbnail implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Countries
     *
     * @var \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory
     */
    protected $_thumbnailCollectionFactory;

    /**
     * Options array
     *
     * @var array
     */
    protected $options;

    /**
     * @param \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory
     */
    public function __construct(
        \Tigren\ProductThumbnail\Model\ResourceModel\Thumbnail\CollectionFactory $thumbnailCollectionFactory
    ) {
        $this->_thumbnailCollectionFactory = $thumbnailCollectionFactory;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $thumbnailCollection = $this->_thumbnailCollectionFactory->create()
                ->addFilter('status', '1', 'eq');
            $options = [];
            $i = 0;

            foreach ($thumbnailCollection as $thumbnail) {
                $options[$i]['label'] = $thumbnail->getTitle();
                $options[$i]['value'] = $thumbnail->getProductthumbnailId();
                $i++;
            }
            $this->options = $options;
        }

        return $this->options;
    }
}
