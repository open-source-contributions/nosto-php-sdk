<?php
/**
 * Copyright (c) 2019, Nosto Solutions Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 * may be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2019 Nosto Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 *
 */

namespace Nosto\Mixins;

use Nosto\NostoException;

/**
 * Iframe mixin class for account administration iframe.
 */
trait CollectionJsonSerializerTrait
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $data = [];
        /* @var SkuInterface $item */
        do {
            $current = $this->current();
            if ($current instanceof \JsonSerializable) {
                $data[] = $current->jsonSerialize();
            } elseif (is_scalar($current)) {
                $data[] = $current;
            }
            $this->next();
        } while ($this->current() !== false);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function jsonDenormalize(array $data)
    {
        /* @var SkuInterface $item */
        $collection = new static();
        foreach ($data as $itemData) {
            // Scalar types
            if ($this->deserializeType() === null) {
                $collectionItem = $itemData;
            } else {
                $class = new \ReflectionClass($this->deserializeType());
                if ($class->implementsInterface('Nosto\Types\JsonDenormalizableInterface') === false) {
                    throw new NostoException(
                        sprintf(
                            'Cannot deserialize %s as it does\'nt implement JsonDeserializableInterface',
                            $class->getName()
                        )
                    );
                }
                /* @var JsonDenormalizableInterface $object */
                $object = $class->newInstance();
                $collectionItem = $object->jsonDenormalize($itemData);
            }
            $collection->append($collectionItem);
        }
        return $collection;
    }

    /**
     * Type where the items will be deserialized
     *
     * @return string|null null should be returned if the collection items are scalar types
     */
    abstract public function deserializeType();

    /**
     * Method appending items to collection
     * @param $item
     */
    abstract public function append($item);

    /**
     * Method for getting the current item from cursor
     */
    abstract public function current();

    /**
     * Method for moving cursor to a next element
     */
    abstract public function next();
}
