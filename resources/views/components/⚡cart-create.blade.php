<?php
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\product;
use App\Models\Order;
use App\Models\OrderItemAddon;
use App\Models\OrderItem;
use App\Http\Requests\StoreproductRequest;
use App\Http\Requests\UpdateproductRequest;
use App\Http\Controllers\StorecustomerRequest;
use App\Http\Controllers\UpdatecustomerRequest;


new class extends Component
{
    public $products;
    public $order;

    public function mount()
    {
        $this->products = product::all();
        $this->order = Order::latest()->first();
    }
        
        public function addItem($order,$productId): void
    {
        $orderChk = OrderItem::where("order_id",$order)->where("product_id",$productId)->first();
        if ($orderChk) {
            $orderChk->delete();
            }else{
                $orderItem = OrderItem::create([
                    'order_id' => $order,
                    'product_id' => $productId,
                    ]);}
            }
};
?>
<container class="d-flex flex-column text-center align-item-center justify-content-center " style="background-color:#fff4f9;">

    <h1 class="mt-5 mb-2" style="font-weight: bold;">
        Kategori & List Harga
    </h1>
    <h3 class="fw-light mt-3">Temukan berbagai kategori produk lengkap dengan list harga terbaru</h3>
    <div class="container pricelist mt-3 d-flex flex-row" style="height: 45vw;">
            @foreach ($products as $product)
        <div class="card col-3 h-100 mx-2 rounded-3">
            <div class="card__shine"></div>
                <div class="card__glow"></div>
                <div class="card__content">
                    <div class="card__badge fs-5">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    <div style="--bg-color: #a78bfa;object-fit: cover;" class="card__image h-75"><img src="assets/img/{{ $product->img }}" alt=""></div>
                    <div class="card__text">
                    <p class="card__title fs-3">{{ $product->name }}</p>
                    <p class="card__description fs-4">{{ $product->description }}</p>
                    </div>
                    <div class="card__footer ">
                    {{-- <div class="card__price dropdown">
                        <div class=" dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            What I get
                        </div>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div> --}}
                    <button class="card__button rounded btn w-100" id="add-product-{{$product->id}}" onclick="add($this,'product-{{ $product->id }}')"   style="background-color: #ffb3ba; color: #fff4f9;">
                        Pilih <i class="ms-2 bi bi-bag-check-fill" style="color: #fff4f9;"></i>
                    </button>
                    {{-- <button class="card__button rounded btn w-100 d-none"id="cancel-product-{{$product->id}}" onclick="cancel('product-{{ $product->id }}')"  wire:click="addItem({{$order->id}},{{ $product->id }})" style="background-color: #ffb3ba; color: #fff4f9;">
                        Pilih <i class="ms-2 bi bi-bag-check-fill" style="color: #fff4f9;"></i>
                    </button> --}}
                    </div>
                </div>
        </div>
    @endforeach
    </div>
</container>
