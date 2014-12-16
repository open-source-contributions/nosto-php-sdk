<?php

/**
 * Product collection for historical data exports.
 * Supports only items implementing "NostoProductInterface".
 */
class NostoExportProductCollection extends NostoExportCollection
{
	/**
	 * @inheritdoc
	 */
	protected $validItemType = 'NostoProductInterface';

	/**
	 * @inheritdoc
	 */
	public function getJson()
	{
		$array = array();
		/** @var NostoProductInterface $item */
		foreach ($this->getArrayCopy() as $item) {
			$array[] = array(
				'url' => $item->getUrl(),
				'product_id' => $item->getProductId(),
				'name' => $item->getName(),
				'image_url' => $item->getImageUrl(),
				'price' => $item->getPrice(),
				'list_price' => $item->getListPrice(),
				'price_currency_code' => $item->getCurrencyCode(),
				'availability' => $item->getAvailability(),
				'tags' => $item->getTags(),
				'categories' => $item->getCategories(),
				'description' => $item->getDescription(),
				'brand' => $item->getBrand(),
				'date_published' => $item->getDatePublished(),
			);
		}
		return json_encode($array);
	}
}