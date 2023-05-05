<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Plugin\Review\Block\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Review\Block\Product\Review as ProductReview;

class Review
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Class constructor
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Add rating parameter to reviews URL
     *
     * @param ProductReview $review
     * @param string $result
     * @return string
     */
    public function afterGetProductReviewUrl(ProductReview $review, $result)
    {
        $url = $result;
        $rating = $this->request->getParam('rating');
        if ($rating && $rating >= 1 && $rating <= 5) {
            $url = $review->getUrl('review/product/listAjax', ['id' => $review->getProductId(), 'rating' => $rating]);
        }

        return $url;
    }
}
