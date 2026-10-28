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

    // ── B1G1 HALLOWEEN (product 14, type 106) ──────────────────────────────────
    // Satu pembelian B1G1 = 2 baris order_item (2 kode): Standar 2 Lembar (101)
    // + High Angle (104). Baris High Angle gratis (total dihitung di checkout).
    private const B1G1_PRODUCT = 14;

    /** @var array<int, array{printType:int, printCount:int}> */
    private const B1G1_LINES = [
        ['printType' => 101, 'printCount' => 2], // Standar 2 Lembar
        ['printType' => 104, 'printCount' => 1], // High Angle
    ];

    public function mount()
    {
        // Produk type 106 (B1G1) selalu paling depan di list.
        $this->products = Product::whereNot("type", "addon")
            ->get()
            ->sortByDesc(fn ($p) => $p->type === '106')
            ->values();
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
    // Bikin baris order_item buat 1 unit produk. B1G1 jadi 2 baris sekaligus.
    private function createProductLines(int $productId): void
    {
        if ($productId === self::B1G1_PRODUCT) {
            foreach (self::B1G1_LINES as $line) {
                OrderItem::create([
                    'order_id'   => $this->order->id,
                    'product_id' => $productId,
                    'printType'  => $line['printType'],
                    'printCount' => $line['printCount'],
                ]);
            }

            return;
        }

        $type = $this->products->where('id', $productId)->first()->type;

        OrderItem::create([
            'order_id'   => $this->order->id,
            'product_id' => $productId,
            'printType'  => $type,
            'printCount' => $productId == 1 ? 2 : 1,
        ]);
    }

    // Hapus 1 unit produk. B1G1 kehapus sepasang (2 baris) sekaligus.
    private function removeProductUnit(int $productId): void
    {
        $perUnit = $productId === self::B1G1_PRODUCT ? count(self::B1G1_LINES) : 1;

        $ids = OrderItem::where('order_id', $this->order->id)
            ->where('product_id', $productId)
            ->orderByDesc('id')
            ->limit($perUnit)
            ->pluck('id');

        OrderItem::whereIn('id', $ids)->delete();
    }

    public function increaseProduct($productId)
    {
        $this->createProductLines((int) $productId);
        $this->refreshOrderItems();
    }

    public function decreaseProduct($productId)
    {
        $this->removeProductUnit((int) $productId);
        $this->refreshOrderItems();
    }

    public function toggleProduct($productId)
    {
        $productId = (int) $productId;

        if (in_array($productId, $this->orderItems)) {
            // Cancel → hapus semua baris produk ini (semua pasangan untuk B1G1).
            OrderItem::where('order_id', $this->order->id)
                ->where('product_id', $productId)
                ->delete();

            $this->orderItems = array_values(array_diff($this->orderItems, [$productId]));

            return;
        }

        $this->createProductLines($productId);
        $this->refreshOrderItems();
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

    /* ── Badge promo B1G1 Halloween: spooky orange/purple ── */
    .promo-b1g1 {
        background: linear-gradient(135deg, #ff7518, #6a0dad) !important;
        border: 2px solid #fff;
        border-radius: 14px;
        padding: 6px 10px;
        box-shadow: 0 4px 14px rgba(255, 117, 24, 0.55);
        animation: promoPulse 1.6s ease-in-out infinite;
        line-height: 1.15;
    }

    .promo-b1g1__tag {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.35);
        margin-bottom: 2px;
    }

    .promo-b1g1__old {
        font-size: 0.85rem;
        color: #ffe1c4;
        text-decoration: line-through;
        opacity: 0.9;
    }

    .promo-b1g1__new {
        font-size: 1.15rem;
        font-weight: 900;
        color: #fff;
    }
    /* ── Pemanis background: soft pastel blobs, nyebar ke seluruh halaman ── */
    body {
        background-color: #fff4f9;
        background-image:
            radial-gradient(45vw 45vw at 12% 8%,  rgba(255, 154, 60, 0.10), transparent 60%),
            radial-gradient(42vw 42vw at 88% 16%, rgba(255, 110, 199, 0.12), transparent 60%),
            radial-gradient(48vw 48vw at 80% 92%, rgba(106, 13, 173, 0.07), transparent 60%),
            radial-gradient(40vw 40vw at 18% 94%, rgba(255, 179, 186, 0.16), transparent 60%);
        background-attachment: fixed;
        background-repeat: no-repeat;
    }

    .cart-sugar-bg {
        background: transparent;
        min-height: 100vh;
    }
</style>

<container class="cart-sugar-bg d-flex flex-column text-center align-item-center justify-content-center " style="background:transparent;">
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

            @php
                $unitCount = collect($orderItems)->filter(fn($id) => $id == $product->id)->count();
                // B1G1 nyimpen 2 baris per unit → tampilin jumlah unit sebenarnya.
                if ((int) $product->id === 14) { $unitCount = intdiv($unitCount, 2); }
            @endphp
            <div class="card__badge selected fs-4 text-white">
                <i class="bi bi-cart-check text-white fw-bold"></i>-{{ $unitCount }}
            </div>

            @elseif($isHolidayProduct)
            <div class="card__badge promo-holiday text-white">
                <div class="promo-holiday__tag">PROMO HOLIDAY</div>
                <div class="promo-holiday__old">Rp {{ number_format($product->price,0,',','.') }}</div>
                <div class="promo-holiday__new">Rp {{ number_format(40000,0,',','.') }}</div>
            </div>

            @elseif((int) $product->id === 14)
            @php
                // Harga "normal" = Standar 2 Lembar (1) + High Angle (4), dicoret.
                $b1g1Old = (optional($products->firstWhere('id', 1))->price ?? 0)
                         + (optional($products->firstWhere('id', 4))->price ?? 0);
            @endphp
            <div class="card__badge promo-b1g1 text-white">
                <div class="promo-b1g1__tag">🎃 B1G1 HALLOWEEN</div>
                <div class="promo-b1g1__old">Rp {{ number_format($b1g1Old, 0, ',', '.') }}</div>
                <div class="promo-b1g1__new">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
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