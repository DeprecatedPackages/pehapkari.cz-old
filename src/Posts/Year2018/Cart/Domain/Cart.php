<?php declare(strict_types=1);

namespace Pehapkari\Website\Posts\Year2018\Cart\Domain;

class Cart
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Item[]
     */
    private $items = [];

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function add(string $productId, Price $unitPrice, int $amount = 1): void
    {
        try {
            $item = $this->find($productId);
            $item->add($amount);
        } catch (ProductNotInCartException $e) {
            $this->items[] = new Item($productId, $unitPrice, $amount);
        }
    }

    /**
     * @throws ProductNotInCartException
     */
    public function remove(string $productId): void
    {
        $key = $this->findKey($productId);
        unset($this->items[$key]);
    }

    /**
     * @throws ProductNotInCartException
     */
    public function changeAmount(string $productId, int $amount): void
    {
        $item = $this->find($productId);
        $item->changeAmount($amount);
    }

    public function calculate(): CartDetail
    {
        $detailItems = array_map(function (Item $item): ItemDetail {
            return $item->toDetail();
        }, $this->items);

        $prices = array_map(function (Item $item): Price {
            return $item->calculatePrice();
        }, $this->items);

        $totalPrice = Price::sum($prices);

        return new CartDetail(array_values($detailItems), $totalPrice);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws ProductNotInCartException
     */
    private function find(string $productId): Item
    {
        foreach ($this->items as $item) {
            if ($item->getProductId() === $productId) {
                return $item;
            }
        }

        throw new ProductNotInCartException();
    }

    /**
     * @throws ProductNotInCartException
     */
    private function findKey(string $productId): int
    {
        foreach ($this->items as $key => $item) {
            if ($item->getProductId() === $productId) {
                return $key;
            }
        }

        throw new ProductNotInCartException();
    }
}
