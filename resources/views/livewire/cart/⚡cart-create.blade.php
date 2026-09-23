<?php

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

new class extends Component
{
    public $products;
    public $order;
    public $orderItems = [];

    // ── HOLIDAY promo config (backend only, TIDAK bisa diubah dari FE) ─────────
    // TEST_MODE: true = paksa promo nyala (buat testing tengah malem dll)
    private const HOLIDAY_TEST_MODE = false;

    private const HOLIDAY_PRICE       = 40000;
    private const HOLIDAY_PRODUCT_IDS = [];

    public bool $holidayActive = false;

    public function mount()
    {
        $this->products = Product::whereNot("type","addon")->get();
        $this->order = Order::latest()->first();

        // ambil product_id saja supaya ringan
        $this->orderItems = OrderItem::where('order_id', $this->order->id)
            ->pluck('product_id')
            ->toArray();

        $this->checkHolidayActive();
    }

    private function checkHolidayActive(): void
    {
        if (self::HOLIDAY_TEST_MODE) {
            $this->holidayActive = true;
            return;
        }

        $now = Carbon::now();

        // Senin (1) s/d Kamis (4)
        $isValidDay = in_array((int) $now->dayOfWeekIso, [1, 2, 3, 4]);

        // Jam 11:00 - 15:00
        $isValidTime = $now->format('H:i') >= '11:00' && $now->format('H:i') <= '15:00';

        $this->holidayActive = $isValidDay && $isValidTime;
    }

    private function refreshOrderItems()
{
    $this->orderItems = OrderItem::where('order_id', $this->order->id)
        ->pluck('product_id')
        ->toArray();
}
    public function increaseProduct($productId){
        $type = $this->products->where('id', $productId)->first()->type;
        if ($productId == 1) {
                    # code...
                     OrderItem::create([
                'order_id'=>$this->order->id,
                'product_id'=>$productId,
                'printType'=>$type,
                    'printCount'=>2]);
                    }else{
                        OrderItem::create([
                'order_id'=>$this->order->id,
                'product_id'=>$productId,
                'printType'=>$type,
                        'printCount'=>1
                        ]);
                        }
        $this->refreshOrderItems();
    }
    public function decreaseProduct($productId){
        OrderItem::where('order_id',$this->order->id)
                ->where('product_id',$productId)->first()
                ->delete();
        $this->refreshOrderItems();
    }
    public function toggleProduct($productId)
    {
        $type = $this->products->where('id', $productId)->first()->type;
        if (in_array($productId, $this->orderItems)) {

            OrderItem::where('order_id',$this->order->id)
                ->where('product_id',$productId)
                ->delete();

            $this->orderItems = array_diff($this->orderItems, [$productId]);

        } else {
                if ($productId == 1) {
                    # code...
                     OrderItem::create([
                'order_id'=>$this->order->id,
                'product_id'=>$productId,
                'printType'=>$type,
                    'printCount'=>2]);
                    }else{
                        OrderItem::create([
                'order_id'=>$this->order->id,
                'product_id'=>$productId,
                'printType'=>$type,
                        'printCount'=>1
                        ]);
                        }
            $this->orderItems[] = $productId;
        }
    }

    };
    ?>

<style>
    /* ── Paksa badge selalu tampil, ga usah nunggu hover ── */
    .card__badge {
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        /*transform: none !important;*/
        z-index :9999999 !important;
    }

    /* ── Badge promo Holiday: eye catching + fun ── */
    .promo-holiday {
        background: linear-gradient(135deg, #ff5e8e, #ff9a3c) !important;
        border: 2px solid #fff;
        border-radius: 14px;
        padding: 6px 10px;
        box-shadow: 0 4px 14px rgba(255, 94, 142, 0.55);
        animation: promoPulse 1.6s ease-in-out infinite;
        line-height: 1.15;
    }

    .promo-holiday__tag {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.25);
        margin-bottom: 2px;
    }

    .promo-holiday__old {
        font-size: 0.85rem;
        color: #ffe1ea;
        text-decoration: line-through;
        opacity: 0.85;
    }

    .promo-holiday__new {
        font-size: 1.15rem;
        font-weight: 900;
        color: #fff;
    }

    @keyframes promoPulse {
        0%   { transform: scale(1);    box-shadow: 0 4px 14px rgba(255, 94, 142, 0.55); }
        50%  { transform: scale(1.05); box-shadow: 0 6px 20px rgba(255, 94, 142, 0.75); }
        100% { transform: scale(1);    box-shadow: 0 4px 14px rgba(255, 94, 142, 0.55); }
    }
</style>

<container class="d-flex flex-column text-center align-item-center justify-content-center " style="background-color:#fff4f9;">
    @if ($orderItems)
    <div class="d-flex justify-content-end" style="position:sticky;top:0;">
        <a href="/checkout/{{$order->id}}"  class="btn btn-primary mt-4 mx-4 fs-3 rounded-pill px-1 fw-bold border-4 " style="background-color: #ffb3ba;border-color: #d6eadf;width:10rem;z-index:100">NEXT <i class="fa-solid text-white fa-right-long"></i></a>
    </div>
    @else
    @endif

    <h1 class="mt-5 mb-2" style="font-weight: bold;">
        Kategori & List Harga
    </h1>
    <h3 class="fw-light mt-3">Temukan berbagai kategori produk lengkap dengan list harga terbaru</h3>


    <div class="container pricelist mt-3 d-flex flex-row" style="height: 45vw;">
            @foreach ($products as $product)
        @php
            $isHolidayProduct = $holidayActive && in_array($product->id, self::HOLIDAY_PRODUCT_IDS);
        @endphp
        <div class="card col-3 h-100 mx-2 rounded-3">
            <div class="card__shine"></div>
                <div class="card__glow"></div>
                <div class="card__content">
            @if(in_array($product->id,$orderItems))

            <div class="card__badge selected fs-4 text-white">
                <i class="bi bi-cart-check text-white fw-bold"></i>-{{ collect($orderItems)->filter(fn($id) => $id == $product->id)->count() }}
            </div>

            @elseif($isHolidayProduct)
            <div class="card__badge promo-holiday text-white">
                <div class="promo-holiday__tag">PROMO HOLIDAY</div>
                <div class="promo-holiday__old">Rp {{ number_format($product->price,0,',','.') }}</div>
                <div class="promo-holiday__new">Rp {{ number_format(40000,0,',','.') }}</div>
            </div>

            @else
            <div class="card__badge fs-5">
                 Rp {{ number_format($product->price,0,',','.') }}
            </div>
            @endif
                    <div style="--bg-color: #a78bfa;" class="card__image h-75"><img src="assets/img/{{ $product->img }}" style="object-fit: cover; height:80%" alt=""></div>
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
                    <div class="container">

                        <div class="row justify-content-between">
                            <div class="col-12">

                    <button wire:click="toggleProduct({{ $product->id }})" class="card__button active mb-2 rounded btn fw-bold fs-4 w-100"  style="background-color: #ffb3ba; color: #fff4f9;">
                        @if(in_array($product->id,$orderItems))
                        Cancel
                    @else

                        Pilih  <i class="ms-2 bi bi-bag-check-fill" style="color: #fff4f9;"></i>
                    @endif
                    </button>
                            </div>
                                @if(in_array($product->id,$orderItems))
                            <div class="col-6">
                        <button wire:click="decreaseProduct({{ $product->id }})" class="card__button active  rounded btn fw-bold fs-4 w-100"  style="background-color: #ffb3ba; color: #fff4f9;">-
                    </button>
                            </div>
                        @else
                    @endif
                        @if(in_array($product->id,$orderItems))
                            <div class="col-6">


                        <button wire:click="increaseProduct({{ $product->id }})" class="card__button active  rounded btn fw-bold fs-4 w-100"  style="background-color: #ffb3ba; color: #fff4f9;">+
                    </button>
                            </div>
                        @else
                    @endif
                        </div>
                    </div>
                </div>
                    </div>
        </div>
    @endforeach
</div>

</container>