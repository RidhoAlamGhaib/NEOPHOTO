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

    public function mount()
    {
        $this->products = product::all();
    }

    public function addItem($productId): void
    {
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $productId,
        ]);
        return redirect('/checkout');

    }
};
?>

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
                    <button class="card__button rounded btn w-100"   wire:click="addItem({{ $product->id }})" style="background-color: #ffb3ba; color: #fff4f9;">
                        Pilih <i class="ms-2 bi bi-bag-check-fill" style="color: #fff4f9;"></i>
                    </button>
                    </div>
                </div>
        </div>
    @endforeach