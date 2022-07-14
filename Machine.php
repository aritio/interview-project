<?php 
/*
alur vending machine => masukan uang, pilih productnya
*/
//products
class Product {
    public $item, 
           $price,
           $stock;

    public function __construct( $item, $price, $stock) {
        $this->item = $item;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getInfoProduct() {
        return "$this->item -  Rp. $this->price - Qty: $this->stock";
    }

    public function getItemPrice(){
        return $this->price;
    }
}

class VendingMachine {
    public $qty, $money;
    protected $out_item = array();
    protected $list_products = array();
    protected $change_moneys = array();

    public function __construct() {
    
    }

    public function addProduct(Product $product ) {
        $this->list_products[] = $product;
    }

    public function buy($money, $qty) {
        $all_money = $this->calculateTotalMoney($money);
     
        $total_charge = $this->calculateCharge($qty, $all_money[0]);

        $str = '';
        $i = 1;
        foreach($qty->product as $key => $val ) {
            $str .= $i++ . ". ";
            $str .= $key .' - qty:'.$val;

            $str .= "<br>";
        }

        echo '<br><br><b>Uang Masuk: </b>' . array_sum($all_money). '<br>
        List Belanja <br><b>'. $str .'</b><br> <hr>Tereksekusi </hr>'.
        ($this->out_item ? implode(", ",$this->out_item) : '-').' <br> <b>Uang kembalian: '.($all_money[1] + $total_charge) . '<br>
        dan deskripsi pecahan ditolak '.implode(", ",$this->change_moneys).'</b>
        <br>' ;
    }

    function calculateTotalMoney(object $customer): array
    {
        $total_money = 0;
        $total_money_not_valid = 0;

        $rule = array(2000, 5000, 10000, 20000, 50000);
        foreach ($customer->wallet as $coin) {
            if (in_array($coin, $rule)) {
                $total_money += $coin;
            } else {
                $this->change_moneys[] = $coin;
                $total_money_not_valid += $coin;
            }
        }
        
        return [$total_money, $total_money_not_valid];
    }

    public function lists() {
        $str = 'List Product in Machine : <br>';
        $i = 1;
        foreach( $this->list_products as $p ) {
            $str .= $i++ . ". ";
            $str .= "{$p->getInfoProduct()}";
            $str .= "<br>";
        }

        return $str;
    }

    function searchItem($total, $id, $qty) {
        $total_charge = 0;
        foreach ($this->list_products as $key => $val) {
            if ($val->item === $id) {
                if(($total >= (int)$val->price * $qty) && ($qty <= (int)$val->stock)) {
                    $val->stock = $val->stock - $qty;
                    $this->out_item[] = $val->item .' x '.$qty;
                    $total_charge += $val->price;
                }
                    
            }
        }
        return $total_charge;
    }

    function calculateCharge(object $customer, $total): int
    {   
        foreach ($customer->product as $key => $val) {
            $div = $this->searchItem($total,$key, $val);
            $total -= $div;
        }  

        return $total;
    }
}

$product1 = new Product("Biskuit", 6000, 10);
$product2 = new Product("Chips", 8000, 15);
$product3 = new Product("Oreo", 10000, 20);
$product4 = new Product("Tango", 12000, 5);
$product5 = new Product("Cokelat", 15000, 4);

$customerWallet = new stdClass();
$customerWallet->wallet = [
    1000, 5000, 2000, 4000
];
$customerProduct = new stdClass();
$customerProduct->product = [
    'Biskuit' => 1, 
    'Cokelat' => 2
];

$vm = new VendingMachine();

$vm->addProduct($product1);
$vm->addProduct($product2);
$vm->addProduct($product3);
$vm->addProduct($product4);
$vm->addProduct($product5);

echo "<hr>before:<br><hr>";
echo $vm->lists();
$vm->buy($customerWallet, $customerProduct);
echo "<hr>after:<br><hr>";
echo $vm->lists();




