<?php

namespace app\library;

class Cart
{

  /**
   * adiciona produtos ao carrinho de compra
   **/
  public function add(Product $product)
  {

    if (!isset($_SESSION['cart'])) {
      $_SESSION['cart'] = [];
    }
    $inCart = false;
    $this->setTotal($product);
    if (count($this->getCart()) > 0) {
      foreach ($this->getCart() as $productInCart) {
        if ($productInCart->getId() === $product->getId()) {
          $quantity = $productInCart->getQuantity() + $product->getQuantity();
          $productInCart->setQuantity($quantity);
          $inCart = true;
          break;
        }
      }
    }

    if (!$inCart) {
      $this->setProductsInCart($product);
    }
  }
  /**
   * define todos os produtos no carrinho de compras
   **/
  private function setProductsInCart($product)
  {
    if (!isset($_SESSION['cart']['products'])) {
      $_SESSION['cart']['products'] = [];
    }
    $_SESSION['cart']['products'][]  = $product;
  }

  /**
   * define valor total  dos produtos no carrinho de compras
   **/
  private function setTotal(Product $product)
  {
    if (!isset($_SESSION['cart']['total'])) {
      $_SESSION['cart']['total'] = 0;
    }

    $_SESSION['cart']['total'] += $product->getPrice() * $product->getQuantity();
  }

  /**
   * remove produtos ao carrinho de compra
   **/
  public function remove(int $id)
  {
    if (isset($_SESSION['cart']['products'])) {
      foreach ($this->getCart() as $index => $product) {
        if ($product->getId() === $id) {
          unset($_SESSION['cart']['products'][$index]);
          $_SESSION['cart']['total'] -= $product->getPrice() * $product->getQuantity();
        }
      }
    }
  }

  /**
   * retorna dados do carrinho de compra
   **/
  public function getCart()
  {
    return $_SESSION['cart']['products'] ?? [];
  }

  /**
   * retorna o valor total do carrinho de compras
   **/
  public function getTotal()
  {
    return $_SESSION['cart']['total'] ?? 0;
  }
}
