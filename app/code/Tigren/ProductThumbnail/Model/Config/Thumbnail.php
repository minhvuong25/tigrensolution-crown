<?php
/**
 * *
 *  * @author    Tigren Solutions <info@tigren.com>
 *  * @copyright Copyright (c) 2020 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 *  * @license   Open Software License ("OSL") v. 1.0.0
 *
 */

namespace Tigren\ProductThumbnail\Model\Config;

/**
 * Class Thumbnail
 * {@inheritDoc}
 */
class Thumbnail extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Tigren\ProductThumbnail\Model\Source\Thumbnail
     */
    protected $_thumbnail;

    /**
     * AllowedAreas constructor.
     * @param \Tigren\ProductThumbnail\Model\Source\Thumbnail $thumbnail
     */
    public function __construct(\Tigren\ProductThumbnail\Model\Source\Thumbnail $thumbnail)
    {
        $this->_thumbnail = $thumbnail;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = $this->_thumbnail->toOptionArray();
        }

        return $this->_options;
    }
}
